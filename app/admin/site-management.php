<?php
/**
 * BuildTech CMS - Site Management
 * Unified content and settings management with 5 tabs
 */

define('ADMIN_PANEL', true);
require_once 'config/config.php';
require_once dirname(__DIR__) . '/shared/config/frontend_config.php';
requireLogin();

$page_title = 'Site Yönetimi';
$breadcrumb = [
    ['title' => 'Ana Sayfa', 'url' => 'dashboard.php'],
    ['title' => 'Site Yönetimi']
];

// İşlem türü
$action = $_GET['action'] ?? 'overview';
$section = $_GET['section'] ?? 'content';

// Form işleme
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!validateCSRF($csrf_token)) {
        setErrorMessage('Güvenlik hatası. Lütfen tekrar deneyin.');
    } else {
        $post_section = $_POST['section'] ?? '';
        
        // About Content Update
        if ($post_section === 'about') {
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
        }
        
        // General Site Settings Update
        elseif ($post_section === 'general') {
            $general_settings = [
                'site_title' => trim($_POST['site_title'] ?? ''),
                'site_description' => trim($_POST['site_description'] ?? ''),
                'company_name' => trim($_POST['company_name'] ?? '')
            ];
            
            $success_count = 0;
            foreach ($general_settings as $key => $value) {
                $existing = $database->fetchOne("SELECT id FROM site_settings WHERE setting_key = ?", [$key]);
                
                if ($existing) {
                    if ($database->execute("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?", [$value, $key])) {
                        $success_count++;
                    }
                } else {
                    if ($database->execute("INSERT INTO site_settings (setting_key, setting_value, setting_type, description) VALUES (?, ?, 'text', ?)", 
                        [$key, $value, ucfirst(str_replace(['_', 'site'], [' ', 'Site'], $key))])) {
                        $success_count++;
                    }
                }
            }
            
            if ($success_count > 0) {
                setSuccessMessage('Genel site bilgileri başarıyla güncellendi.');
            } else {
                setErrorMessage('Genel site bilgileri güncellenirken bir hata oluştu.');
            }
        }
        
        // Contact Information Update
        elseif ($post_section === 'contact') {
            $contact_settings = [
                'company_address' => trim($_POST['company_address'] ?? ''),
                'company_phone' => trim($_POST['company_phone'] ?? ''),
                'company_email' => trim($_POST['company_email'] ?? '')
            ];
            
            $success_count = 0;
            foreach ($contact_settings as $key => $value) {
                $existing = $database->fetchOne("SELECT id FROM site_settings WHERE setting_key = ?", [$key]);
                
                if ($existing) {
                    if ($database->execute("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?", [$value, $key])) {
                        $success_count++;
                    }
                } else {
                    if ($database->execute("INSERT INTO site_settings (setting_key, setting_value, setting_type, description) VALUES (?, ?, 'text', ?)", 
                        [$key, $value, ucfirst(str_replace(['company_', '_'], ['', ' '], $key))])) {
                        $success_count++;
                    }
                }
            }
            
            if ($success_count > 0) {
                setSuccessMessage('İletişim bilgileri başarıyla güncellendi.');
            } else {
                setErrorMessage('İletişim bilgileri güncellenirken bir hata oluştu.');
            }
        }
        
        // SEO & Analytics Update
        elseif ($post_section === 'seo') {
            $seo_settings = [
                'meta_title' => trim($_POST['meta_title'] ?? ''),
                'meta_description' => trim($_POST['meta_description'] ?? ''),
                'meta_keywords' => trim($_POST['meta_keywords'] ?? ''),
                'google_analytics' => trim($_POST['google_analytics'] ?? ''),
                'search_console' => trim($_POST['search_console'] ?? '')
            ];
            
            $success_count = 0;
            foreach ($seo_settings as $key => $value) {
                $existing = $database->fetchOne("SELECT id FROM site_settings WHERE setting_key = ?", [$key]);
                
                if ($existing) {
                    if ($database->execute("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?", [$value, $key])) {
                        $success_count++;
                    }
                } else {
                    if ($database->execute("INSERT INTO site_settings (setting_key, setting_value, setting_type, description) VALUES (?, ?, 'text', ?)", 
                        [$key, $value, ucfirst(str_replace(['meta_', '_'], ['Meta ', ' '], $key))])) {
                        $success_count++;
                    }
                }
            }
            
            if ($success_count > 0) {
                setSuccessMessage('SEO ve Analytics ayarları başarıyla güncellendi.');
            } else {
                setErrorMessage('SEO ve Analytics ayarları güncellenirken bir hata oluştu.');
            }
        }
        
        // Social Media Update
        elseif ($post_section === 'social') {
            $social_settings = [
                'social_facebook' => trim($_POST['social_facebook'] ?? ''),
                'social_instagram' => trim($_POST['social_instagram'] ?? ''),
                'whatsapp_number' => trim($_POST['whatsapp_number'] ?? '')
            ];
            
            $success_count = 0;
            foreach ($social_settings as $key => $value) {
                $existing = $database->fetchOne("SELECT id FROM site_settings WHERE setting_key = ?", [$key]);
                
                if ($existing) {
                    if ($database->execute("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?", [$value, $key])) {
                        $success_count++;
                    }
                } else {
                    if ($database->execute("INSERT INTO site_settings (setting_key, setting_value, setting_type, description) VALUES (?, ?, 'text', ?)", 
                        [$key, $value, ucfirst(str_replace(['social_', '_'], ['', ' '], $key)) . ''])) {
                        $success_count++;
                    }
                }
            }
            
            if ($success_count > 0) {
                setSuccessMessage('Sosyal medya hesapları başarıyla güncellendi.');
            } else {
                setErrorMessage('Sosyal medya hesapları güncellenirken bir hata oluştu.');
            }
        }
    }
}

// Verileri hazırla
$about_content = $database->fetchOne("SELECT * FROM about_content ORDER BY id DESC LIMIT 1") ?: [
    'title' => '',
    'subtitle' => '',
    'content' => '',
    'image' => ''
];

$site_settings = [];
$settings_result = $database->fetchAll("SELECT setting_key, setting_value FROM site_settings");
foreach ($settings_result as $setting) {
    $site_settings[$setting['setting_key']] = $setting['setting_value'];
}

include 'includes/header.php';
?>

<?= displayMessages() ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-cogs me-2"></i>Site Yönetimi
        </h1>
        <div class="d-flex">
            <a href="dashboard.php" class="btn btn-secondary btn-sm me-2">
                <i class="fas fa-arrow-left"></i>
                Geri Dön
            </a>
            <a href="https://artein.tr" target="_blank" class="btn btn-info btn-sm">
                <i class="fas fa-external-link-alt"></i>
                Siteyi Görüntüle
            </a>
        </div>
    </div>

    <!-- Main Content Tabs -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Tab Navigation -->
                    <style>
                    .nav-tabs .nav-link {
                        color: #495057;
                        background-color: #f8f9fa;
                        border: 1px solid #dee2e6;
                        margin-right: 2px;
                        padding: 12px 20px;
                        font-weight: 500;
                        transition: all 0.3s ease;
                    }
                    
                    .nav-tabs .nav-link:hover {
                        color: #113736;
                        background-color: #e9ecef;
                        border-color: #dfeade;
                    }
                    
                    .nav-tabs .nav-link.active {
                        color: #fff !important;
                        background-color: #113736 !important;
                        border-color: #113736 !important;
                        font-weight: 600;
                    }
                    
                    .nav-tabs {
                        border-bottom: 2px solid #113736;
                        margin-bottom: 2rem;
                    }
                    
                    .tab-content {
                        min-height: 400px;
                        padding: 1rem 0;
                    }
                    </style>
                    
                    <ul class="nav nav-tabs mb-4" id="siteManagementTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a href="?section=content" class="nav-link <?= $section === 'content' ? 'active' : '' ?>" 
                               id="content-tab">
                                <i class="fas fa-edit me-2"></i>Sayfa İçerikleri
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="?section=general" class="nav-link <?= $section === 'general' ? 'active' : '' ?>" 
                               id="general-tab">
                                <i class="fas fa-globe me-2"></i>Genel Bilgiler
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="?section=contact" class="nav-link <?= $section === 'contact' ? 'active' : '' ?>" 
                               id="contact-tab">
                                <i class="fas fa-address-card me-2"></i>İletişim Bilgileri
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="?section=seo" class="nav-link <?= $section === 'seo' ? 'active' : '' ?>" 
                               id="seo-tab">
                                <i class="fas fa-search me-2"></i>SEO & Analytics
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="?section=social" class="nav-link <?= $section === 'social' ? 'active' : '' ?>" 
                               id="social-tab">
                                <i class="fas fa-share-alt me-2"></i>Sosyal Medya Hesapları
                            </a>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="siteManagementTabContent">
                        
                        <!-- Content Management Tab -->
                        <div class="<?= $section === 'content' ? '' : 'd-none' ?>" 
                             id="content" role="tabpanel">
                            
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION[CSRF_TOKEN_NAME] ?>">
                                <input type="hidden" name="section" value="about">
                                
                                <div class="row">
                                    <div class="col-lg-8">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Hakkımızda İçeriği</h5>
                                            </div>
                                            <div class="card-body">
                                                
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
                                                    <?php if (!empty($about_content['image'])): ?>
                                                        <div class="mb-2">
                                                            <img src="../../<?= escape($about_content['image']) ?>" 
                                                                 alt="Mevcut resim" 
                                                                 class="img-thumbnail" 
                                                                 style="max-width: 200px;">
                                                        </div>
                                                    <?php endif; ?>
                                                    <input type="file" class="form-control" name="image_upload" accept="image/*">
                                                    <small class="text-muted">Yeni resim yüklemek için seçin (JPG, PNG, GIF)</small>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-4">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>İçerik Bilgileri</h6>
                                            </div>
                                            <div class="card-body">
                                                <ul class="list-unstyled">
                                                    <li class="mb-2">
                                                        <i class="fas fa-check-circle text-success me-2"></i>
                                                        <small>Başlık zorunlu alandır</small>
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-check-circle text-success me-2"></i>
                                                        <small>Alt başlık ana sayfada görünür</small>
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-check-circle text-success me-2"></i>
                                                        <small>İçerik HTML formatında olabilir</small>
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-check-circle text-success me-2"></i>
                                                        <small>Resim ana sayfada gösterilir</small>
                                                    </li>
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
                                                            <h5 class="text-primary mb-0"><?= str_word_count(strip_tags($about_content['content'] ?? '')) ?></h5>
                                                            <small class="text-muted">Kelime</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <h5 class="text-info mb-0"><?= strlen(strip_tags($about_content['content'] ?? '')) ?></h5>
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
                                
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i>İçeriği Kaydet
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- General Information Tab -->
                        <div class="<?= $section === 'general' ? '' : 'd-none' ?>" 
                             id="general" role="tabpanel">
                            
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION[CSRF_TOKEN_NAME] ?>">
                                <input type="hidden" name="section" value="general">
                                
                                <div class="row">
                                    <div class="col-lg-8">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">
                                                    <i class="fas fa-globe me-2"></i>Genel Site Bilgileri
                                                </h5>
                                            </div>
                                            <div class="card-body">
                                                
                                                <!-- Site Başlığı -->
                                                <div class="mb-4">
                                                    <label for="site_title" class="form-label">
                                                        <i class="fas fa-heading me-2"></i>Site Başlığı
                                                    </label>
                                                    <input type="text" 
                                                           class="form-control" 
                                                           id="site_title" 
                                                           name="site_title" 
                                                           value="<?= escape($site_settings['site_title'] ?? '') ?>" 
                                                           placeholder="Örnek: Arte In İnşaat" 
                                                           maxlength="100">
                                                    <small class="form-text text-muted">
                                                        Tarayıcı sekmesinde ve arama sonuçlarında görünecek başlık
                                                    </small>
                                                </div>

                                                <!-- Site Açıklaması -->
                                                <div class="mb-4">
                                                    <label for="site_description" class="form-label">
                                                        <i class="fas fa-align-left me-2"></i>Site Açıklaması
                                                    </label>
                                                    <textarea class="form-control" 
                                                              id="site_description" 
                                                              name="site_description" 
                                                              rows="3" 
                                                              placeholder="Site hakkında kısa açıklama" 
                                                              maxlength="255"><?= escape($site_settings['site_description'] ?? '') ?></textarea>
                                                    <small class="form-text text-muted">
                                                        Arama sonuçlarında görünecek açıklama (maksimum 255 karakter)
                                                    </small>
                                                </div>

                                                <!-- Şirket Adı -->
                                                <div class="mb-4">
                                                    <label for="company_name" class="form-label">
                                                        <i class="fas fa-building me-2"></i>Şirket Adı
                                                    </label>
                                                    <input type="text" 
                                                           class="form-control" 
                                                           id="company_name" 
                                                           name="company_name" 
                                                           value="<?= escape($site_settings['company_name'] ?? '') ?>" 
                                                           placeholder="Örnek: Arte In İnşaat Ltd. Şti." 
                                                           maxlength="100">
                                                    <small class="form-text text-muted">
                                                        Resmi şirket adı
                                                    </small>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-4">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">
                                                    <i class="fas fa-info-circle me-2"></i>Bilgi
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <ul class="list-unstyled">
                                                    <li class="mb-2">
                                                        <i class="fas fa-check-circle text-success me-2"></i>
                                                        <small>Site başlığı SEO için çok önemlidir</small>
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-check-circle text-success me-2"></i>
                                                        <small>Açıklama arama sonuçlarında görünür</small>
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-check-circle text-success me-2"></i>
                                                        <small>Şirket adı iletişim sayfalarında kullanılır</small>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i>Genel Bilgileri Kaydet
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Contact Information Tab -->
                        <div class="<?= $section === 'contact' ? '' : 'd-none' ?>" 
                             id="contact" role="tabpanel">
                            
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION[CSRF_TOKEN_NAME] ?>">
                                <input type="hidden" name="section" value="contact">
                                
                                <div class="row">
                                    <div class="col-lg-8">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">
                                                    <i class="fas fa-address-card me-2"></i>İletişim Bilgileri
                                                </h5>
                                            </div>
                                            <div class="card-body">
                                                
                                                <!-- Adres -->
                                                <div class="mb-4">
                                                    <label for="company_address" class="form-label">
                                                        <i class="fas fa-map-marker-alt me-2"></i>Şirket Adresi
                                                    </label>
                                                    <textarea class="form-control" 
                                                              id="company_address" 
                                                              name="company_address" 
                                                              rows="3" 
                                                              placeholder="Şirket adresi"><?= escape($site_settings['company_address'] ?? '') ?></textarea>
                                                    <small class="form-text text-muted">
                                                        Tam şirket adresi
                                                    </small>
                                                </div>

                                                <!-- Telefon -->
                                                <div class="mb-4">
                                                    <label for="company_phone" class="form-label">
                                                        <i class="fas fa-phone me-2"></i>Telefon Numarası
                                                    </label>
                                                    <input type="tel" 
                                                           class="form-control" 
                                                           id="company_phone" 
                                                           name="company_phone" 
                                                           value="<?= escape($site_settings['company_phone'] ?? '') ?>" 
                                                           placeholder="Örnek: +90 555 123 45 67">
                                                    <small class="form-text text-muted">
                                                        Uluslararası format ile telefon numarası
                                                    </small>
                                                </div>

                                                <!-- E-posta -->
                                                <div class="mb-4">
                                                    <label for="company_email" class="form-label">
                                                        <i class="fas fa-envelope me-2"></i>E-posta Adresi
                                                    </label>
                                                    <input type="email" 
                                                           class="form-control" 
                                                           id="company_email" 
                                                           name="company_email" 
                                                           value="<?= escape($site_settings['company_email'] ?? '') ?>" 
                                                           placeholder="Örnek: info@artein.com">
                                                    <small class="form-text text-muted">
                                                        İletişim için kullanılacak e-posta adresi
                                                    </small>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-4">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">
                                                    <i class="fas fa-info-circle me-2"></i>Bilgi
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <ul class="list-unstyled">
                                                    <li class="mb-2">
                                                        <i class="fas fa-check-circle text-success me-2"></i>
                                                        <small>Adres iletişim sayfasında görünür</small>
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-check-circle text-success me-2"></i>
                                                        <small>Telefon tıklanabilir link olur</small>
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-check-circle text-success me-2"></i>
                                                        <small>E-posta iletişim formunda kullanılır</small>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i>İletişim Bilgilerini Kaydet
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- SEO & Analytics Tab -->
                        <div class="<?= $section === 'seo' ? '' : 'd-none' ?>" 
                             id="seo" role="tabpanel">
                            
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION[CSRF_TOKEN_NAME] ?>">
                                <input type="hidden" name="section" value="seo">
                                
                                <div class="row">
                                    <div class="col-lg-8">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">
                                                    <i class="fas fa-search me-2"></i>SEO & Analytics Ayarları
                                                </h5>
                                            </div>
                                            <div class="card-body">
                                                
                                                <!-- Meta Title -->
                                                <div class="mb-4">
                                                    <label for="meta_title" class="form-label">
                                                        <i class="fas fa-heading me-2"></i>Meta Title
                                                    </label>
                                                    <input type="text" 
                                                           class="form-control" 
                                                           id="meta_title" 
                                                           name="meta_title" 
                                                           value="<?= escape($site_settings['meta_title'] ?? '') ?>" 
                                                           placeholder="Örnek: Arte In İnşaat - Modern Yapı Çözümleri" 
                                                           maxlength="60">
                                                    <small class="form-text text-muted">
                                                        Arama sonuçlarında görünecek başlık (maksimum 60 karakter)
                                                    </small>
                                                </div>

                                                <!-- Meta Description -->
                                                <div class="mb-4">
                                                    <label for="meta_description" class="form-label">
                                                        <i class="fas fa-align-left me-2"></i>Meta Description
                                                    </label>
                                                    <textarea class="form-control" 
                                                              id="meta_description" 
                                                              name="meta_description" 
                                                              rows="3" 
                                                              placeholder="Site hakkında detaylı açıklama" 
                                                              maxlength="160"><?= escape($site_settings['meta_description'] ?? '') ?></textarea>
                                                    <small class="form-text text-muted">
                                                        Arama sonuçlarında görünecek açıklama (maksimum 160 karakter)
                                                    </small>
                                                </div>

                                                <!-- Meta Keywords -->
                                                <div class="mb-4">
                                                    <label for="meta_keywords" class="form-label">
                                                        <i class="fas fa-tags me-2"></i>Meta Keywords
                                                    </label>
                                                    <textarea class="form-control" 
                                                              id="meta_keywords" 
                                                              name="meta_keywords" 
                                                              rows="2" 
                                                              placeholder="inşaat, yapı, mimarlık, tasarım, proje"><?= escape($site_settings['meta_keywords'] ?? '') ?></textarea>
                                                    <small class="form-text text-muted">
                                                        Virgülle ayırın (örnek: inşaat, yapı, mimarlık)
                                                    </small>
                                                </div>

                                                <!-- Google Analytics ID -->
                                                <div class="mb-4">
                                                    <label for="google_analytics" class="form-label">
                                                        <i class="fab fa-google me-2"></i>Google Analytics ID
                                                    </label>
                                                    <input type="text" 
                                                           class="form-control" 
                                                           id="google_analytics" 
                                                           name="google_analytics" 
                                                           value="<?= escape($site_settings['google_analytics'] ?? '') ?>" 
                                                           placeholder="Örnek: GA-XXXXXXXXX-X">
                                                    <small class="form-text text-muted">
                                                        Google Analytics takip kodu (GA- ile başlar)
                                                    </small>
                                                </div>

                                                <!-- Search Console -->
                                                <div class="mb-4">
                                                    <label for="search_console" class="form-label">
                                                        <i class="fas fa-search-plus me-2"></i>Search Console Kodu
                                                    </label>
                                                    <textarea class="form-control" 
                                                              id="search_console" 
                                                              name="search_console" 
                                                              rows="3" 
                                                              placeholder="Google Search Console doğrulama kodu"><?= escape($site_settings['search_console'] ?? '') ?></textarea>
                                                    <small class="form-text text-muted">
                                                        Google Search Console'dan alınan doğrulama kodu
                                                    </small>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-4">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">
                                                    <i class="fas fa-lightbulb me-2"></i>SEO İpuçları
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <ul class="list-unstyled">
                                                    <li class="mb-2">
                                                        <i class="fas fa-check-circle text-success me-2"></i>
                                                        <small>Meta title 50-60 karakter olmalı</small>
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-check-circle text-success me-2"></i>
                                                        <small>Meta description 150-160 karakter olmalı</small>
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-check-circle text-success me-2"></i>
                                                        <small>Keywords artık SEO'da çok etkili değil</small>
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-check-circle text-success me-2"></i>
                                                        <small>Analytics kodu site performansını takip eder</small>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i>SEO Ayarlarını Kaydet
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Social Media Tab -->
                        <div class="<?= $section === 'social' ? '' : 'd-none' ?>" 
                             id="social" role="tabpanel">
                            
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION[CSRF_TOKEN_NAME] ?>">
                                <input type="hidden" name="section" value="social">
                                
                                <div class="row">
                                    <div class="col-lg-8">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">
                                                    <i class="fas fa-share-alt me-2"></i>Sosyal Medya Hesapları
                                                </h5>
                                            </div>
                                            <div class="card-body">
                                                
                                                <!-- Facebook -->
                                                <div class="mb-4">
                                                    <label for="social_facebook" class="form-label">
                                                        <i class="fab fa-facebook text-primary me-2"></i>Facebook URL
                                                    </label>
                                                    <input type="url" 
                                                           class="form-control" 
                                                           id="social_facebook" 
                                                           name="social_facebook" 
                                                           value="<?= escape($site_settings['social_facebook'] ?? '') ?>" 
                                                           placeholder="https://www.facebook.com/artein">
                                                    <small class="form-text text-muted">
                                                        Facebook sayfanızın tam URL'si
                                                    </small>
                                                </div>

                                                <!-- Instagram -->
                                                <div class="mb-4">
                                                    <label for="social_instagram" class="form-label">
                                                        <i class="fab fa-instagram text-danger me-2"></i>Instagram URL
                                                    </label>
                                                    <input type="url" 
                                                           class="form-control" 
                                                           id="social_instagram" 
                                                           name="social_instagram" 
                                                           value="<?= escape($site_settings['social_instagram'] ?? '') ?>" 
                                                           placeholder="https://www.instagram.com/artein">
                                                    <small class="form-text text-muted">
                                                        Instagram hesabınızın tam URL'si
                                                    </small>
                                                </div>

                                                <!-- WhatsApp -->
                                                <div class="mb-4">
                                                    <label for="whatsapp_number" class="form-label">
                                                        <i class="fab fa-whatsapp text-success me-2"></i>WhatsApp Numarası
                                                    </label>
                                                    <input type="text" 
                                                           class="form-control" 
                                                           id="whatsapp_number" 
                                                           name="whatsapp_number" 
                                                           value="<?= escape($site_settings['whatsapp_number'] ?? getSetting('whatsapp_number', getSetting('company_phone', ''))) ?>" 
                                                           placeholder="Örnek: +90 555 888 99 88">
                                                    <small class="form-text text-muted">
                                                        Ülke kodu ile yazın. Ziyaretçiler WhatsApp’a yönlendirilir.
                                                    </small>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-4">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="card-title mb-0">
                                                    <i class="fas fa-info-circle me-2"></i>Sosyal Medya İpuçları
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <ul class="list-unstyled">
                                                    <li class="mb-2">
                                                        <i class="fas fa-check-circle text-success me-2"></i>
                                                        <small>Hesap linklerinizin doğru olduğundan emin olun</small>
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-check-circle text-success me-2"></i>
                                                        <small>URL'lerin https:// ile başladığından emin olun</small>
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-check-circle text-success me-2"></i>
                                                        <small>Sosyal medya hesaplarınızı düzenli güncelleyin</small>
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-check-circle text-success me-2"></i>
                                                        <small>İletişim bilgilerinizi güncel tutun</small>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i>Sosyal Medya Ayarlarını Kaydet
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
