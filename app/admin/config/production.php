<?php
/**
 * Production Configuration
 * Canlı sunucu için güvenlik ayarları
 */

// PRODUCTION ORTAMI İÇİN GÜVENLİK AYARLARI
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../../storage/logs/error.log');

// Timezone ayarı
date_default_timezone_set('Europe/Istanbul');

// Session güvenlik ayarları (HTTPS için)
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // HTTPS zorunlu
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', 1);
ini_set('session.use_only_cookies', 1);

// !! BURALAR DEĞİŞTİRİLMELİ !!
define('SITE_URL', 'https://artein.tr'); // Gerçek domain
define('ADMIN_URL', SITE_URL . '/app/admin');
define('UPLOAD_PATH', __DIR__ . '/../../../assets/uploads/');
define('UPLOAD_URL', SITE_URL . '/assets/uploads');
define('MAX_FILE_SIZE', 50 * 1024 * 1024); // Production'da 50MB

// Güvenlik ayarları
define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_TIMEOUT', 1800); // 30 dakika (production'da daha kısa)

// Medya ayarları
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'webp']); // GIF kaldırıldı
define('ALLOWED_VIDEO_TYPES', ['mp4', 'webm']);
define('ALLOWED_MEDIA_TYPES', array_merge(ALLOWED_IMAGE_TYPES, ALLOWED_VIDEO_TYPES));
define('MAX_IMAGE_WIDTH', 1920);
define('MAX_IMAGE_HEIGHT', 1080);
define('THUMBNAIL_WIDTH', 300);
define('THUMBNAIL_HEIGHT', 200);

// Sayfalama ayarları
define('ITEMS_PER_PAGE', 10);

// E-posta ayarları (gerçek SMTP bilgileri)
define('SMTP_HOST', 'mail.artein.tr'); // Gerçek SMTP
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'info@artein.tr'); // Gerçek email
define('SMTP_PASSWORD', 'arteinHK24!'); // Gerçek şifre
define('FROM_EMAIL', 'info@artein.tr');
define('FROM_NAME', 'Arte In Engineering');

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

// Production güvenlik header'ları
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// CSP Header (Content Security Policy)
$csp = "default-src 'self'; ";
$csp .= "script-src 'self' 'unsafe-inline' https://code.jquery.com https://cdn.jsdelivr.net; ";
$csp .= "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; ";
$csp .= "img-src 'self' data:; ";
$csp .= "font-src 'self' https://cdn.jsdelivr.net;";
header("Content-Security-Policy: $csp");
?>

