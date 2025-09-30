<?php
define('ADMIN_PANEL', true);
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'config/functions.php';

// Session başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Oturum kontrolü
requireLogin();

// CSRF token oluştur
if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
    $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
}

$id = 1; // Tek kayıt için sabit ID

// Form işlemleri
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!validateCSRF($csrf_token)) {
        setErrorMessage('Güvenlik hatası. Lütfen tekrar deneyin.');
        header('Location: about-us.php');
        exit;
    }

    if (isset($_POST['update_content'])) {
        // İçerik güncelleme
        $title = trim($_POST['title']);
        $subtitle = trim($_POST['subtitle']);
        $content = trim($_POST['content']);
        
        if (empty($title)) {
            setErrorMessage('Başlık alanı zorunludur.');
        } else {
            // Görsel yükleme
            $image_path = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_result = uploadFile($_FILES['image'], 'about');
                if ($upload_result['success']) {
                    $image_path = $upload_result['path'];
                } else {
                    setErrorMessage('Görsel yüklenirken hata: ' . $upload_result['message']);
                }
            }
            
            // Veritabanını güncelle
            try {
                $existing = $database->fetchOne("SELECT id FROM about_us_content WHERE id = ?", [$id]);
                
                if ($existing) {
                    // Güncelleme
                    if ($image_path) {
                        $result = $database->execute(
                            "UPDATE about_us_content SET title = ?, subtitle = ?, content = ?, image = ? WHERE id = ?",
                            [$title, $subtitle, $content, $image_path, $id]
                        );
                    } else {
                        $result = $database->execute(
                            "UPDATE about_us_content SET title = ?, subtitle = ?, content = ? WHERE id = ?",
                            [$title, $subtitle, $content, $id]
                        );
                    }
                } else {
                    // Yeni kayıt ekle
                    if ($image_path) {
                        $result = $database->execute(
                            "INSERT INTO about_us_content (id, title, subtitle, content, image) VALUES (?, ?, ?, ?, ?)",
                            [$id, $title, $subtitle, $content, $image_path]
                        );
                    } else {
                        $result = $database->execute(
                            "INSERT INTO about_us_content (id, title, subtitle, content) VALUES (?, ?, ?, ?)",
                            [$id, $title, $subtitle, $content]
                        );
                    }
                }
                
                if ($result) {
                    setSuccessMessage('İçerik başarıyla kaydedildi.');
                } else {
                    setErrorMessage('İçerik kaydedilirken hata oluştu.');
                }
            } catch (Exception $e) {
                setErrorMessage('Veritabanı hatası: ' . $e->getMessage());
            }
        }
    }
    
    header('Location: about-us.php');
    exit;
}

// Mevcut içeriği al
$about_content = $database->fetchOne("SELECT * FROM about_us_content WHERE id = ?", [$id]) ?: [
    'title' => 'Hakkımızda',
    'subtitle' => 'Arte In olarak inşaat sektöründe kalite ve güvenin adıyız',
    'content' => '<p>Biz Arte In olarak, inşaat sektöründe sadece binalar inşa etmiyoruz; yaşam alanları yaratıyoruz. Her projemizde sanat ve mühendisliğin mükemmel uyumunu sağlayarak, müşterilerimizin hayallerini gerçeğe dönüştürüyoruz.</p>',
    'image' => 'assets/images/about.jpg'
];

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-users me-2"></i>
                    Hakkımızda Yönetimi
                </h1>
            </div>

            <?php displayMessages(); ?>

            <!-- İçerik Düzenleme Formu -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        İçerik Düzenleme
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION[CSRF_TOKEN_NAME] ?>">
                        <input type="hidden" name="update_content" value="1">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Başlık</label>
                                    <input type="text" class="form-control" name="title" 
                                           value="<?= escape($about_content['title']) ?>" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Alt Başlık</label>
                                    <input type="text" class="form-control" name="subtitle" 
                                           value="<?= escape($about_content['subtitle']) ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">İçerik</label>
                            <textarea class="form-control summernote" name="content" 
                                      placeholder="Hakkımızda sayfası içeriği..."><?= escape($about_content['content']) ?></textarea>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Zengin metin editörü ile içeriğinizi düzenleyebilirsiniz. Paragraflar, başlıklar, kalın yazı ve diğer formatlamalar desteklenir.
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Ana Görsel</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                            <?php if ($about_content['image']): ?>
                                <div class="mt-2">
                                    <small class="text-muted">Mevcut görsel:</small><br>
                                    <img src="../../<?= escape($about_content['image']) ?>" 
                                         class="img-thumbnail mt-1" style="max-width: 200px; max-height: 150px;" 
                                         alt="Mevcut görsel">
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Önizleme -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-eye me-2"></i>
                        Önizleme
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h3><?= escape($about_content['title']) ?></h3>
                            <?php if ($about_content['subtitle']): ?>
                                <p class="lead"><?= escape($about_content['subtitle']) ?></p>
                            <?php endif; ?>
                            <div class="content-preview">
                                <?= $about_content['content'] ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <?php if ($about_content['image']): ?>
                                <img src="../../<?= escape($about_content['image']) ?>" 
                                     class="img-fluid rounded" alt="Önizleme">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.content-preview {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    padding: 1rem;
    border-radius: 0.375rem;
    background: #f8f9fa;
}

.content-preview p {
    margin-bottom: 1rem;
}

.content-preview p:last-child {
    margin-bottom: 0;
}
</style>

<?php include 'includes/footer.php'; ?>