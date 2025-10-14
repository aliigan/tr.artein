<?php
/**
 * BuildTech CMS - Ana Sayfa
 * Dinamik içerik yönetimi ile ana sayfa
 */

require_once '../shared/config/frontend_config.php';
define('FRONTEND_ACCESS', true);

// Site ayarlarını al
$settings = getSiteSettings();
$sliders = getActiveSliders();
$aboutContent = getAboutContent();
$services = getActiveServices();
$projects = getActiveProjects(6, true); // Son 6 öne çıkan proje
$socialLinks = getSocialMediaLinks();

// Debug kaldırıldı - veriler çalışıyor

// Meta etiketleri
$pageTitle = $settings['site_title'] ?? 'Arte In Engineering';
$pageDescription = $settings['site_description'] ?? 'Modern İnşaat Çözümleri';

// Google reCAPTCHA Site Key (Bu değeri Google reCAPTCHA konsolundan alın)
define('RECAPTCHA_SITE_KEY', '6Lepu-grAAAAAAMBBN9oxtEdXLtM00kDbDT3Yxvt');
define('RECAPTCHA_SECRET_KEY', '6Lepu-grAAAAAIwUMbcGSV_KugykQ3yR2YAKb1wb');

// İletişim formu işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_form'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
    
    $errors = [];
    
    if (empty($name)) $errors[] = 'İsim alanı zorunludur.';
    if (empty($email)) $errors[] = 'E-posta alanı zorunludur.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Geçerli bir e-posta adresi girin.';
    if (empty($message)) $errors[] = 'Mesaj alanı zorunludur.';
    if (!empty($phone)) {
        $cleanPhone = preg_replace('/[\s\-\(\)]+/', '', $phone);
        if (!preg_match('/^\+?[0-9]{10,15}$/', $cleanPhone)) {
            $errors[] = 'Lütfen geçerli bir telefon numarası girin.';
        }
    }
    if (empty($recaptcha_response)) $errors[] = 'Lütfen reCAPTCHA doğrulamasını tamamlayın.';
    
    // Google reCAPTCHA doğrulama
    if (!empty($recaptcha_response)) {
        $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
        $recaptcha_data = [
            'secret' => RECAPTCHA_SECRET_KEY,
            'response' => $recaptcha_response,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ];
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($recaptcha_data)
            ]
        ];
        $context = stream_context_create($options);
        $result = file_get_contents($recaptcha_url, false, $context);
        $recaptcha_result = json_decode($result, true);
        if (!$recaptcha_result['success']) {
            $errors[] = 'reCAPTCHA doğrulaması başarısız. Lütfen tekrar deneyin.';
        }
    }
    
    if (empty($errors)) {
        if (saveContactMessage($name, $email, $phone, $subject, $message)) {
            $success_message = 'Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.';
            $_SESSION['contact_success'] = $success_message;
            // Form verilerini temizle
            unset($_SESSION['form_data']);
            // AJAX isteği için JSON yanıt
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => $success_message]);
                exit;
            }
        } else {
            $error_message = 'Mesaj gönderilirken bir hata oluştu. Lütfen tekrar deneyin.';
            if (isset($database)) {
                $dbErr = method_exists($database, 'getLastErrorMessage') ? $database->getLastErrorMessage() : '';
                if (!empty($dbErr)) {
                    $error_message .= ' [' . $dbErr . ']';
                }
            }
            // Form verilerini sakla
            $_SESSION['form_data'] = [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'subject' => $subject,
                'message' => $message
            ];
            // AJAX isteği için JSON yanıt
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $error_message]);
                exit;
            }
        }
    } else {
        $error_message = implode('<br>', $errors);
        $_SESSION['form_data'] = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'subject' => $subject,
            'message' => $message
        ];
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $error_message]);
            exit;
        }
    }
}

// Form verilerini al (hata durumunda)
$form_data = $_SESSION['form_data'] ?? [];

// Header'ı include et
include 'includes/header.php';
?>

    <style>
        /* Index.php page specific styles */
        
        /* Section Backgrounds - Brand Guide Renkleri */
        .about-section {
            background-color: var(--artein-white);
        }
        .services-section {
            background-color: var(--artein-light);
        }
        .projects-section {
            background-color: var(--artein-white);
        }
        .contact-section {
            background-color: var(--artein-light);
        }
        
        /* Footer specific styles */
        .footer {
            background-color: var(--artein-dark) !important;
            padding: 3rem 0;
        }
        .footer-logo {
            margin-bottom: 1.5rem;
        }
        .footer-logo img {
            height: 60px;
        }
        .footer-contact {
            margin-bottom: 2rem;
        }
        .footer-contact p {
            margin-bottom: 0.5rem;
            color: #fff;
        }
        .footer-contact i {
            margin-right: 0.5rem;
            color: #fff;
        }
        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        .social-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            color: #fff;
            font-size: 1.2rem;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            background: var(--artein-dark);
            border: 2px solid #fff;
        }
        .social-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        .social-link.facebook {
            background: var(--artein-dark);
            border: 2px solid #fff;
        }
        .social-link.whatsapp {
            background: var(--artein-dark);
            border: 2px solid #fff;
        }
        .social-link.instagram {
            background: var(--artein-dark);
            border: 2px solid #fff;
        }
        .social-link:hover {
            transform: translateY(-3px) scale(1.1);
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
            background: #fff;
            color: var(--artein-dark);
        }
        .social-link:hover::before {
            background: rgba(255, 255, 255, 0.1);
        }
    </style>

    <!-- Hero Section / Slider -->
    <section id="home" class="hero-section">
        <?php if (!empty($sliders)): ?>
        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <?php foreach ($sliders as $index => $slider): ?>
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?= $index ?>" 
                            <?= $index === 0 ? 'class="active"' : '' ?>></button>
                <?php endforeach; ?>
            </div>
            <div class="carousel-inner">
                <?php foreach ($sliders as $index => $slider): ?>
                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>" 
                         style="background-image: url('../../<?= escape($slider['background_image']) ?>')">
                        <div class="carousel-caption">
                            <h1 class="display-text text-white"><?= escape($slider['title']) ?></h1>
                            <?php if ($slider['subtitle']): ?>
                                <p class="lead"><?= escape($slider['subtitle']) ?></p>
                            <?php endif; ?>
                            <?php if ($slider['button_text'] && $slider['button_link']): ?>
                                <a href="<?= escape($slider['button_link']) ?>" class="btn-artein-primary btn-lg mt-3">
                                    <?= escape($slider['button_text']) ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (count($sliders) > 1): ?>
                <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                    <span class="visually-hidden">Önceki</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                    <span class="visually-hidden">Sonraki</span>
                </button>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </section>

    <!-- About Section -->
    <section id="about" class="about-section py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="section-title text-center">
                        <?= escape($aboutContent['title'] ?? getSetting('about_title', 'Hakkımızda')) ?>
                    </h2>
                    <?php if (isset($aboutContent['subtitle']) && $aboutContent['subtitle']): ?>
                        <p class="lead"><?= escape($aboutContent['subtitle']) ?></p>
                    <?php endif; ?>
                    <?php if (isset($aboutContent['content']) && $aboutContent['content']): ?>
                        <div><?= $aboutContent['content'] ?></div>
                    <?php else: ?>
                        <p class="lead"><?= escape(getSetting('about_subtitle', '"Arte in" adında mühendislik, inşaat ve taahhüt işleri yapan bir şirketiz.')) ?></p>
                        <p>Adından aldığı ilham ile sanatı ve mühendislik arasındaki ilişkiyi gösteren bir misyonu bulunmaktadır. Kaliteyi, sürdürülebilirliği, yeniliği, akılcılığı ve güveni hedeflemektedir.</p>
                    <?php endif; ?>
                </div>
                <div class="col-lg-6">
                    <img src="../../<?= escape($aboutContent['image'] ?? 'assets/images/about.jpg') ?>" 
                         alt="<?= escape($aboutContent['title'] ?? 'Hakkımızda') ?>" 
                         class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <?php if (!empty($services)): ?>
    <section id="services" class="services-section py-5">
        <div class="container">
            <h2 class="section-title">Hizmetlerimiz</h2>
            
            
            <div class="row g-4">
                <?php foreach ($services as $service): ?>
                    <div class="col-lg-3 col-md-6">
                    <div class="service-card artein-card">
                            <?php if ($service['icon']): ?>
                                <div class="service-icon">
                                    <i class="<?= escape($service['icon']) ?>"></i>
                                </div>
                            <?php else: ?>
                                <div class="service-icon">
                                    <i class="fas fa-cog"></i>
                                </div>
                            <?php endif; ?>
                        <h4 style="color: var(--artein-black);"><?= escape($service['title']) ?></h4>
                            <?php if ($service['description']): ?>
                            <p style="color: var(--artein-gray-600);"><?= escape($service['description']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Projects Section -->
    <?php if (!empty($projects)): ?>
    <section id="projects" class="projects-section py-5">
        <div class="container">
            <h2 class="section-title">Projelerimiz</h2>
            <div class="row g-4">
                <?php foreach ($projects as $project): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="project-card">
                            <?php if ($project['featured_image']): ?>
                                <img src="../../<?= escape($project['featured_image']) ?>" 
                                     alt="<?= escape($project['title']) ?>">
                            <?php endif; ?>
                            <div class="project-card-body">
                                <h5><?= escape($project['title']) ?></h5>
                                <?php if ($project['description']): ?>
                                    <p class="text-muted"><?= escape(substr($project['description'], 0, 100)) ?>...</p>
                                <?php endif; ?>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <?= escape($project['category']) ?>
                                        <?php if ($project['location']): ?>
                                            • <?= escape($project['location']) ?>
                                        <?php endif; ?>
                                    </small>
                                    <a href="proje-detay.php?slug=<?= escape($project['slug']) ?>" 
                                       class="btn-artein-secondary btn-sm">Detay</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="projeler.php" class="btn-artein-primary btn-lg">Tüm Projeleri Görüntüle</a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Contact Section -->
    <section id="contact" class="contact-section py-5">
        <div class="container">
            <h2 class="section-title">İletişim</h2>
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="contact-form">
                        <?php if (isset($success_message)): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i><?= $success_message ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i><?= $error_message ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" novalidate>
                            <input type="hidden" name="contact_form" value="1">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">İsim Soyisim *</label>
                                        <input type="text" class="form-control" name="name" minlength="2" maxlength="100" 
                                               pattern="[a-zA-ZçğıöşüÇĞIİÖŞÜ\s]+" value="<?= escape($form_data['name'] ?? '') ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">E-posta *</label>
                                        <input type="email" class="form-control" name="email" 
                                               value="<?= escape($form_data['email'] ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Telefon</label>
                                        <div class="input-group phone-input-group">
                                            <select class="form-select phone-cc" name="phone_cc" aria-label="Ülke Kodu">
                                                <option value="+90" selected data-flag="tr" data-country="TR">🇹🇷 +90 TR</option>
                                                <option value="+1" data-flag="us" data-country="US">🇺🇸 +1 US</option>
                                                <option value="+44" data-flag="gb" data-country="GB">🇬🇧 +44 GB</option>
                                                <option value="+49" data-flag="de" data-country="DE">🇩🇪 +49 DE</option>
                                                <option value="+33" data-flag="fr" data-country="FR">🇫🇷 +33 FR</option>
                                                <option value="+971" data-flag="ae" data-country="AE">🇦🇪 +971 AE</option>
                                                <option value="+7" data-flag="ru" data-country="RU">🇷🇺 +7 RU</option>
                                                <option value="+86" data-flag="cn" data-country="CN">🇨🇳 +86 CN</option>
                                                <option value="+81" data-flag="jp" data-country="JP">🇯🇵 +81 JP</option>
                                                <option value="+91" data-flag="in" data-country="IN">🇮🇳 +91 IN</option>
                                            </select>
                                            <input type="tel" class="form-control phone-local" name="phone_local" placeholder="555 123 45 67"
                                                   maxlength="20" pattern="[0-9\s\-\(\)]+" inputmode="numeric" value="">
                                        </div>
                                        <input type="hidden" name="phone" value="<?= escape($form_data['phone'] ?? '') ?>">
                                        <small class="text-muted">Ülke kodu seçin, numarayı boşluksuz veya boşluklu yazabilirsiniz.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Konu</label>
                                        <input type="text" class="form-control" name="subject" maxlength="150" 
                                               pattern="[a-zA-ZçğıöşüÇĞIİÖŞÜ0-9\s\-\.\,\!\?]+" value="<?= escape($form_data['subject'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mesaj *</label>
                                <textarea class="form-control" name="message" rows="5" minlength="10" maxlength="2000" 
                                          pattern="[a-zA-ZçğıöşüÇĞIİÖŞÜ0-9\s\-\.\,\!\?\n\r]+" required><?= escape($form_data['message'] ?? '') ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Güvenlik Doğrulaması *</label>
                                <div class="g-recaptcha" data-sitekey="<?= RECAPTCHA_SITE_KEY ?>"></div>
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Bu güvenlik doğrulaması spam koruması içindir.
                                </small>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn-artein-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Mesaj Gönder
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php
// Index.php page specific JavaScript
$pageSpecificJS = <<<'HTML'
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toastEl = document.getElementById("contactToast");
            if (toastEl) {
                const t = new bootstrap.Toast(toastEl);
                t.show();
            }
        });
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector("form");
            if (form) {
                form.addEventListener("submit", function(e) {
                    e.preventDefault();
                    const name = document.querySelector("input[name='name']").value.trim();
                    const email = document.querySelector("input[name='email']").value.trim();
                    const ccEl = document.querySelector("select[name='phone_cc']");
                    const phoneLocalEl = document.querySelector("input[name='phone_local']");
                    const phoneHiddenEl = document.querySelector("input[name='phone']");
                    const cc = ccEl ? ccEl.value.trim() : '';
                    const phoneLocal = phoneLocalEl ? phoneLocalEl.value.trim() : '';
                    const digitsOnly = phoneLocal.replace(/\D/g, '');
                    const composedPhone = digitsOnly ? (cc + digitsOnly) : '';
                    if (phoneHiddenEl) { phoneHiddenEl.value = composedPhone; }
                    const phone = composedPhone;
                    const message = document.querySelector("textarea[name='message']").value.trim();
                    const recaptcha = document.querySelector("textarea[name='g-recaptcha-response']").value.trim();
                    const existingAlerts = document.querySelectorAll(".alert");
                    existingAlerts.forEach(alert => alert.remove());
                    if (!name || !email || !message) { showAlert("Lütfen zorunlu alanları doldurun.", "danger"); return false; }
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(email)) { showAlert("Lütfen geçerli bir e-posta adresi girin.", "danger"); return false; }
                    if (!recaptcha) { showAlert("Lütfen güvenlik doğrulamasını tamamlayın.", "danger"); return false; }
                    // Phone validation (optional)
                    if (phone) {
                        const phoneClean = phone.replace(/[\s\-\(\)]/g, '');
                        if (!/^\+?[0-9]{10,15}$/.test(phoneClean)) {
                            showAlert("Lütfen geçerli bir telefon numarası girin.", "danger");
                            return false;
                        }
                    }
                    const submitBtn = document.querySelector("button[type='submit']");
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = "<i class='fas fa-spinner fa-spin me-2'></i>Gönderiliyor...";
                    submitBtn.disabled = true;
                    const formData = new FormData(form);
                    fetch(window.location.href, { method: "POST", headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: formData })
                    .then(response => response.json())
                    .then(data => { if (data.success) { form.reset(); if (typeof grecaptcha !== 'undefined') { grecaptcha.reset(); } showSuccessPopup(data.message); } else { showAlert(data.message, "danger"); } submitBtn.innerHTML = originalText; submitBtn.disabled = false; })
                    .catch(error => { console.error('Form submission error:', error); showAlert("Bir hata oluştu. Lütfen tekrar deneyin.", "danger"); submitBtn.innerHTML = originalText; submitBtn.disabled = false; });
                });
            }
        });
        function showAlert(message, type) {
            const alertDiv = document.createElement("div");
            alertDiv.className = 'alert alert-' + type + ' alert-dismissible fade show';
            alertDiv.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>' + message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            const form = document.querySelector(".contact-form");
            if (form) { form.insertBefore(alertDiv, form.firstChild); }
        }
        function showSuccessPopup(message) {
            const existingToasts = document.querySelectorAll('.toast');
            existingToasts.forEach(toast => toast.remove());
            const toastHtml = '<div class="toast align-items-center text-bg-success border-0 position-fixed bottom-0 end-0 m-3" id="successToast" role="alert" aria-live="assertive" aria-atomic="true" style="z-index: 9999;">' +
                '<div class="d-flex">' +
                '<div class="toast-body">' +
                '<i class="fas fa-check-circle me-2"></i>' + message +
                '</div>' +
                '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>' +
                '</div>' +
                '</div>';
            document.body.insertAdjacentHTML('beforeend', toastHtml);
            const toastElement = document.getElementById('successToast');
            const toast = new bootstrap.Toast(toastElement, { autohide: true, delay: 5000 });
            toast.show();
            toastElement.addEventListener('hidden.bs.toast', function() { toastElement.remove(); });
        }
        // Ek stil: telefon input grubu marka uyumu
        (function(){
            const style = document.createElement('style');
            style.textContent = `
                .phone-input-group {
                    position: relative;
                }
                .phone-input-group .form-select { 
                    max-width: 150px; 
                    border-color: var(--artein-dark); 
                    background-color: var(--artein-dark);
                    color: white;
                    font-weight: 500;
                    border-radius: 8px 0 0 8px;
                    border-right: none;
                    transition: all 0.3s ease;
                    box-shadow: 0 2px 4px rgba(17,55,54,0.1);
                    font-size: 14px;
                }
                .phone-input-group .form-control { 
                    border-color: var(--artein-dark);
                    border-radius: 0 8px 8px 0;
                    border-left: 1px solid var(--artein-dark);
                    transition: all 0.3s ease;
                    box-shadow: 0 2px 4px rgba(17,55,54,0.1);
                }
                .phone-input-group .form-select:focus, 
                .phone-input-group .form-control:focus {
                    box-shadow: 0 0 0 3px rgba(30,95,93,.15), 0 4px 8px rgba(17,55,54,0.2);
                    border-color: var(--artein-dark);
                    transform: translateY(-1px);
                }
                /* Hover states */
                .phone-input-group .form-select:hover {
                    background-color: #0a2524;
                    border-color: var(--artein-dark);
                    color: white;
                    transform: translateY(-1px);
                    box-shadow: 0 4px 8px rgba(17,55,54,0.15);
                }
                .phone-input-group .form-control:hover {
                    border-color: var(--artein-dark);
                    transform: translateY(-1px);
                    box-shadow: 0 4px 8px rgba(17,55,54,0.15);
                }
                /* Modern dropdown arrow */
                .phone-input-group .form-select {
                    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23ffffff' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
                    background-repeat: no-repeat;
                    background-position: right 0.75rem center;
                    background-size: 16px 12px;
                    padding-right: 2.5rem;
                }
                /* Country flag and code styling - clean black/white text */
                .phone-cc option {
                    padding: 8px 12px;
                    font-size: 14px;
                    line-height: 1.4;
                    color: #333;
                    background-color: white;
                }
                .phone-cc option:hover {
                    background-color: #f8f9fa;
                    color: #333;
                }
                .phone-cc option:checked {
                    background-color: var(--artein-light);
                    color: var(--artein-dark);
                }
            `;
            document.head.appendChild(style);
        })();

        // Modern interactions and input restrictions
        (function(){
            const ccEl = document.querySelector('select[name="phone_cc"]');
            if (!ccEl) return;
            
            // Add modern focus/blur effects
            ccEl.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            ccEl.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
            
            // Telefon numarası alanına sadece rakam, boşluk, tire ve parantez girilebilir
            const phoneInput = document.querySelector('input[name="phone_local"]');
            if (phoneInput) {
                phoneInput.addEventListener('keypress', function(e) {
                    // İzin verilen karakterler: rakam, boşluk, tire, parantez
                    const allowedChars = /[0-9\s\-\(\)]/;
                    if (!allowedChars.test(e.key)) {
                        e.preventDefault();
                    }
                });
                
                // Paste event için de kontrol
                phoneInput.addEventListener('paste', function(e) {
                    setTimeout(() => {
                        let value = this.value;
                        // Sadece izin verilen karakterleri tut
                        value = value.replace(/[^0-9\s\-\(\)]/g, '');
                        this.value = value;
                    }, 0);
                });
            }
            
            // İsim alanına sadece harf ve boşluk
            const nameInput = document.querySelector('input[name="name"]');
            if (nameInput) {
                nameInput.addEventListener('keypress', function(e) {
                    const allowedChars = /[a-zA-ZçğıöşüÇĞIİÖŞÜ\s]/;
                    if (!allowedChars.test(e.key)) {
                        e.preventDefault();
                    }
                });
                
                nameInput.addEventListener('paste', function(e) {
                    setTimeout(() => {
                        let value = this.value;
                        value = value.replace(/[^a-zA-ZçğıöşüÇĞIİÖŞÜ\s]/g, '');
                        this.value = value;
                    }, 0);
                });
            }
        })();
    </script>
HTML;

// Footer'ı include et
include 'includes/footer.php';
?>
