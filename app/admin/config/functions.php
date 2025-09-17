<?php
/**
 * BuildTech CMS - Helper Functions
 * Yardımcı fonksiyonlar
 */

/**
 * Güvenli şekilde veri çıktısı alma
 */
function escape($data) {
    if ($data === null) {
        return '';
    }
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * CSRF token doğrulama
 */
function validateCSRF($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * Kullanıcı giriş kontrolü
 */
function isLoggedIn() {
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_username']);
}

/**
 * Admin sayfalarına erişim kontrolü
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . ADMIN_URL . '/login.php');
        exit;
    }
    
    // Session timeout kontrolü
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        session_destroy();
        header('Location: ' . ADMIN_URL . '/login.php?timeout=1');
        exit;
    }
    
    $_SESSION['last_activity'] = time();
}

/**
 * Başarı mesajı gösterme
 */
function setSuccessMessage($message) {
    $_SESSION['success_message'] = $message;
}

/**
 * Hata mesajı gösterme
 */
function setErrorMessage($message) {
    $_SESSION['error_message'] = $message;
}

/**
 * Mesajları göster ve temizle
 */
function displayMessages() {
    $html = '';
    
    if (isset($_SESSION['success_message'])) {
        $html .= '<div class="alert alert-success alert-dismissible fade show" role="alert">';
        $html .= escape($_SESSION['success_message']);
        $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        unset($_SESSION['success_message']);
    }
    
    if (isset($_SESSION['error_message'])) {
        $html .= '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
        $html .= escape($_SESSION['error_message']);
        $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        unset($_SESSION['error_message']);
    }
    
    return $html;
}

/**
 * Dosya yükleme fonksiyonu
 */
function uploadFile($file, $directory = 'general') {
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Dosya yükleme hatası.'];
    }
    
    // Dosya boyutu kontrolü
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'Dosya boyutu çok büyük.'];
    }
    
    // Dosya türü kontrolü
    $fileInfo = pathinfo($file['name']);
    $extension = strtolower($fileInfo['extension']);
    
    if (!in_array($extension, ALLOWED_IMAGE_TYPES)) {
        return ['success' => false, 'message' => 'Desteklenmeyen dosya türü.'];
    }
    
    // Upload klasörü oluştur
    $uploadDir = UPLOAD_PATH . $directory . '/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Benzersiz dosya adı oluştur
    $fileName = uniqid() . '_' . time() . '.' . $extension;
    $filePath = $uploadDir . $fileName;
    
    // Dosyayı taşı
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        // Resim boyutlandırma (isteğe bağlı)
        resizeImage($filePath, MAX_IMAGE_WIDTH, MAX_IMAGE_HEIGHT);
        
        $relativePath = 'assets/uploads/' . $directory . '/' . $fileName;
        return [
            'success' => true, 
            'path' => $relativePath,
            'filename' => $fileName,
            'original_name' => $file['name'],
            'size' => $file['size']
        ];
    }
    
    return ['success' => false, 'message' => 'Dosya yüklenemedi.'];
}

/**
 * Resim boyutlandırma
 */
function resizeImage($imagePath, $maxWidth, $maxHeight) {
    $imageInfo = getimagesize($imagePath);
    if (!$imageInfo) return false;
    
    $originalWidth = $imageInfo[0];
    $originalHeight = $imageInfo[1];
    $imageType = $imageInfo[2];
    
    // Boyutlandırma gerekli mi?
    if ($originalWidth <= $maxWidth && $originalHeight <= $maxHeight) {
        return true;
    }
    
    // Orantılı boyutları hesapla
    $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
    $newWidth = intval($originalWidth * $ratio);
    $newHeight = intval($originalHeight * $ratio);
    
    // Kaynak resmi oluştur
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $sourceImage = imagecreatefromjpeg($imagePath);
            break;
        case IMAGETYPE_PNG:
            $sourceImage = imagecreatefrompng($imagePath);
            break;
        case IMAGETYPE_GIF:
            $sourceImage = imagecreatefromgif($imagePath);
            break;
        default:
            return false;
    }
    
    // Yeni resim oluştur
    $newImage = imagecreatetruecolor($newWidth, $newHeight);
    
    // PNG için şeffaflık koruması
    if ($imageType == IMAGETYPE_PNG) {
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
    }
    
    // Resmi yeniden boyutlandır
    imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
    
    // Resmi kaydet
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            imagejpeg($newImage, $imagePath, 85);
            break;
        case IMAGETYPE_PNG:
            imagepng($newImage, $imagePath);
            break;
        case IMAGETYPE_GIF:
            imagegif($newImage, $imagePath);
            break;
    }
    
    // Belleği temizle
    imagedestroy($sourceImage);
    imagedestroy($newImage);
    
    return true;
}

/**
 * Slug oluşturma
 */
function createSlug($text) {
    // Türkçe karakterleri değiştir
    $turkish = ['ç', 'ğ', 'ı', 'İ', 'ö', 'ş', 'ü', 'Ç', 'Ğ', 'Ö', 'Ş', 'Ü'];
    $english = ['c', 'g', 'i', 'I', 'o', 's', 'u', 'C', 'G', 'O', 'S', 'U'];
    $text = str_replace($turkish, $english, $text);
    
    // Küçük harfe çevir ve özel karakterleri temizle
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    $text = trim($text, '-');
    
    return $text;
}

/**
 * Tarih formatı
 */
function formatDate($date, $format = 'd.m.Y H:i') {
    return date($format, strtotime($date));
}

/**
 * Dosya boyutu formatı
 */
function formatFileSize($size) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $power = $size > 0 ? floor(log($size, 1024)) : 0;
    return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
}

/**
 * Sayfalama
 */
function generatePagination($currentPage, $totalPages, $baseUrl) {
    if ($totalPages <= 1) return '';
    
    $html = '<nav aria-label="Sayfa navigasyonu"><ul class="pagination justify-content-center">';
    
    // Önceki sayfa
    if ($currentPage > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=' . ($currentPage - 1) . '">Önceki</a></li>';
    }
    
    // Sayfa numaraları
    $start = max(1, $currentPage - 2);
    $end = min($totalPages, $currentPage + 2);
    
    for ($i = $start; $i <= $end; $i++) {
        $active = $i == $currentPage ? ' active' : '';
        $html .= '<li class="page-item' . $active . '"><a class="page-link" href="' . $baseUrl . '?page=' . $i . '">' . $i . '</a></li>';
    }
    
    // Sonraki sayfa
    if ($currentPage < $totalPages) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=' . ($currentPage + 1) . '">Sonraki</a></li>';
    }
    
    $html .= '</ul></nav>';
    return $html;
}
?>
