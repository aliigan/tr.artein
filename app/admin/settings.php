<?php
/**
 * BuildTech CMS - Site Settings
 * Site ayarları paneli
 */

define('ADMIN_PANEL', true);
require_once 'config/config.php';
requireLogin();

$page_title = 'Site Ayarları';
$breadcrumb = [
    ['title' => 'Ana Sayfa', 'url' => 'dashboard.php'],
    ['title' => 'Site Ayarları']
];

// İşlemler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!validateCSRF($csrf_token)) {
        setErrorMessage('Güvenlik hatası. Lütfen tekrar deneyin.');
    } else {
        $settings = $_POST['settings'] ?? [];
        $updatedCount = 0;
        
        foreach ($settings as $key => $value) {
            if ($database->execute("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?", [$value, $key])) {
                $updatedCount++;
            }
        }
        
        if ($updatedCount > 0) {
            setSuccessMessage('Site ayarları başarıyla güncellendi.');
        } else {
            setErrorMessage('Ayarlar güncellenirken hata oluştu.');
        }
    }
}

// Ayarları getir
$settings = [];
$settingsData = $database->fetchAll("SELECT * FROM site_settings ORDER BY setting_key");
foreach ($settingsData as $setting) {
    $settings[$setting['setting_key']] = $setting;
}

include 'includes/header.php';
?>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION[CSRF_TOKEN_NAME] ?>">
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Genel Ayarlar -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Genel Ayarlar</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Site Başlığı</label>
                        <input type="text" class="form-control" name="settings[site_title]" 
                               value="<?= escape($settings['site_title']['setting_value'] ?? '') ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Site Açıklaması</label>
                        <textarea class="form-control" name="settings[site_description]" rows="3"><?= escape($settings['site_description']['setting_value'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Şirket Adı</label>
                        <input type="text" class="form-control" name="settings[company_name]" 
                               value="<?= escape($settings['company_name']['setting_value'] ?? '') ?>">
                    </div>
                </div>
            </div>
            
            <!-- İletişim Bilgileri -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-address-book me-2"></i>İletişim Bilgileri</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">E-posta</label>
                                <input type="email" class="form-control" name="settings[company_email]" 
                                       value="<?= escape($settings['company_email']['setting_value'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Telefon</label>
                                <input type="text" class="form-control" name="settings[company_phone]" 
                                       value="<?= escape($settings['company_phone']['setting_value'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Adres</label>
                        <textarea class="form-control" name="settings[company_address]" rows="3"><?= escape($settings['company_address']['setting_value'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
            
            <!-- Sosyal Medya -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-share-alt me-2"></i>Sosyal Medya</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Facebook URL</label>
                                <input type="url" class="form-control" name="settings[facebook_url]" 
                                       value="<?= escape($settings['facebook_url']['setting_value'] ?? '') ?>"
                                       placeholder="https://facebook.com/...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Instagram URL</label>
                                <input type="url" class="form-control" name="settings[instagram_url]" 
                                       value="<?= escape($settings['instagram_url']['setting_value'] ?? '') ?>"
                                       placeholder="https://instagram.com/...">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">LinkedIn URL</label>
                                <input type="url" class="form-control" name="settings[linkedin_url]" 
                                       value="<?= escape($settings['linkedin_url']['setting_value'] ?? '') ?>"
                                       placeholder="https://linkedin.com/company/...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">WhatsApp Numarası</label>
                                <input type="text" class="form-control" name="settings[whatsapp_number]" 
                                       value="<?= escape($settings['whatsapp_number']['setting_value'] ?? '') ?>"
                                       placeholder="+90 555 888 99 88">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">YouTube URL</label>
                                <input type="url" class="form-control" name="settings[youtube_url]" 
                                       value="<?= escape($settings['youtube_url']['setting_value'] ?? '') ?>"
                                       placeholder="https://youtube.com/channel/...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Twitter URL</label>
                                <input type="url" class="form-control" name="settings[twitter_url]" 
                                       value="<?= escape($settings['twitter_url']['setting_value'] ?? '') ?>"
                                       placeholder="https://twitter.com/...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Logo -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-image me-2"></i>Logo</h5>
                </div>
                <div class="card-body text-center">
                    <?php if (isset($settings['logo']) && $settings['logo']['setting_value']): ?>
                        <img src="../../<?= escape($settings['logo']['setting_value']) ?>" 
                             class="img-fluid mb-3" style="max-height: 100px;" alt="Logo">
                        <br>
                        <small class="text-muted">Mevcut Logo</small>
                    <?php else: ?>
                        <div class="bg-light p-4 mb-3">
                            <i class="fas fa-image fa-3x text-muted"></i>
                            <p class="text-muted mb-0">Logo yüklenmemiş</p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mt-3">
                        <input type="file" class="form-control" name="logo" accept="image/*">
                        <small class="form-text text-muted">PNG formatı önerilir</small>
                    </div>
                </div>
            </div>
            
            <!-- Hakkımızda İçeriği -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Hakkımızda</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Başlık</label>
                        <input type="text" class="form-control" name="settings[about_title]" 
                               value="<?= escape($settings['about_title']['setting_value'] ?? 'Hakkımızda') ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Kısa Açıklama</label>
                        <textarea class="form-control" name="settings[about_subtitle]" rows="3"><?= escape($settings['about_subtitle']['setting_value'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
            
            <!-- SEO Ayarları -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-search me-2"></i>SEO Ayarları</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Meta Anahtar Kelimeler</label>
                        <input type="text" class="form-control" name="settings[meta_keywords]" 
                               value="<?= escape($settings['meta_keywords']['setting_value'] ?? '') ?>"
                               placeholder="inşaat, mühendislik, yapı">
                        <small class="form-text text-muted">Virgül ile ayırın</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Google Analytics ID</label>
                        <input type="text" class="form-control" name="settings[google_analytics]" 
                               value="<?= escape($settings['google_analytics']['setting_value'] ?? '') ?>"
                               placeholder="G-XXXXXXXXXX">
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-between">
        <a href="dashboard.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Dashboard'a Dön
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>Ayarları Kaydet
        </button>
    </div>
</form>

<?php
// Eğer ayarlar tabloda yoksa ekle
$requiredSettings = [
    'social_facebook' => ['value' => '', 'type' => 'text', 'desc' => 'Facebook URL'],
    'social_instagram' => ['value' => '', 'type' => 'text', 'desc' => 'Instagram URL'],
    'social_linkedin' => ['value' => '', 'type' => 'text', 'desc' => 'LinkedIn URL'],
    'social_twitter' => ['value' => '', 'type' => 'text', 'desc' => 'Twitter URL'],
    'about_title' => ['value' => 'Hakkımızda', 'type' => 'text', 'desc' => 'Hakkımızda başlığı'],
    'about_subtitle' => ['value' => '', 'type' => 'textarea', 'desc' => 'Hakkımızda alt başlığı'],
    'meta_keywords' => ['value' => '', 'type' => 'text', 'desc' => 'Meta anahtar kelimeler'],
    'google_analytics' => ['value' => '', 'type' => 'text', 'desc' => 'Google Analytics ID']
];

foreach ($requiredSettings as $key => $setting) {
    if (!isset($settings[$key])) {
        $database->execute(
            "INSERT IGNORE INTO site_settings (setting_key, setting_value, setting_type, description) VALUES (?, ?, ?, ?)",
            [$key, $setting['value'], $setting['type'], $setting['desc']]
        );
    }
}

include 'includes/footer.php';
?>
