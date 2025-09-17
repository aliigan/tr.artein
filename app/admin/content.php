<?php
/**
 * BuildTech CMS - Content Management
 * Admin panelinde içerik yönetimi
 */

define('ADMIN_PANEL', true);
require_once 'config/config.php';
require_once dirname(__DIR__) . '/shared/config/frontend_config.php'; // getSiteSettings() fonksiyonu için
requireLogin();

$page_title = 'İçerik Yönetimi';
$breadcrumb = [
    ['title' => 'Ana Sayfa', 'url' => 'dashboard.php'],
    ['title' => 'İçerik Yönetimi']
];

// İşlem türü
$action = $_GET['action'] ?? 'list';
$content_type = $_GET['type'] ?? 'about';

// İçerik güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!validateCSRF($csrf_token)) {
        setErrorMessage('Güvenlik hatası. Lütfen tekrar deneyin.');
    } else {
        if ($content_type === 'about') {
            $title = trim($_POST['title'] ?? '');
            $subtitle = trim($_POST['subtitle'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $image = trim($_POST['image'] ?? '');
            
            // Resim yükleme işlemi
            if (isset($_FILES['image_upload']) && $_FILES['image_upload']['error'] === UPLOAD_ERR_OK) {
                $upload_result = uploadFile($_FILES['image_upload'], 'general');
                if ($upload_result['success']) {
                    $image = $upload_result['path'];
                } else {
                    setErrorMessage('Resim yüklenirken hata: ' . $upload_result['message']);
                }
            }
            
            if (empty($title)) {
                setErrorMessage('Başlık alanı zorunludur.');
            } else {
                // Mevcut kayıt var mı kontrol et
                $existing = $database->fetchOne("SELECT id FROM about_content ORDER BY id DESC LIMIT 1");
                
                if ($existing) {
                    // Güncelle
                    $sql = "UPDATE about_content SET title = ?, subtitle = ?, content = ?, image = ?, updated_at = NOW() WHERE id = ?";
                    $params = [$title, $subtitle, $content, $image, $existing['id']];
                } else {
                    // Yeni ekle
                    $sql = "INSERT INTO about_content (title, subtitle, content, image) VALUES (?, ?, ?, ?)";
                    $params = [$title, $subtitle, $content, $image];
                }
                
                if ($database->execute($sql, $params)) {
                    setSuccessMessage('Hakkımızda içeriği başarıyla güncellendi.');
                } else {
                    setErrorMessage('İçerik güncellenirken bir hata oluştu.');
                }
            }
        } elseif ($content_type === 'site_settings') {
            // Site ayarları güncelleme
            $settings = [
                'site_title' => trim($_POST['site_title'] ?? ''),
                'site_description' => trim($_POST['site_description'] ?? ''),
                'company_name' => trim($_POST['company_name'] ?? ''),
                'company_address' => trim($_POST['company_address'] ?? ''),
                'company_phone' => trim($_POST['company_phone'] ?? ''),
                'company_email' => trim($_POST['company_email'] ?? ''),
                'meta_keywords' => trim($_POST['meta_keywords'] ?? ''),
                'google_analytics' => trim($_POST['google_analytics'] ?? ''),
                'social_facebook' => trim($_POST['social_facebook'] ?? ''),
                'social_instagram' => trim($_POST['social_instagram'] ?? ''),
                'social_linkedin' => trim($_POST['social_linkedin'] ?? ''),
                'social_twitter' => trim($_POST['social_twitter'] ?? '')
            ];
            
            $success_count = 0;
            foreach ($settings as $key => $value) {
                $existing = $database->fetchOne("SELECT id FROM site_settings WHERE setting_key = ?", [$key]);
                
                if ($existing) {
                    if ($database->execute("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?", [$value, $key])) {
                        $success_count++;
                    }
                } else {
                    if ($database->execute("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)", [$key, $value])) {
                        $success_count++;
                    }
                }
            }
            
            if ($success_count > 0) {
                setSuccessMessage('Site ayarları başarıyla güncellendi.');
            } else {
                setErrorMessage('Site ayarları güncellenirken bir hata oluştu.');
            }
        }
    }
}

// Sayfa verilerini hazırla
if ($content_type === 'about') {
    $about_content = $database->fetchOne("SELECT * FROM about_content ORDER BY id DESC LIMIT 1") ?: [
        'title' => '',
        'subtitle' => '',
        'content' => '',
        'image' => ''
    ];
} elseif ($content_type === 'site_settings') {
    $site_settings = getSiteSettings();
}

include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?= $page_title ?></h1>
</div>

<?= displayMessages() ?>

<!-- İçerik Türü Seçimi -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="btn-group w-100" role="group">
                    <a href="content.php?type=about" 
                       class="btn <?= $content_type === 'about' ? 'btn-primary' : 'btn-outline-primary' ?>">
                        <i class="fas fa-users me-2"></i>Hakkımızda İçeriği
                    </a>
                    <a href="content.php?type=site_settings" 
                       class="btn <?= $content_type === 'site_settings' ? 'btn-primary' : 'btn-outline-primary' ?>">
                        <i class="fas fa-cog me-2"></i>Site Ayarları
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($content_type === 'about'): ?>
<!-- Hakkımızda İçeriği -->
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Hakkımızda İçeriği</h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION[CSRF_TOKEN_NAME] ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Başlık *</label>
                        <input type="text" class="form-control" name="title" 
                               value="<?= escape($about_content['title']) ?>" required maxlength="255">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Alt Başlık</label>
                        <input type="text" class="form-control" name="subtitle" 
                               value="<?= escape($about_content['subtitle']) ?>" maxlength="500">
                        <small class="text-muted">Başlık altında görünecek kısa açıklama</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">İçerik</label>
                        <textarea class="form-control summernote" name="content" rows="10"><?= escape($about_content['content']) ?></textarea>
                        <small class="text-muted">HTML etiketleri kullanabilirsiniz</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mevcut Resim</label>
                        <?php if ($about_content['image']): ?>
                            <div class="mb-2">
                                <img src="../../<?= escape($about_content['image']) ?>" alt="Mevcut Resim" 
                                     class="img-thumbnail" style="max-width: 200px;">
                            </div>
                        <?php endif; ?>
                        <input type="hidden" name="image" value="<?= escape($about_content['image']) ?>">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Yeni Resim Yükle</label>
                        <input type="file" class="form-control" name="image_upload" accept="image/*">
                        <small class="text-muted">Desteklenen formatlar: JPG, PNG, GIF, WebP (Maks: 10MB)</small>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Değişiklikleri Kaydet
                        </button>
                        <a href="<?= SITE_URL ?>/app/frontend/biz-kimiz.php" target="_blank" class="btn btn-outline-secondary">
                            <i class="fas fa-eye me-2"></i>Önizleme
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Bilgilendirme</h6>
            </div>
            <div class="card-body">
                <h6>İçerik Düzenleme İpuçları:</h6>
                <ul class="small text-muted mb-0">
                    <li>Başlık kısa ve etkileyici olmalı</li>
                    <li>Alt başlık ile ana mesajınızı özetleyin</li>
                    <li>İçerikte HTML etiketleri kullanabilirsiniz</li>
                    <li>Resim boyutu 1200x800 piksel önerilir</li>
                    <li>Değişiklikler anında sitede görünür</li>
                </ul>
                
                <hr>
                
                <h6>SEO İpuçları:</h6>
                <ul class="small text-muted mb-0">
                    <li>Başlık 60 karakteri geçmemeli</li>
                    <li>Alt başlık 160 karakteri geçmemeli</li>
                    <li>İçerikte anahtar kelimeler kullanın</li>
                    <li>Resim alt etiketlerini unutmayın</li>
                </ul>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>İçerik İstatistikleri</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h5 class="text-primary mb-0"><?= str_word_count(strip_tags($about_content['content'])) ?></h5>
                            <small class="text-muted">Kelime</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h5 class="text-info mb-0"><?= strlen(strip_tags($about_content['content'])) ?></h5>
                        <small class="text-muted">Karakter</small>
                    </div>
                </div>
                <?php if (isset($about_content['updated_at'])): ?>
                <hr>
                <small class="text-muted">
                    <strong>Son Güncelleme:</strong><br>
                    <?= formatDate($about_content['updated_at']) ?>
                </small>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php elseif ($content_type === 'site_settings'): ?>
<!-- Site Ayarları -->
<div class="row">
    <div class="col-lg-8">
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION[CSRF_TOKEN_NAME] ?>">
            
            <!-- Genel Ayarlar -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-globe me-2"></i>Genel Site Ayarları</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Site Başlığı</label>
                                <input type="text" class="form-control" name="site_title" 
                                       value="<?= escape($site_settings['site_title'] ?? '') ?>" maxlength="100">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Şirket Adı</label>
                                <input type="text" class="form-control" name="company_name" 
                                       value="<?= escape($site_settings['company_name'] ?? '') ?>" maxlength="100">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Site Açıklaması</label>
                        <textarea class="form-control" name="site_description" rows="3" maxlength="255"><?= escape($site_settings['site_description'] ?? '') ?></textarea>
                        <small class="text-muted">Arama motorlarında görünecek açıklama</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Meta Anahtar Kelimeler</label>
                        <input type="text" class="form-control" name="meta_keywords" 
                               value="<?= escape($site_settings['meta_keywords'] ?? '') ?>" maxlength="255">
                        <small class="text-muted">Virgülle ayırarak yazın (örn: inşaat, mühendislik, tasarım)</small>
                    </div>
                </div>
            </div>
            
            <!-- İletişim Bilgileri -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-address-card me-2"></i>İletişim Bilgileri</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Adres</label>
                        <textarea class="form-control" name="company_address" rows="2" maxlength="255"><?= escape($site_settings['company_address'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Telefon</label>
                                <input type="text" class="form-control" name="company_phone" 
                                       value="<?= escape($site_settings['company_phone'] ?? '') ?>" maxlength="20">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">E-posta</label>
                                <input type="email" class="form-control" name="company_email" 
                                       value="<?= escape($site_settings['company_email'] ?? '') ?>" maxlength="100">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sosyal Medya -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-share-alt me-2"></i>Sosyal Medya Hesapları</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><i class="fab fa-facebook me-2"></i>Facebook</label>
                                <input type="url" class="form-control" name="social_facebook" 
                                       value="<?= escape($site_settings['social_facebook'] ?? '') ?>" 
                                       placeholder="https://facebook.com/yourpage">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><i class="fab fa-instagram me-2"></i>Instagram</label>
                                <input type="url" class="form-control" name="social_instagram" 
                                       value="<?= escape($site_settings['social_instagram'] ?? '') ?>" 
                                       placeholder="https://instagram.com/yourpage">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><i class="fab fa-linkedin me-2"></i>LinkedIn</label>
                                <input type="url" class="form-control" name="social_linkedin" 
                                       value="<?= escape($site_settings['social_linkedin'] ?? '') ?>" 
                                       placeholder="https://linkedin.com/company/yourcompany">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><i class="fab fa-twitter me-2"></i>Twitter</label>
                                <input type="url" class="form-control" name="social_twitter" 
                                       value="<?= escape($site_settings['social_twitter'] ?? '') ?>" 
                                       placeholder="https://twitter.com/yourhandle">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- SEO & Analytics -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>SEO & Analytics</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Google Analytics ID</label>
                        <input type="text" class="form-control" name="google_analytics" 
                               value="<?= escape($site_settings['google_analytics'] ?? '') ?>" 
                               placeholder="G-XXXXXXXXXX">
                        <small class="text-muted">Google Analytics 4 ölçüm kimliği</small>
                    </div>
                </div>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Ayarları Kaydet
                </button>
                <a href="<?= SITE_URL ?>" target="_blank" class="btn btn-outline-secondary">
                    <i class="fas fa-external-link-alt me-2"></i>Siteyi Görüntüle
                </a>
            </div>
        </form>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Ayarlar Yardımı</h6>
            </div>
            <div class="card-body">
                <h6>Site Ayarları:</h6>
                <ul class="small text-muted mb-3">
                    <li><strong>Site Başlığı:</strong> Tarayıcı sekmesinde görünür</li>
                    <li><strong>Açıklama:</strong> Google'da site açıklaması</li>
                    <li><strong>Anahtar Kelimeler:</strong> SEO için önemli</li>
                </ul>
                
                <h6>Sosyal Medya:</h6>
                <ul class="small text-muted mb-3">
                    <li>Tam URL adreslerini girin</li>
                    <li>Boş bırakılan hesaplar gösterilmez</li>
                    <li>Footer'da otomatik görünür</li>
                </ul>
                
                <h6>Google Analytics:</h6>
                <ul class="small text-muted mb-0">
                    <li>GA4 ölçüm kimliğinizi girin</li>
                    <li>Ziyaretçi istatistikleri için gerekli</li>
                    <li>Boş bırakılabilir</li>
                </ul>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-tools me-2"></i>Hızlı İşlemler</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= SITE_URL ?>/app/frontend/index.php" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-home me-2"></i>Ana Sayfayı Görüntüle
                    </a>
                    <a href="<?= SITE_URL ?>/app/frontend/biz-kimiz.php" target="_blank" class="btn btn-sm btn-outline-info">
                        <i class="fas fa-users me-2"></i>Hakkımızda Sayfası
                    </a>
                    <a href="messages.php" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-envelope me-2"></i>İletişim Mesajları
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<!-- Summernote CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/lang/summernote-tr-TR.min.js"></script>

<script>
$(document).ready(function() {
    // Summernote editör
    $('.summernote').summernote({
        height: 300,
        lang: 'tr-TR',
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });
    
    // Form validasyonu
    $('form').on('submit', function(e) {
        const requiredFields = $(this).find('[required]');
        let isValid = true;
        
        requiredFields.each(function() {
            if (!$(this).val().trim()) {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Lütfen zorunlu alanları doldurun.');
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
