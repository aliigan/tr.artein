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
define('RECAPTCHA_SITE_KEY', '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI'); // Test key
define('RECAPTCHA_SECRET_KEY', '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe'); // Test secret

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
    // Telefon (opsiyonel) doğrulama: +, boşluk, -, ( ) ve rakamlar; en az 10 rakam
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
            // Bugünkü istatistiklerde contact_forms +1
            try {
                if (isset($database)) {
                    $today = date('Y-m-d');
                    $database->execute("INSERT INTO site_stats (stat_date, page_views, unique_visitors, contact_forms) VALUES (?, 0, 0, 0) ON DUPLICATE KEY UPDATE stat_date = stat_date", [$today]);
                    $database->execute("UPDATE site_stats SET contact_forms = contact_forms + 1 WHERE stat_date = ?", [$today]);
                }
            } catch (Throwable $e) {}
            // Form verilerini temizle
            unset($_SESSION['form_data']);
        } else {
            $error_message = 'Mesaj gönderilirken bir hata oluştu. Lütfen tekrar deneyin.';
            // Form verilerini sakla
            $_SESSION['form_data'] = [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'subject' => $subject,
                'message' => $message
            ];
        }
    } else {
        $error_message = implode('<br>', $errors);
        // Form verilerini sakla
        $_SESSION['form_data'] = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'subject' => $subject,
            'message' => $message
        ];
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
                                <a href="<?= escape($slider['button_link']) ?>" class="btn btn-lg mt-3">
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
                                               value="<?= escape($form_data['name'] ?? '') ?>" required>
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
                                        <input type="tel" class="form-control" name="phone" placeholder="Ör: +90 555 123 45 67"
                                               pattern="^\+?[0-9\s\-\(\)]{10,20}$" maxlength="20"
                                               value="<?= escape($form_data['phone'] ?? '') ?>">
                                        <small class="text-muted">Sadece rakam, boşluk ve + işareti kullanılabilir.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Konu</label>
                                        <input type="text" class="form-control" name="subject" maxlength="150" 
                                               value="<?= escape($form_data['subject'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mesaj *</label>
                                <textarea class="form-control" name="message" rows="5" minlength="10" maxlength="2000" required><?= escape($form_data['message'] ?? '') ?></textarea>
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
        // Bootstrap Toast init
        document.addEventListener("DOMContentLoaded", function() {
            const toastEl = document.getElementById("contactToast");
            if (toastEl) {
                const t = new bootstrap.Toast(toastEl);
                t.show();
            }
        });
        // Form validation
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector("form");
            if (form) {
                form.addEventListener("submit", function(e) {
                    e.preventDefault(); // Sayfa yenilenmesini engelle
                    
                    const name = document.querySelector("input[name=\\"name\\"]").value.trim();
                    const email = document.querySelector("input[name=\\"email\\"]").value.trim();
                    const phone = document.querySelector("input[name=\\"phone\\"]").value.trim();
                    const message = document.querySelector("textarea[name=\\"message\\"]").value.trim();
                    const recaptcha = document.querySelector("textarea[name=\\"g-recaptcha-response\\"]").value.trim();
                    
                    // Hata mesajlarını temizle
                    const existingAlerts = document.querySelectorAll(".alert");
                    existingAlerts.forEach(alert => alert.remove());
                    
                    if (!name || !email || !message) {
                        showAlert("Lütfen zorunlu alanları doldurun.", "danger");
                        return false;
                    }
                    
                    // Email validation
                    const emailRegex = /^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/;
                    if (!emailRegex.test(email)) {
                        showAlert("Lütfen geçerli bir e-posta adresi girin.", "danger");
                        return false;
                    }

                    // Phone validation (optional)
                    if (phone) {
                        const phoneClean = phone.replace(/[\\s\\-\\(\\)]/g, '');
                        if (!/^\\+?[0-9]{10,15}$/.test(phoneClean)) {
                            showAlert("Lütfen geçerli bir telefon numarası girin.", "danger");
                            return false;
                        }
                    }
                    
                    // reCAPTCHA validation
                    if (!recaptcha) {
                        showAlert("Lütfen güvenlik doğrulamasını tamamlayın.", "danger");
                        return false;
                    }
                    
                    // Form gönderiliyor, loading göster
                    const submitBtn = document.querySelector("button[type=\\"submit\\"]");
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = "<i class=\\"fas fa-spinner fa-spin me-2\\"></i>Gönderiliyor...";
                    submitBtn.disabled = true;
                    
                    // Formu AJAX ile gönder
                    const formData = new FormData(form);
                    
                    fetch(window.location.href, {
                        method: "POST",
                        body: formData
                    })
                    .then(response => response.text())
                    .then(data => {
                        // Sayfayı yenile
                        window.location.reload();
                    })
                    .catch(error => {
                        showAlert("Bir hata oluştu. Lütfen tekrar deneyin.", "danger");
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    });
                });
            }
        });
        
        // Alert gösterme fonksiyonu
        function showAlert(message, type) {
            const alertDiv = document.createElement("div");
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                <i class="fas fa-exclamation-triangle me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const form = document.querySelector(".contact-form");
            if (form) {
                form.insertBefore(alertDiv, form.firstChild);
            }
        }
    </script>
HTML;

// Footer'ı include et
include 'includes/footer.php';
?>
