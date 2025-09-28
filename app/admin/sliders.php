<?php
/**
 * BuildTech CMS - Slider Management
 * Slider yönetimi sayfası
 */

define('ADMIN_PANEL', true);
require_once 'config/config.php';
requireLogin();

$page_title = 'Slider Yönetimi';
$breadcrumb = [
    ['title' => 'Ana Sayfa', 'url' => 'dashboard.php'],
    ['title' => 'Slider Yönetimi']
];

$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? 0;

// İşlemler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!validateCSRF($csrf_token)) {
        setErrorMessage('Güvenlik hatası. Lütfen tekrar deneyin.');
    } else {
        switch ($action) {
            case 'add':
                $title = trim($_POST['title'] ?? '');
                $subtitle = trim($_POST['subtitle'] ?? '');
                $button_text = trim($_POST['button_text'] ?? '');
                $button_link = trim($_POST['button_link'] ?? '');
                $order_index = (int)($_POST['order_index'] ?? 0);
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                
                if (empty($title)) {
                    setErrorMessage('Başlık alanı zorunludur.');
                } else {
                    $background_image = '';
                    
                    // Resim yükleme
                    if (isset($_FILES['background_image']) && $_FILES['background_image']['error'] === UPLOAD_ERR_OK) {
                        $upload = uploadFile($_FILES['background_image'], 'sliders');
                        if ($upload['success']) {
                            $background_image = $upload['path'];
                        } else {
                            setErrorMessage('Resim yükleme hatası: ' . $upload['message']);
                            break;
                        }
                    } elseif (isset($_FILES['background_image']) && $_FILES['background_image']['error'] !== UPLOAD_ERR_NO_FILE) {
                        // Upload hatası var
                        $error_messages = [
                            UPLOAD_ERR_INI_SIZE => 'Dosya boyutu çok büyük (PHP limiti: ' . ini_get('upload_max_filesize') . ')',
                            UPLOAD_ERR_FORM_SIZE => 'Dosya boyutu çok büyük (Form limiti)',
                            UPLOAD_ERR_PARTIAL => 'Dosya kısmen yüklendi',
                            UPLOAD_ERR_NO_TMP_DIR => 'Geçici klasör bulunamadı',
                            UPLOAD_ERR_CANT_WRITE => 'Dosya yazılamadı',
                            UPLOAD_ERR_EXTENSION => 'Dosya yükleme uzantı tarafından durduruldu'
                        ];
                        $error_msg = $error_messages[$_FILES['background_image']['error']] ?? 'Bilinmeyen yükleme hatası';
                        setErrorMessage('Resim yükleme hatası: ' . $error_msg);
                        break;
                    }
                    
                    $sql = "INSERT INTO sliders (title, subtitle, button_text, button_link, background_image, order_index, is_active) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)";
                    
                    if ($database->execute($sql, [$title, $subtitle, $button_text, $button_link, $background_image, $order_index, $is_active])) {
                        setSuccessMessage('Slider başarıyla eklendi.');
                        header('Location: sliders.php?action=list');
                        exit;
                    } else {
                        setErrorMessage('Slider eklenirken hata oluştu.');
                    }
                }
                break;
                
            case 'edit':
                $title = trim($_POST['title'] ?? '');
                $subtitle = trim($_POST['subtitle'] ?? '');
                $button_text = trim($_POST['button_text'] ?? '');
                $button_link = trim($_POST['button_link'] ?? '');
                $order_index = (int)($_POST['order_index'] ?? 0);
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                
                if (empty($title)) {
                    setErrorMessage('Başlık alanı zorunludur.');
                } else {
                    // Mevcut kaydı al
                    $slider = $database->fetchOne("SELECT * FROM sliders WHERE id = ?", [$id]);
                    if (!$slider) {
                        setErrorMessage('Slider bulunamadı.');
                        break;
                    }
                    
                    $background_image = $slider['background_image'];
                    
                    // Yeni resim yüklendi mi?
                    if (isset($_FILES['background_image']) && $_FILES['background_image']['error'] === UPLOAD_ERR_OK) {
                        $upload = uploadFile($_FILES['background_image'], 'sliders');
                        if ($upload['success']) {
                            // Eski resmi sil
                            if ($background_image && file_exists($background_image)) {
                                unlink($background_image);
                            }
                            $background_image = $upload['path'];
                        } else {
                            setErrorMessage('Resim yükleme hatası: ' . $upload['message']);
                            break;
                        }
                    } elseif (isset($_FILES['background_image']) && $_FILES['background_image']['error'] !== UPLOAD_ERR_NO_FILE) {
                        // Upload hatası var
                        $error_messages = [
                            UPLOAD_ERR_INI_SIZE => 'Dosya boyutu çok büyük (PHP limiti: ' . ini_get('upload_max_filesize') . ')',
                            UPLOAD_ERR_FORM_SIZE => 'Dosya boyutu çok büyük (Form limiti)',
                            UPLOAD_ERR_PARTIAL => 'Dosya kısmen yüklendi',
                            UPLOAD_ERR_NO_TMP_DIR => 'Geçici klasör bulunamadı',
                            UPLOAD_ERR_CANT_WRITE => 'Dosya yazılamadı',
                            UPLOAD_ERR_EXTENSION => 'Dosya yükleme uzantı tarafından durduruldu'
                        ];
                        $error_msg = $error_messages[$_FILES['background_image']['error']] ?? 'Bilinmeyen yükleme hatası';
                        setErrorMessage('Resim yükleme hatası: ' . $error_msg);
                        break;
                    }
                    
                    $sql = "UPDATE sliders SET title = ?, subtitle = ?, button_text = ?, button_link = ?, 
                            background_image = ?, order_index = ?, is_active = ? WHERE id = ?";
                    
                    if ($database->execute($sql, [$title, $subtitle, $button_text, $button_link, $background_image, $order_index, $is_active, $id])) {
                        setSuccessMessage('Slider başarıyla güncellendi.');
                        header('Location: sliders.php?action=list');
                        exit;
                    } else {
                        setErrorMessage('Slider güncellenirken hata oluştu.');
                    }
                }
                break;
                
            case 'delete':
                $slider = $database->fetchOne("SELECT * FROM sliders WHERE id = ?", [$id]);
                if ($slider) {
                    // Resmi sil
                    if ($slider['background_image'] && file_exists($slider['background_image'])) {
                        unlink($slider['background_image']);
                    }
                    
                    if ($database->execute("DELETE FROM sliders WHERE id = ?", [$id])) {
                        setSuccessMessage('Slider başarıyla silindi.');
                    } else {
                        setErrorMessage('Slider silinirken hata oluştu.');
                    }
                } else {
                    setErrorMessage('Slider bulunamadı.');
                }
                header('Location: sliders.php?action=list');
                exit;
                break;
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'delete') {
    // GET request ile silme işlemi
    $slider = $database->fetchOne("SELECT * FROM sliders WHERE id = ?", [$id]);
    if ($slider) {
        // Resmi sil
        if ($slider['background_image'] && file_exists($slider['background_image'])) {
            unlink($slider['background_image']);
        }
        
        if ($database->execute("DELETE FROM sliders WHERE id = ?", [$id])) {
            setSuccessMessage('Slider başarıyla silindi.');
        } else {
            setErrorMessage('Slider silinirken hata oluştu.');
        }
    } else {
        setErrorMessage('Slider bulunamadı.');
    }
    header('Location: sliders.php?action=list');
    exit;
}

// Sayfa içeriği
switch ($action) {
    case 'add':
        $breadcrumb[] = ['title' => 'Yeni Slider'];
        break;
    case 'edit':
        $slider = $database->fetchOne("SELECT * FROM sliders WHERE id = ?", [$id]);
        if (!$slider) {
            setErrorMessage('Slider bulunamadı.');
            header('Location: sliders.php?action=list');
            exit;
        }
        $breadcrumb[] = ['title' => 'Slider Düzenle'];
        break;
    default:
        $sliders = $database->fetchAll("SELECT * FROM sliders ORDER BY order_index ASC, created_at DESC");
        break;
}

include 'includes/header.php';
?>

<?php if ($action === 'list'): ?>
    <!-- Slider Listesi -->
    <?= displayMessages() ?>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-images me-2"></i>Slider Listesi</h5>
            <a href="sliders.php?action=add" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Yeni Slider Ekle
            </a>
        </div>
        <div class="card-body">
            <?php if (empty($sliders)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-images fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Henüz slider bulunmuyor</h5>
                    <p class="text-muted">İlk slider'ınızı eklemek için yukarıdaki butona tıklayın.</p>
                    <a href="sliders.php?action=add" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Yeni Slider Ekle
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="80">Görsel</th>
                                <th>Başlık</th>
                                <th>Alt Başlık</th>
                                <th width="100">Sıra</th>
                                <th width="100">Durum</th>
                                <th width="120">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sliders as $slider): ?>
                                <tr>
                                    <td>
                                        <?php if ($slider['background_image']): ?>
                                            <img src="../../<?= escape($slider['background_image']) ?>" 
                                                 class="img-thumbnail" style="width: 60px; height: 40px; object-fit: cover;" 
                                                 alt="<?= escape($slider['title']) ?>">
                                        <?php else: ?>
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 40px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?= escape($slider['title']) ?></strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= escape(substr($slider['subtitle'] ?? '', 0, 50)) ?>
                                            <?= strlen($slider['subtitle'] ?? '') > 50 ? '...' : '' ?>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?= $slider['order_index'] ?></span>
                                    </td>
                                    <td>
                                        <?php if ($slider['is_active']): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Pasif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="sliders.php?action=edit&id=<?= $slider['id'] ?>" 
                                               class="btn btn-outline-primary" title="Düzenle">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="sliders.php?action=delete&id=<?= $slider['id'] ?>" 
                                               class="btn btn-outline-danger" title="Sil"
                                               onclick="return confirmDelete('Bu slider\'ı silmek istediğinizden emin misiniz?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php else: ?>
    <!-- Slider Ekleme/Düzenleme Formu -->
    <?= displayMessages() ?>
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-<?= $action === 'add' ? 'plus' : 'edit' ?> me-2"></i>
                <?= $action === 'add' ? 'Yeni Slider Ekle' : 'Slider Düzenle' ?>
            </h5>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION[CSRF_TOKEN_NAME] ?>">
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">Başlık <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" 
                                   value="<?= escape($slider['title'] ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Alt Başlık</label>
                            <textarea class="form-control" name="subtitle" rows="3"><?= escape($slider['subtitle'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Buton Metni</label>
                                    <input type="text" class="form-control" name="button_text" 
                                           value="<?= escape($slider['button_text'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Buton Linki</label>
                                    <input type="text" class="form-control" name="button_link" 
                                           value="<?= escape($slider['button_link'] ?? '') ?>" 
                                           placeholder="#contact, #about, vb.">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Sıra</label>
                                    <input type="number" class="form-control" name="order_index" 
                                           value="<?= escape($slider['order_index'] ?? 0) ?>" min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch mt-4">
                                        <input type="checkbox" class="form-check-input" name="is_active" 
                                               <?= ($slider['is_active'] ?? 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label">Aktif</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Arka Plan Görseli</label>
                            <input type="file" class="form-control" name="background_image" 
                                   accept="image/*" onchange="previewImage(this, 'imagePreview')">
                            <small class="form-text text-muted">
                                Önerilen boyut: 1920x1080px<br>
                                Maksimum: <?= formatFileSize(MAX_FILE_SIZE) ?>
                            </small>
                        </div>
                        
                        <?php if (isset($slider['background_image']) && $slider['background_image']): ?>
                            <div class="mb-3">
                                <label class="form-label">Mevcut Görsel</label>
                                <img src="../../<?= escape($slider['background_image']) ?>" 
                                     class="img-fluid rounded" id="imagePreview" 
                                     alt="<?= escape($slider['title']) ?>">
                            </div>
                        <?php else: ?>
                            <img id="imagePreview" class="img-fluid rounded" style="display: none;">
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="sliders.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Geri Dön
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
