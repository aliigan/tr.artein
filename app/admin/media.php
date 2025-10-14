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

// Edit sayfası için özel kontrol
if ($action === 'edit') {
    $editId = $_GET['id'] ?? 0;
    $editMedia = $database->fetchOne("SELECT * FROM media_files WHERE id = ?", [$editId]);
    if (!$editMedia) {
        setErrorMessage('Düzenlenecek medya bulunamadı.');
        header('Location: media.php');
        exit;
    }
    
    // Edit sayfasında tüm medyaları da listele
    $allMediaFiles = $database->fetchAll("SELECT m.*, a.username as uploader FROM media_files m 
                                         LEFT JOIN admin_users a ON m.uploaded_by = a.id 
                                         ORDER BY m.created_at DESC");
}

// İşlemler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!validateCSRF($csrf_token)) {
        setErrorMessage('Güvenlik hatası. Lütfen tekrar deneyin.');
        header('Location: media.php');
        exit;
    } else {
        switch ($action) {
            case 'upload':
                if (isset($_FILES['media_files']) && !empty($_FILES['media_files']['name'][0])) {
                    $uploadCount = 0;
                    $errorCount = 0;
                    $errors = [];
                    
                    // Get common title, alt_text, and description
                    $commonTitle = trim($_POST['title'] ?? '');
                    $commonAltText = trim($_POST['alt_text'] ?? '');
                    $commonDescription = trim($_POST['description'] ?? '');
                    
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
                                $sql = "INSERT INTO media_files (filename, original_name, file_path, file_type, file_size, media_type, title, alt_text, description, uploaded_by) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                                
                                $fileInfo = pathinfo($upload['filename']);
                                $fileType = $fileInfo['extension'] ?? 'unknown';
                                
                                // Determine media type based on file extension
                                $mediaType = 'photo';
                                if (in_array(strtolower($fileType), ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv'])) {
                                    $mediaType = 'video';
                                }
                                
                                // Use common values or filename as fallback for title
                                $title = $commonTitle ?: pathinfo($upload['original_name'], PATHINFO_FILENAME);
                                $altText = $commonAltText;
                                $description = $commonDescription;
                                
                                try {
                                    $stmt = $database->getConnection()->prepare($sql);
                                    if ($stmt->execute([$upload['filename'], $upload['original_name'], 
                                                      $upload['path'], $fileType, $upload['size'], $mediaType, 
                                                      $title, $altText, $description, $_SESSION['admin_id']])) {
                                        $uploadCount++;
                                    } else {
                                        $errorCount++;
                                        $errors[] = "Veritabanı hatası: " . $mediaFile['name'] . " - " . implode(", ", $stmt->errorInfo());
                                    }
                                } catch (Exception $e) {
                                    $errorCount++;
                                    $errors[] = "Veritabanı hatası: " . $mediaFile['name'] . " - " . $e->getMessage();
                                }
                            } else {
                                $errorCount++;
                                $errors[] = $upload['message'] . ": " . $mediaFile['name'];
                            }
                        } else {
                            $errorCount++;
                            $errors[] = "Dosya yükleme hatası (kod: " . $_FILES['media_files']['error'][$i] . "): " . $_FILES['media_files']['name'][$i];
                        }
                    }
                    
                    if ($uploadCount > 0) {
                        $message = "$uploadCount dosya başarıyla yüklendi." . ($errorCount > 0 ? " $errorCount dosya yüklenemedi." : "");
                        if (!empty($errors)) {
                            $message .= " Hatalar: " . implode(", ", $errors);
                        }
                        setSuccessMessage($message);
                    } else {
                        $errorMessage = 'Hiçbir dosya yüklenemedi.';
                        if (!empty($errors)) {
                            $errorMessage .= " Hatalar: " . implode(", ", $errors);
                        }
                        setErrorMessage($errorMessage);
                    }
                } else {
                    setErrorMessage('Lütfen en az bir dosya seçin.');
                }
                header('Location: media.php');
                exit;
                break;
                
            case 'delete':
                $id = $_POST['id'] ?? 0;
                $media = $database->fetchOne("SELECT * FROM media_files WHERE id = ?", [$id]);
                if ($media) {
                    if (file_exists($media['file_path'])) {
                        unlink($media['file_path']);
                    }
                    
                    if ($database->execute("DELETE FROM media_files WHERE id = ?", [$id])) {
                        setSuccessMessage('Dosya başarıyla silindi.');
                    } else {
                        setErrorMessage('Dosya silinirken hata oluştu.');
                    }
                } else {
                    setErrorMessage('Dosya bulunamadı.');
                }
                header('Location: media.php');
                exit;
                break;
                
            case 'update':
                $id = $_POST['id'] ?? 0;
                $title = trim($_POST['title'] ?? '');
                $alt_text = trim($_POST['alt_text'] ?? '');
                $description = trim($_POST['description'] ?? '');
                
                if ($database->execute("UPDATE media_files SET title = ?, alt_text = ?, description = ? WHERE id = ?", [$title, $alt_text, $description, $id])) {
                    setSuccessMessage('Medya bilgileri başarıyla güncellendi.');
                } else {
                    setErrorMessage('Güncelleme başarısız.');
                }
                header('Location: media.php');
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

<?php if ($action === 'edit'): ?>
<!-- Edit Form -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Medya Düzenle</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <!-- Media Preview -->
                <div class="text-center mb-3">
                    <?php if ($editMedia['media_type'] === 'video'): ?>
                        <video class="img-fluid rounded" style="max-height: 300px;" controls>
                            <source src="../../<?= escape($editMedia['file_path']) ?>" type="video/<?= $editMedia['file_type'] ?>">
                        </video>
                    <?php elseif (in_array(strtolower($editMedia['file_type']), ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                        <img src="../../<?= escape($editMedia['file_path']) ?>" 
                             class="img-fluid rounded" style="max-height: 300px;" 
                             alt="<?= escape($editMedia['alt_text']) ?>">
                    <?php else: ?>
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="fas fa-file fa-3x text-muted"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="text-center">
                    <small class="text-muted">
                        <strong>Dosya:</strong> <?= escape($editMedia['original_name']) ?><br>
                        <strong>Boyut:</strong> <?= formatFileSize($editMedia['file_size']) ?><br>
                        <strong>Tür:</strong> <?= strtoupper($editMedia['file_type']) ?>
                    </small>
                </div>
            </div>
            <div class="col-md-8">
                <!-- Edit Form -->
                <form method="POST" action="media.php?action=update">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION[CSRF_TOKEN_NAME] ?>">
                    <input type="hidden" name="id" value="<?= $editMedia['id'] ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Başlık</label>
                        <input type="text" class="form-control" name="title" 
                               value="<?= escape($editMedia['title']) ?>" placeholder="Medya başlığı">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Alt Metin (Alt Text)</label>
                        <input type="text" class="form-control" name="alt_text" 
                               value="<?= escape($editMedia['alt_text']) ?>" placeholder="SEO için alt metin">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Açıklama</label>
                        <textarea class="form-control" name="description" rows="4" 
                                  placeholder="Medya açıklaması"><?= escape($editMedia['description']) ?></textarea>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Kaydet
                        </button>
                        <a href="media.php" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>İptal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Medya Listesi -->
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Tüm Medya Dosyaları</h5>
    </div>
    <div class="card-body">
        <?php if (empty($allMediaFiles)): ?>
            <div class="text-center py-3">
                <i class="fas fa-photo-video fa-2x text-muted mb-2"></i>
                <p class="text-muted">Henüz medya dosyası bulunmuyor.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Önizleme</th>
                            <th>Başlık</th>
                            <th>Dosya Adı</th>
                            <th>Tür</th>
                            <th>Boyut</th>
                            <th>Tarih</th>
                            <th style="width: 120px;">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allMediaFiles as $media): ?>
                            <tr <?= $media['id'] == $editMedia['id'] ? 'class="table-primary"' : '' ?>>
                                <td>
                                    <?php if ($media['media_type'] === 'video'): ?>
                                        <div class="bg-dark rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 40px;">
                                            <i class="fas fa-play text-white"></i>
                                        </div>
                                    <?php elseif (in_array(strtolower($media['file_type']), ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                                        <img src="../../<?= escape($media['file_path']) ?>" 
                                             class="rounded" style="width: 60px; height: 40px; object-fit: cover;" 
                                             alt="<?= escape($media['alt_text']) ?>">
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 40px;">
                                            <i class="fas fa-file text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= escape($media['title']) ?: 'Başlık yok' ?></strong>
                                    <?php if ($media['id'] == $editMedia['id']): ?>
                                        <span class="badge bg-primary ms-2">Düzenleniyor</span>
                                    <?php endif; ?>
                                    <?php if ($media['alt_text']): ?>
                                        <br><small class="text-muted"><?= escape($media['alt_text']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span title="<?= escape($media['original_name']) ?>">
                                        <?= escape(substr($media['original_name'], 0, 25)) ?>
                                        <?= strlen($media['original_name']) > 25 ? '...' : '' ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?= strtoupper($media['file_type']) ?></span>
                                </td>
                                <td>
                                    <small><?= formatFileSize($media['file_size']) ?></small>
                                </td>
                                <td>
                                    <small><?= formatDate($media['created_at'], 'd.m.Y') ?></small>
                                </td>
                                <td>
                                    <?php if ($media['id'] != $editMedia['id']): ?>
                                        <a href="media.php?action=edit&id=<?= $media['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="btn btn-sm btn-primary disabled">
                                            <i class="fas fa-edit"></i>
                                        </span>
                                    <?php endif; ?>
                                    <a href="https://artein.tr/<?= escape($media['file_path']) ?>" 
                                       target="_blank" class="btn btn-sm btn-outline-success ms-1" title="Aç">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
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
                                <?php if ($media['media_type'] === 'video'): ?>
                                    <video class="card-img-top" style="height: 150px; object-fit: cover; cursor: pointer;" 
                                           onclick="showMediaModal(<?= $media['id'] ?>)">
                                        <source src="../../<?= escape($media['file_path']) ?>" type="video/<?= $media['file_type'] ?>">
                                        <div class="bg-light d-flex align-items-center justify-content-center h-100">
                                            <i class="fas fa-play-circle fa-3x text-muted"></i>
                                        </div>
                                    </video>
                                <?php elseif (in_array(strtolower($media['file_type']), ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
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
                                            <li><a class="dropdown-item" href="media.php?action=edit&id=<?= $media['id'] ?>">
                                                <i class="fas fa-edit me-2"></i>Düzenle</a></li>
                                            <li><a class="dropdown-item" href="https://artein.tr/<?= escape($media['file_path']) ?>" target="_blank">
                                                <i class="fas fa-external-link-alt me-2"></i>Aç</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="copyToClipboard('https://artein.tr/<?= escape($media['file_path']) ?>')">
                                                <i class="fas fa-copy me-2"></i>URL Kopyala</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form method="POST" action="media.php?action=delete" style="display: inline;" 
                                                      onsubmit="return confirm('Bu dosyayı silmek istediğinizden emin misiniz?\n\nBu işlem geri alınamaz!')">
                                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION[CSRF_TOKEN_NAME] ?>">
                                                    <input type="hidden" name="id" value="<?= $media['id'] ?>">
                                                    <button type="submit" class="dropdown-item text-danger" style="border: none; background: none; width: 100%; text-align: left;">
                                                        <i class="fas fa-trash me-2"></i>Sil
                                                    </button>
                                                </form>
                                            </li>
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

<?php endif; // End of edit check ?>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-upload me-2"></i>Dosya Yükle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="uploadForm" method="POST" enctype="multipart/form-data" action="media.php?action=upload">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION[CSRF_TOKEN_NAME] ?>">
                    
                <div class="mb-3">
                    <label class="form-label">Dosyalar Seçin <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" name="media_files[]" multiple accept="image/*,video/*" required>
                    <small class="form-text text-muted">
                        Birden fazla dosya seçebilirsiniz.<br>
                        Desteklenen formatlar: JPG, PNG, GIF, WebP, MP4, AVI, MOV, WebM, MKV<br>
                        Maksimum dosya boyutu: <?= formatFileSize(MAX_FILE_SIZE) ?><br>
                        <strong>Video dosyaları için önerilen boyut: 50MB altı</strong>
                    </small>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Başlık</label>
                            <input type="text" class="form-control" name="title" placeholder="Medya başlığı (opsiyonel)">
                            <small class="form-text text-muted">Boş bırakılırsa dosya adı kullanılır</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Alt Metin (Alt Text)</label>
                            <input type="text" class="form-control" name="alt_text" placeholder="SEO için alt metin (opsiyonel)">
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Açıklama</label>
                    <textarea class="form-control" name="description" rows="3" placeholder="Medya açıklaması (opsiyonel)"></textarea>
                    <small class="form-text text-muted">Bu açıklama tüm yüklenen dosyalar için kullanılacaktır</small>
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Yüklenen görseller otomatik olarak optimize edilecektir.<br>
                    <i class="fas fa-video me-2"></i>
                    Video dosyaları orijinal boyutlarında saklanacaktır.
                </div>
                    
                    <div id="uploadProgress" class="d-none">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                        </div>
                        <small class="text-muted mt-2 d-block">Yükleniyor...</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary" id="uploadBtn">
                        <i class="fas fa-upload me-2"></i>Yükle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
// Basit URL kopyalama fonksiyonu - AJAX yok!
function copyToClipboard(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function() {
            alert("URL kopyalandı!");
        }).catch(function() {
            // Fallback
            fallbackCopy(text);
        });
    } else {
        // Fallback for older browsers
        fallbackCopy(text);
    }
}

function fallbackCopy(text) {
    var textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.position = "fixed";
    textArea.style.top = "0";
    textArea.style.left = "0";
    
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        var successful = document.execCommand('copy');
        if (successful) {
            alert("URL kopyalandı!");
        } else {
            alert("URL kopyalanamadı. Lütfen manuel olarak kopyalayın:\n" + text);
        }
    } catch (err) {
        alert("URL kopyalanamadı. Lütfen manuel olarak kopyalayın:\n" + text);
    }
    
    document.body.removeChild(textArea);
}

// Upload form handler
$(document).ready(function() {
    $("#uploadForm").on("submit", function(e) {
        const fileInput = $('input[name="media_files[]"]')[0];
        if (!fileInput.files || fileInput.files.length === 0) {
            e.preventDefault();
            alert("Lütfen en az bir dosya seçin.");
            return false;
        }
        
        const uploadBtn = $("#uploadBtn");
        const progressDiv = $("#uploadProgress");
        
        progressDiv.removeClass("d-none");
        uploadBtn.prop("disabled", true).html('<i class="fas fa-spinner fa-spin me-2"></i>Yükleniyor...');
        
        return true;
    });
    
    $("#uploadModal").on("hidden.bs.modal", function() {
        $("#uploadForm")[0].reset();
        $("#uploadProgress").addClass("d-none");
        $("#uploadBtn").prop("disabled", false).html('<i class="fas fa-upload me-2"></i>Yükle');
    });
});
</script>

<?php include 'includes/footer.php'; ?>
