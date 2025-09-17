<?php
require_once './app/shared/config/frontend_config.php';

$database = new Database();

echo "=== PROJE RESİMLERİ KONTROLÜ ===\n\n";

// Projeleri getir
$projects = $database->fetchAll("SELECT id, title, featured_image FROM projects WHERE is_active = 1 LIMIT 5");

foreach($projects as $project) {
    echo "Proje ID: " . $project['id'] . "\n";
    echo "Başlık: " . $project['title'] . "\n";
    echo "Resim Yolu: " . $project['featured_image'] . "\n";
    
    // Dosya var mı kontrol et
    $fullPath = $project['featured_image'];
    if (file_exists($fullPath)) {
        echo "✅ Dosya mevcut: " . $fullPath . "\n";
    } else {
        echo "❌ Dosya bulunamadı: " . $fullPath . "\n";
        
        // Alternatif yolları kontrol et
        $altPath1 = "assets/" . $project['featured_image'];
        $altPath2 = "../" . $project['featured_image'];
        $altPath3 = "../../" . $project['featured_image'];
        
        echo "Alternatif 1 (assets/): " . ($altPath1 && file_exists($altPath1) ? "✅" : "❌") . " " . $altPath1 . "\n";
        echo "Alternatif 2 (../): " . ($altPath2 && file_exists($altPath2) ? "✅" : "❌") . " " . $altPath2 . "\n";
        echo "Alternatif 3 (../../): " . ($altPath3 && file_exists($altPath3) ? "✅" : "❌") . " " . $altPath3 . "\n";
    }
    echo "---\n";
}

// Uploads klasörünü kontrol et
echo "\n=== UPLOADS KLASÖRÜ KONTROLÜ ===\n";
$uploadsDir = "assets/uploads/projects/";
if (is_dir($uploadsDir)) {
    $files = scandir($uploadsDir);
    echo "Uploads klasörü mevcut. İçindeki dosyalar:\n";
    foreach($files as $file) {
        if($file != '.' && $file != '..') {
            echo "- " . $file . "\n";
        }
    }
} else {
    echo "❌ Uploads klasörü bulunamadı: " . $uploadsDir . "\n";
}
?>
