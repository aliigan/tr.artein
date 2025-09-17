<?php
/**
 * BuildTech CMS - Hizmet Sıralaması Güncelleme
 * AJAX ile hizmet sıralaması güncelleme
 */

define('ADMIN_PANEL', true);
require_once '../config/config.php';

header('Content-Type: application/json');

// Sadece POST isteklerine izin ver
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Sadece POST isteklerine izin verilir.']);
    exit;
}

// Admin giriş kontrolü
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Yetkilendirme gerekli.']);
    exit;
}

// CSRF token kontrolü
$csrf_token = $_POST['csrf_token'] ?? '';
if (!validateCSRF($csrf_token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Güvenlik hatası.']);
    exit;
}

// Sıralama verisi
$order = $_POST['order'] ?? [];

if (empty($order) || !is_array($order)) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz sıralama verisi.']);
    exit;
}

try {
    // Her bir hizmetin sırasını güncelle
    foreach ($order as $index => $service_id) {
        $service_id = (int)$service_id;
        $order_index = $index + 1;
        
        $sql = "UPDATE services SET order_index = ?, updated_at = NOW() WHERE id = ?";
        $database->execute($sql, [$order_index, $service_id]);
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'Sıralama başarıyla güncellendi.',
        'updated_count' => count($order)
    ]);
    
} catch (Exception $e) {
    error_log("Service order update error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Sıralama güncellenirken bir hata oluştu.']);
}
?>
