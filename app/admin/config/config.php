<?php
/**
 * BuildTech CMS - Main Configuration
 * Ana konfigürasyon dosyası
 */

// Hata raporlama (geliştirme ortamı için)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone ayarı
date_default_timezone_set('Europe/Istanbul');

// Session ayarları
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // HTTPS için 1 yapın
ini_set('session.use_strict_mode', 1);

// Site ayarları
define('SITE_URL', 'http://localhost/tr.artein');
define('ADMIN_URL', SITE_URL . '/app/admin');
define('UPLOAD_PATH', __DIR__ . '/../../../assets/uploads/');
define('UPLOAD_URL', SITE_URL . '/assets/uploads');
define('MAX_FILE_SIZE', 100 * 1024 * 1024); // 100MB

// Güvenlik ayarları
define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_TIMEOUT', 3600); // 1 saat

// Medya ayarları
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('ALLOWED_VIDEO_TYPES', ['mp4', 'avi', 'mov', 'webm', 'mkv']);
define('ALLOWED_MEDIA_TYPES', array_merge(ALLOWED_IMAGE_TYPES, ALLOWED_VIDEO_TYPES));
define('MAX_IMAGE_WIDTH', 1920);
define('MAX_IMAGE_HEIGHT', 1080);
define('THUMBNAIL_WIDTH', 300);
define('THUMBNAIL_HEIGHT', 200);

// Sayfalama ayarları
define('ITEMS_PER_PAGE', 10);

// E-posta ayarları (isteğe bağlı)
define('SMTP_HOST', 'localhost');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('FROM_EMAIL', 'noreply@buildtech.com');
define('FROM_NAME', 'BuildTech CMS');

// Veritabanı bağlantısını dahil et
require_once __DIR__ . '/database.php';

// Yardımcı fonksiyonları dahil et
require_once __DIR__ . '/functions.php';

// Session başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CSRF token oluştur
if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
    $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
}
?>
