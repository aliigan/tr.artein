<?php
/**
 * BuildTech CMS - Media Management
 * Medya yönetimi sayfası
 */

define('ADMIN_PANEL', true);
require_once 'config/config.php';
requireLogin();

$page_title = 'Medya Galerisi';
$breadcrumb = [
    ['title' => 'Ana Sayfa', 'url' => 'dashboard.php'],
    ['title' => 'Medya Galerisi']
];

$action = $_GET['action'] ?? 'list';

// İşlemler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!validateCSRF($csrf_token)) {
        setErrorMessage('Güvenlik hatası. Lütfen tekrar deneyin.');
    } else {
        switch ($action) {
            case 'upload':
                if (isset($_FILES['media_files']) && !empty($_FILES['media_files']['name'][0])) {
                    $uploadCount = 0;
                    $errorCount = 0;
                    
                    $fileCount = count($_FILES['media_files']['name']);
                    for ($i = 0; $i < $fileCount; $i++) {
                        if ($_FILES['media_files']['error'][$i] === UPLOAD_ERR_OK) {
                            $mediaFile = [
                                'name' => $_FILES['media_files']['name'][$i],
                                'type' => $_FILES['media_files']['type'][$i],
                                'tmp_name' => $_FILES['media_files']['tmp_name'][$i],
                                'error' => $_FILES['media_files']['error'][$i],
                                'size' => $_FILES['media_files']['size'][$i]
                            ];
                            
                            $upload = uploadFile($mediaFile, 'media');
                            if ($upload['success']) {
                                $sql = "INSERT INTO media_files (filename, original_name, file_path, file_type, file_size, uploaded_by) 
                                        VALUES (?, ?, ?, ?, ?, ?)";
                                
                                $fileInfo = pathinfo($upload['filename']);
                                $fileType = $fileInfo['extension'] ?? 'unknown';
                                
                                if ($database->execute($sql, [$upload['filename'], $upload['original_name'], 
                                                            $upload['path'], $fileType, $upload['size'], $_SESSION['admin_id']])) {
                                    $uploadCount++;
                                } else {
                                    $errorCount++;
                                }
                            } else {
                                $errorCount++;
                            }
                        } else {
                            $errorCount++;
                        }
                    }
                    
                    if ($uploadCount > 0) {
                        setSuccessMessage("$uploadCount dosya başarıyla yüklendi." . ($errorCount > 0 ? " $errorCount dosya yüklenemedi." : ""));
                    } else {
                        setErrorMessage('Hiçbir dosya yüklenemedi.');
                    }
                } else {
                    setErrorMessage('Lütfen en az bir dosya seçin.');
                }
                break;
                
            case 'delete':
                $id = $_POST['id'] ?? 0;
                $media = $database->fetchOne("SELECT * FROM media_files WHERE id = ?", [$id]);
                if ($media) {
                    if (file_exists($media['file_path'])) {
                        unlink($media['file_path']);
                    }
                    
                    if ($database->execute("DELETE FROM media_files WHERE id = ?", [$id])) {
                        echo json_encode(['success' => true]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Dosya silinirken hata oluştu.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Dosya bulunamadı.']);
                }
                exit;
                break;
                
            case 'update':
                $id = $_POST['id'] ?? 0;
                $title = trim($_POST['title'] ?? '');
                $alt_text = trim($_POST['alt_text'] ?? '');
                
                if ($database->execute("UPDATE media_files SET title = ?, alt_text = ? WHERE id = ?", [$title, $alt_text, $id])) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Güncelleme başarısız.']);
                }
                exit;
                break;
        }
    }
}

// Medya dosyalarını listele
$page = (int)($_GET['page'] ?? 1);
$offset = ($page - 1) * ITEMS_PER_PAGE;

$search = $_GET['search'] ?? '';
$type = $_GET['type'] ?? '';

$where = [];
$params = [];

if ($search) {
    $where[] = "(original_name LIKE ? OR title LIKE ? OR alt_text LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($type) {
    $where[] = "file_type = ?";
    $params[] = $type;
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$mediaFiles = $database->fetchAll("SELECT m.*, a.username as uploader FROM media_files m 
                                   LEFT JOIN admin_users a ON m.uploaded_by = a.id 
                                   $whereClause ORDER BY m.created_at DESC 
                                   LIMIT " . ITEMS_PER_PAGE . " OFFSET $offset", $params);

$totalFiles = $database->fetchOne("SELECT COUNT(*) as count FROM media_files $whereClause", $params)['count'] ?? 0;
$totalPages = ceil($totalFiles / ITEMS_PER_PAGE);

// Dosya türlerini al
$fileTypes = $database->fetchAll("SELECT DISTINCT file_type FROM media_files WHERE file_type != '' ORDER BY file_type");

include 'includes/header.php';
?>

<?= displayMessages() ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-photo-video me-2"></i>Medya Galerisi</h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i class="fas fa-upload me-2"></i>Dosya Yükle
        </button>
    </div>
    <div class="card-body">
        <!-- Filtreler -->
        <form method="GET" class="row mb-4">
            <div class="col-md-4">
                <input type="text" class="form-control" name="search" 
                       placeholder="Dosya ara..." value="<?= escape($search) ?>">
            </div>
            <div class="col-md-3">
                <select class="form-control" name="type">
                    <option value="">Tüm Türler</option>
                    <?php foreach ($fileTypes as $fileType): ?>
                        <option value="<?= escape($fileType['file_type']) ?>" <?= $type === $fileType['file_type'] ? 'selected' : '' ?>>
                            <?= strtoupper(escape($fileType['file_type'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fas fa-search me-1"></i>Ara
                </button>
            </div>
            <div class="col-md-3 text-end">
                <small class="text-muted">Toplam: <?= $totalFiles ?> dosya</small>
            </div>
        </form>
        
        <?php if (empty($mediaFiles)): ?>
            <div class="text-center py-5">
                <i class="fas fa-photo-video fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">
                    <?= $search || $type ? 'Arama kriterlerine uygun dosya bulunamadı' : 'Henüz medya dosyası bulunmuyor' ?>
                </h5>
                <p class="text-muted">
                    <?= $search || $type ? 'Farklı arama kriterleri deneyebilirsiniz.' : 'İlk dosyanızı yüklemek için yukarıdaki butona tıklayın.' ?>
                </p>
                <?php if (!$search && !$type): ?>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                        <i class="fas fa-upload me-2"></i>Dosya Yükle
                    </button>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Medya Galerisi -->
            <div class="row" id="mediaGallery">
                <?php foreach ($mediaFiles as $media): ?>
                    <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4" id="media-<?= $media['id'] ?>">
                        <div class="card h-100">
                            <div class="position-relative">
                                <?php if (in_array(strtolower($media['file_type']), ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                                    <img src="../../<?= escape($media['file_path']) ?>" 
                                         class="card-img-top" style="height: 150px; object-fit: cover; cursor: pointer;" 
                                         alt="<?= escape($media['alt_text']) ?>"
                                         onclick="showMediaModal(<?= $media['id'] ?>)">
                                <?php else: ?>
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                         style="height: 150px; cursor: pointer;" onclick="showMediaModal(<?= $media['id'] ?>)">
                                        <i class="fas fa-file fa-3x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="position-absolute top-0 end-0 m-1">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-dark opacity-75" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" onclick="editMedia(<?= $media['id'] ?>)">
                                                <i class="fas fa-edit me-2"></i>Düzenle</a></li>
                                            <li><a class="dropdown-item" href="<?= SITE_URL ?>/<?= escape($media['file_path']) ?>" target="_blank">
                                                <i class="fas fa-external-link-alt me-2"></i>Aç</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="copyMediaUrl('<?= SITE_URL ?>/<?= escape($media['file_path']) ?>')">
                                                <i class="fas fa-copy me-2"></i>URL Kopyala</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteMedia(<?= $media['id'] ?>)">
                                                <i class="fas fa-trash me-2"></i>Sil</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-body p-2">
                                <p class="card-text small mb-1" title="<?= escape($media['original_name']) ?>">
                                    <?= escape(substr($media['original_name'], 0, 20)) ?>
                                    <?= strlen($media['original_name']) > 20 ? '...' : '' ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <?= formatFileSize($media['file_size']) ?>
                                    </small>
                                    <span class="badge bg-secondary"><?= strtoupper($media['file_type']) ?></span>
                                </div>
                                <small class="text-muted">
                                    <?= formatDate($media['created_at'], 'd.m.Y') ?>
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Sayfalama -->
            <?php if ($totalPages > 1): ?>
                <?= generatePagination($page, $totalPages, 'media.php') ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-upload me-2"></i>Dosya Yükle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data" action="media.php?action=upload">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION[CSRF_TOKEN_NAME] ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Dosyalar Seçin</label>
                        <input type="file" class="form-control" name="media_files[]" multiple accept="image/*" required>
                        <small class="form-text text-muted">
                            Birden fazla dosya seçebilirsiniz.<br>
                            Desteklenen formatlar: JPG, PNG, GIF, WebP<br>
                            Maksimum dosya boyutu: <?= formatFileSize(MAX_FILE_SIZE) ?>
                        </small>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Yüklenen görseller otomatik olarak optimize edilecektir.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>Yükle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Media Detail Modal -->
<div class="modal fade" id="mediaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Medya Detayları</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="mediaModalContent">
                <!-- Content will be loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<?php
$additional_js = '
<script>
function deleteMedia(mediaId) {
    if (confirm("Bu dosyayı silmek istediğinizden emin misiniz?")) {
        $.post("media.php?action=delete", {
            csrf_token: "' . $_SESSION[CSRF_TOKEN_NAME] . '",
            id: mediaId
        }, function(response) {
            if (response.success) {
                $("#media-" + mediaId).fadeOut(function() {
                    $(this).remove();
                });
            } else {
                alert("Hata: " + response.message);
            }
        }, "json");
    }
}

function copyMediaUrl(url) {
    navigator.clipboard.writeText(url).then(function() {
        alert("URL kopyalandı!");
    });
}

function showMediaModal(mediaId) {
    // Load media details via AJAX
    $("#mediaModalContent").html("<div class=\"text-center\"><i class=\"fas fa-spinner fa-spin\"></i> Yükleniyor...</div>");
    $("#mediaModal").modal("show");
    
    // Here you can load detailed media info via AJAX
}

function editMedia(mediaId) {
    // Edit media functionality
    alert("Düzenleme özelliği yakında eklenecek!");
}
</script>
';

include 'includes/footer.php';
?>
