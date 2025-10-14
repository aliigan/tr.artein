<?php
/**
 * BuildTech CMS - Frontend Configuration
 * Frontend için veritabanı bağlantısı ve yardımcı fonksiyonlar
 */

// Session başlatma frontend için gerekli değil
// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }

// Admin paneli config dosyasını dahil et
require_once __DIR__ . '/../../admin/config/database.php';
require_once __DIR__ . '/../../admin/config/functions.php';

// Frontend için güvenlik ayarları
ini_set('display_errors', 0); // Canlı sunucuda hataları gizle

/**
 * Site ayarlarını getir
 */
function getSiteSettings() {
    global $database;
    static $settings = null;
    
    if ($settings === null) {
        $settings = [];
        $settingsData = $database->fetchAll("SELECT setting_key, setting_value FROM site_settings");
        foreach ($settingsData as $setting) {
            $settings[$setting['setting_key']] = $setting['setting_value'];
        }
    }
    
    return $settings;
}

/**
 * Tek bir site ayarını getir
 */
function getSetting($key, $default = '') {
    $settings = getSiteSettings();
    return $settings[$key] ?? $default;
}

/**
 * Aktif slider'ları getir
 */
function getActiveSliders() {
    global $database;
    return $database->fetchAll("SELECT * FROM sliders WHERE is_active = 1 ORDER BY order_index ASC");
}

/**
 * Aktif manifesto slider'ları getir
 */
function getActiveManifestoSliders() {
    global $database;
    return $database->fetchAll("SELECT * FROM manifesto_sliders WHERE is_active = 1 ORDER BY order_index ASC");
}

/**
 * Hakkımızda içeriğini getir
 */
function getAboutContent() {
    global $database;
    return $database->fetchOne("SELECT * FROM about_content ORDER BY id DESC LIMIT 1");
}

/**
 * Aktif hizmetleri getir
 */
function getActiveServices() {
    global $database;
    return $database->fetchAll("SELECT * FROM services WHERE is_active = 1 ORDER BY order_index ASC");
}

/**
 * Aktif projeleri getir
 */
function getActiveProjects($limit = null, $featured_only = false) {
    global $database;
    
    $where = "WHERE is_active = 1";
    if ($featured_only) {
        $where .= " AND is_featured = 1";
    }
    
    $limitClause = $limit ? "LIMIT $limit" : "";
    
    return $database->fetchAll("SELECT * FROM projects $where ORDER BY order_index ASC, created_at DESC $limitClause");
}

/**
 * Proje detayını slug ile getir
 */
function getProjectBySlug($slug) {
    global $database;
    return $database->fetchOne("SELECT * FROM projects WHERE slug = ? AND is_active = 1", [$slug]);
}

/**
 * Proje görsellerini getir
 */
function getProjectImages($project_id) {
    global $database;
    return $database->fetchAll("SELECT * FROM project_images WHERE project_id = ? ORDER BY order_index ASC", [$project_id]);
}

/**
 * İletişim mesajı kaydet
 */
function saveContactMessage($name, $email, $phone, $subject, $message) {
    global $database;
    
    // Debug: Database bağlantısını kontrol et
    if (!isset($database)) {
        error_log("saveContactMessage - Database object not found!");
        return false;
    }
    
    $sql = "INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)";
    
    // Hata ayıklama için loglama
    error_log("saveContactMessage called with: " . print_r([$name, $email, $phone, $subject, $message], true));
    error_log("SQL: " . $sql);
    
    try {
        $result = $database->execute($sql, [$name, $email, $phone, $subject, $message]);
        error_log("Database execute result: " . ($result ? 'true' : 'false'));
        
        if ($result) {
            error_log("Contact message inserted successfully");
            // İstatistik güncelle
            $today = date('Y-m-d');
            $statsResult = $database->execute("INSERT INTO site_stats (stat_date, contact_forms) VALUES (?, 1) 
                               ON DUPLICATE KEY UPDATE contact_forms = contact_forms + 1", [$today]);
            error_log("Stats update result: " . ($statsResult ? 'true' : 'false'));
            return true;
        } else {
            error_log("Contact message insert failed - execute returned false");
        }
    } catch (Exception $e) {
        error_log("Contact message insert exception: " . $e->getMessage());
    }
    
    return false;
}

/**
 * Sayfa görüntülenme istatistiği ekle
 */
function trackPageView() {
    global $database;
    
    $today = date('Y-m-d');
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    
    // Bugünkü istatistik var mı kontrol et
    $stat = $database->fetchOne("SELECT * FROM site_stats WHERE stat_date = ?", [$today]);
    
    if ($stat) {
        // Sayfa görüntülenmesini artır
        $database->execute("UPDATE site_stats SET page_views = page_views + 1 WHERE stat_date = ?", [$today]);
        
        // Benzersiz ziyaretçi kontrolü (basit IP tabanlı)
        if (!isset($_SESSION['visitor_tracked_' . $today])) {
            $database->execute("UPDATE site_stats SET unique_visitors = unique_visitors + 1 WHERE stat_date = ?", [$today]);
            $_SESSION['visitor_tracked_' . $today] = true;
        }
    } else {
        // Yeni gün için kayıt oluştur
        $database->execute("INSERT INTO site_stats (stat_date, page_views, unique_visitors) VALUES (?, 1, 1)", [$today]);
        $_SESSION['visitor_tracked_' . $today] = true;
    }
}

/**
 * Breadcrumb oluştur
 */
function generateBreadcrumb($items) {
    $html = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
    
    foreach ($items as $index => $item) {
        $isLast = ($index === count($items) - 1);
        
        if ($isLast) {
            $html .= '<li class="breadcrumb-item active">' . escape($item['title']) . '</li>';
        } else {
            $html .= '<li class="breadcrumb-item"><a href="' . escape($item['url']) . '">' . escape($item['title']) . '</a></li>';
        }
    }
    
    $html .= '</ol></nav>';
    return $html;
}

/**
 * Meta etiketlerini oluştur
 */
function generateMetaTags($title = '', $description = '', $keywords = '', $image = '') {
    $settings = getSiteSettings();
    
    $siteTitle = $settings['site_title'] ?? 'BuildTech Engineering';
    $siteDescription = $settings['site_description'] ?? 'Modern İnşaat Çözümleri';
    $siteKeywords = $settings['meta_keywords'] ?? '';
    
    $finalTitle = $title ? $title . ' - ' . $siteTitle : $siteTitle;
    $finalDescription = $description ?: $siteDescription;
    $finalKeywords = $keywords ?: $siteKeywords;
    
    $html = '<title>' . escape($finalTitle) . '</title>' . "\n";
    $html .= '<meta name="description" content="' . escape($finalDescription) . '">' . "\n";
    
    if ($finalKeywords) {
        $html .= '<meta name="keywords" content="' . escape($finalKeywords) . '">' . "\n";
    }
    
    // Open Graph etiketleri
    $html .= '<meta property="og:title" content="' . escape($finalTitle) . '">' . "\n";
    $html .= '<meta property="og:description" content="' . escape($finalDescription) . '">' . "\n";
    $html .= '<meta property="og:type" content="website">' . "\n";
    
    if ($image) {
        $html .= '<meta property="og:image" content="' . escape($image) . '">' . "\n";
    }
    
    return $html;
}

/**
 * Sosyal medya linklerini oluştur
 */
function getSocialMediaLinks() {
    $settings = getSiteSettings();
    $links = [];
    
    $socialPlatforms = [
        'facebook' => ['icon' => 'fab fa-facebook-f', 'name' => 'Facebook'],
        'instagram' => ['icon' => 'fab fa-instagram', 'name' => 'Instagram'],
        'linkedin' => ['icon' => 'fab fa-linkedin-in', 'name' => 'LinkedIn'],
        'twitter' => ['icon' => 'fab fa-twitter', 'name' => 'Twitter']
    ];
    
    foreach ($socialPlatforms as $platform => $data) {
        $url = $settings['social_' . $platform] ?? '';
        if ($url) {
            $links[] = [
                'url' => $url,
                'icon' => $data['icon'],
                'name' => $data['name']
            ];
        }
    }
    
    return $links;
}

// Sayfa görüntülenme istatistiği (frontend için devre dışı)
// trackPageView();
?>
