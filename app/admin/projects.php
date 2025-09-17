<?php
/**
 * BuildTech CMS - Project Management
 * Dinamik proje yönetimi sayfası
 */

define('ADMIN_PANEL', true);
require_once 'config/config.php';
requireLogin();

$page_title = 'Proje Yönetimi';
$breadcrumb = [
    ['title' => 'Ana Sayfa', 'url' => 'dashboard.php'],
    ['title' => 'Proje Yönetimi']
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
                $description = trim($_POST['description'] ?? '');
                $content = trim($_POST['content'] ?? '');
                $category = trim($_POST['category'] ?? '');
                $client = trim($_POST['client'] ?? '');
                $location = trim($_POST['location'] ?? '');
                $completion_date = $_POST['completion_date'] ?? null;
                $budget = floatval($_POST['budget'] ?? 0);
                $order_index = (int)($_POST['order_index'] ?? 0);
                $is_featured = isset($_POST['is_featured']) ? 1 : 0;
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                $slug = createSlug($title);
                $meta_title = trim($_POST['meta_title'] ?? $title);
                $meta_description = trim($_POST['meta_description'] ?? $description);
                
                if (empty($title)) {
                    setErrorMessage('Proje başlığı zorunludur.');
                } else {
                    // Slug benzersizlik kontrolü
                    $existingSlug = $database->fetchOne("SELECT id FROM projects WHERE slug = ?", [$slug]);
                    if ($existingSlug) {
                        $slug .= '-' . time();
                    }
                    
                    $featured_image = '';
                    
                    // Ana resim yükleme
                    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
                        $upload = uploadFile($_FILES['featured_image'], 'projects');
                        if ($upload['success']) {
                            $featured_image = $upload['path'];
                        } else {
                            setErrorMessage($upload['message']);
                            break;
                        }
                    }
                    
                    $sql = "INSERT INTO projects (title, description, content, featured_image, category, client, 
                            completion_date, location, budget, order_index, is_featured, is_active, slug, 
                            meta_title, meta_description) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    
                    if ($database->execute($sql, [$title, $description, $content, $featured_image, $category, 
                                                  $client, $completion_date, $location, $budget, $order_index, 
                                                  $is_featured, $is_active, $slug, $meta_title, $meta_description])) {
                        
                        $project_id = $database->lastInsertId();
                        
                        // Ek resimler yükleme
                        if (isset($_FILES['project_images']) && !empty($_FILES['project_images']['name'][0])) {
                            $imageCount = count($_FILES['project_images']['name']);
                            for ($i = 0; $i < $imageCount; $i++) {
                                if ($_FILES['project_images']['error'][$i] === UPLOAD_ERR_OK) {
                                    $imageFile = [
                                        'name' => $_FILES['project_images']['name'][$i],
                                        'type' => $_FILES['project_images']['type'][$i],
                                        'tmp_name' => $_FILES['project_images']['tmp_name'][$i],
                                        'error' => $_FILES['project_images']['error'][$i],
                                        'size' => $_FILES['project_images']['size'][$i]
                                    ];
                                    
                                    $upload = uploadFile($imageFile, 'projects');
                                    if ($upload['success']) {
                                        $alt_text = $_POST['image_alt'][$i] ?? '';
                                        $database->execute("INSERT INTO project_images (project_id, image_path, alt_text, order_index) VALUES (?, ?, ?, ?)", 
                                                         [$project_id, $upload['path'], $alt_text, $i]);
                                    }
                                }
                            }
                        }
                        
                        setSuccessMessage('Proje başarıyla eklendi.');
                        header('Location: projects.php');
                        exit;
                    } else {
                        setErrorMessage('Proje eklenirken hata oluştu.');
                    }
                }
                break;
                
            case 'edit':
                $title = trim($_POST['title'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $content = trim($_POST['content'] ?? '');
                $category = trim($_POST['category'] ?? '');
                $client = trim($_POST['client'] ?? '');
                $location = trim($_POST['location'] ?? '');
                $completion_date = $_POST['completion_date'] ?? null;
                $budget = floatval($_POST['budget'] ?? 0);
                $order_index = (int)($_POST['order_index'] ?? 0);
                $is_featured = isset($_POST['is_featured']) ? 1 : 0;
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                $meta_title = trim($_POST['meta_title'] ?? $title);
                $meta_description = trim($_POST['meta_description'] ?? $description);
                
                if (empty($title)) {
                    setErrorMessage('Proje başlığı zorunludur.');
                } else {
                    // Mevcut kaydı al
                    $project = $database->fetchOne("SELECT * FROM projects WHERE id = ?", [$id]);
                    if (!$project) {
                        setErrorMessage('Proje bulunamadı.');
                        break;
                    }
                    
                    $featured_image = $project['featured_image'];
                    $slug = $project['slug'];
                    
                    // Başlık değişmişse slug'ı güncelle
                    if ($title !== $project['title']) {
                        $newSlug = createSlug($title);
                        $existingSlug = $database->fetchOne("SELECT id FROM projects WHERE slug = ? AND id != ?", [$newSlug, $id]);
                        if (!$existingSlug) {
                            $slug = $newSlug;
                        }
                    }
                    
                    // Yeni ana resim yüklendi mi?
                    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
                        $upload = uploadFile($_FILES['featured_image'], 'projects');
                        if ($upload['success']) {
                            // Eski resmi sil
                            if ($featured_image && file_exists($featured_image)) {
                                unlink($featured_image);
                            }
                            $featured_image = $upload['path'];
                        } else {
                            setErrorMessage($upload['message']);
                            break;
                        }
                    }
                    
                    $sql = "UPDATE projects SET title = ?, description = ?, content = ?, featured_image = ?, 
                            category = ?, client = ?, completion_date = ?, location = ?, budget = ?, 
                            order_index = ?, is_featured = ?, is_active = ?, slug = ?, meta_title = ?, 
                            meta_description = ? WHERE id = ?";
                    
                    if ($database->execute($sql, [$title, $description, $content, $featured_image, $category, 
                                                  $client, $completion_date, $location, $budget, $order_index, 
                                                  $is_featured, $is_active, $slug, $meta_title, $meta_description, $id])) {
                        
                        // Ek resimler yükleme
                        if (isset($_FILES['project_images']) && !empty($_FILES['project_images']['name'][0])) {
                            $imageCount = count($_FILES['project_images']['name']);
                            for ($i = 0; $i < $imageCount; $i++) {
                                if ($_FILES['project_images']['error'][$i] === UPLOAD_ERR_OK) {
                                    $imageFile = [
                                        'name' => $_FILES['project_images']['name'][$i],
                                        'type' => $_FILES['project_images']['type'][$i],
                                        'tmp_name' => $_FILES['project_images']['tmp_name'][$i],
                                        'error' => $_FILES['project_images']['error'][$i],
                                        'size' => $_FILES['project_images']['size'][$i]
                                    ];
                                    
                                    $upload = uploadFile($imageFile, 'projects');
                                    if ($upload['success']) {
                                        $alt_text = $_POST['image_alt'][$i] ?? '';
                                        $database->execute("INSERT INTO project_images (project_id, image_path, alt_text, order_index) VALUES (?, ?, ?, ?)", 
                                                         [$id, $upload['path'], $alt_text, $i]);
                                    }
                                }
                            }
                        }
                        
                        setSuccessMessage('Proje başarıyla güncellendi.');
                        header('Location: projects.php');
                        exit;
                    } else {
                        setErrorMessage('Proje güncellenirken hata oluştu.');
                    }
                }
                break;
                
            case 'delete':
                $project = $database->fetchOne("SELECT * FROM projects WHERE id = ?", [$id]);
                if ($project) {
                    // Ana resmi sil
                    if ($project['featured_image'] && file_exists($project['featured_image'])) {
                        unlink($project['featured_image']);
                    }
                    
                    // Proje resimlerini sil
                    $projectImages = $database->fetchAll("SELECT * FROM project_images WHERE project_id = ?", [$id]);
                    foreach ($projectImages as $image) {
                        if (file_exists($image['image_path'])) {
                            unlink($image['image_path']);
                        }
                    }
                    
                    // Veritabanından sil
                    $database->execute("DELETE FROM project_images WHERE project_id = ?", [$id]);
                    
                    if ($database->execute("DELETE FROM projects WHERE id = ?", [$id])) {
                        setSuccessMessage('Proje başarıyla silindi.');
                    } else {
                        setErrorMessage('Proje silinirken hata oluştu.');
                    }
                } else {
                    setErrorMessage('Proje bulunamadı.');
                }
                header('Location: projects.php');
                exit;
                break;
                
            case 'delete_image':
                $image_id = $_POST['image_id'] ?? 0;
                $image = $database->fetchOne("SELECT * FROM project_images WHERE id = ?", [$image_id]);
                if ($image) {
                    if (file_exists($image['image_path'])) {
                        unlink($image['image_path']);
                    }
                    $database->execute("DELETE FROM project_images WHERE id = ?", [$image_id]);
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Resim bulunamadı.']);
                }
                exit;
                break;
        }
    }
}

// Sayfa içeriği
switch ($action) {
    case 'add':
        $breadcrumb[] = ['title' => 'Yeni Proje'];
        break;
    case 'edit':
        $project = $database->fetchOne("SELECT * FROM projects WHERE id = ?", [$id]);
        if (!$project) {
            setErrorMessage('Proje bulunamadı.');
            header('Location: projects.php');
            exit;
        }
        $project_images = $database->fetchAll("SELECT * FROM project_images WHERE project_id = ? ORDER BY order_index", [$id]);
        $breadcrumb[] = ['title' => 'Proje Düzenle'];
        break;
    default:
        $page = (int)($_GET['page'] ?? 1);
        $offset = ($page - 1) * ITEMS_PER_PAGE;
        
        $search = $_GET['search'] ?? '';
        $category = $_GET['category'] ?? '';
        
        $where = [];
        $params = [];
        
        if ($search) {
            $where[] = "(title LIKE ? OR description LIKE ? OR client LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if ($category) {
            $where[] = "category = ?";
            $params[] = $category;
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $projects = $database->fetchAll("SELECT * FROM projects $whereClause ORDER BY order_index ASC, created_at DESC LIMIT " . ITEMS_PER_PAGE . " OFFSET $offset", $params);
        $totalProjects = $database->fetchOne("SELECT COUNT(*) as count FROM projects $whereClause", $params)['count'] ?? 0;
        $totalPages = ceil($totalProjects / ITEMS_PER_PAGE);
        
        // Kategorileri al
        $categories = $database->fetchAll("SELECT DISTINCT category FROM projects WHERE category != '' ORDER BY category");
        break;
}

include 'includes/header.php';
?>

<?php if ($action === 'list'): ?>
    <!-- Proje Listesi -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-project-diagram me-2"></i>Proje Listesi</h5>
            <a href="projects.php?action=add" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Yeni Proje Ekle
            </a>
        </div>
        <div class="card-body">
            <!-- Filtreler -->
            <form method="GET" class="row mb-4">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" 
                           placeholder="Proje ara..." value="<?= escape($search) ?>">
                </div>
                <div class="col-md-3">
                    <select class="form-control" name="category">
                        <option value="">Tüm Kategoriler</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= escape($cat['category']) ?>" <?= $category === $cat['category'] ? 'selected' : '' ?>>
                                <?= escape($cat['category']) ?>
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
                    <small class="text-muted">Toplam: <?= $totalProjects ?> proje</small>
                </div>
            </form>
            
            <?php if (empty($projects)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-project-diagram fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">
                        <?= $search || $category ? 'Arama kriterlerine uygun proje bulunamadı' : 'Henüz proje bulunmuyor' ?>
                    </h5>
                    <p class="text-muted">
                        <?= $search || $category ? 'Farklı arama kriterleri deneyebilirsiniz.' : 'İlk projenizi eklemek için yukarıdaki butona tıklayın.' ?>
                    </p>
                    <?php if (!$search && !$category): ?>
                        <a href="projects.php?action=add" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Yeni Proje Ekle
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="80">Görsel</th>
                                <th>Proje Bilgileri</th>
                                <th width="120">Kategori</th>
                                <th width="100">Durum</th>
                                <th width="120">Tarih</th>
                                <th width="150">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($projects as $project): ?>
                                <tr>
                                    <td>
                                        <?php if ($project['featured_image']): ?>
                                            <img src="../../<?= escape($project['featured_image']) ?>" 
                                                 class="img-thumbnail" style="width: 60px; height: 40px; object-fit: cover;" 
                                                 alt="<?= escape($project['title']) ?>">
                                        <?php else: ?>
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 40px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?= escape($project['title']) ?></strong>
                                            <?php if ($project['is_featured']): ?>
                                                <span class="badge bg-warning ms-1">Öne Çıkan</span>
                                            <?php endif; ?>
                                        </div>
                                        <small class="text-muted">
                                            <?= escape($project['client']) ?> • <?= escape($project['location']) ?>
                                        </small>
                                        <div class="small text-muted">
                                            <?= escape(substr($project['description'], 0, 80)) ?>...
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?= escape($project['category']) ?></span>
                                    </td>
                                    <td>
                                        <?php if ($project['is_active']): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Pasif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= formatDate($project['created_at'], 'd.m.Y') ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= SITE_URL ?>/project-detail.php?slug=<?= $project['slug'] ?>" 
                                               target="_blank" class="btn btn-outline-info" title="Görüntüle">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="projects.php?action=edit&id=<?= $project['id'] ?>" 
                                               class="btn btn-outline-primary" title="Düzenle">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="projects.php?action=delete&id=<?= $project['id'] ?>" 
                                               class="btn btn-outline-danger" title="Sil"
                                               onclick="return confirmDelete('Bu projeyi silmek istediğinizden emin misiniz?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Sayfalama -->
                <?php if ($totalPages > 1): ?>
                    <?= generatePagination($page, $totalPages, 'projects.php') ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

<?php else: ?>
    <!-- Proje Ekleme/Düzenleme Formu -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-<?= $action === 'add' ? 'plus' : 'edit' ?> me-2"></i>
                <?= $action === 'add' ? 'Yeni Proje Ekle' : 'Proje Düzenle' ?>
            </h5>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION[CSRF_TOKEN_NAME] ?>">
                
                <div class="row">
                    <div class="col-md-8">
                        <!-- Temel Bilgiler -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Temel Bilgiler</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Proje Başlığı <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="title" 
                                           value="<?= escape($project['title'] ?? '') ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Kısa Açıklama</label>
                                    <textarea class="form-control" name="description" rows="3"><?= escape($project['description'] ?? '') ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Detaylı İçerik</label>
                                    <textarea class="form-control summernote" name="content"><?= escape($project['content'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Proje Detayları -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Proje Detayları</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Kategori</label>
                                            <input type="text" class="form-control" name="category" 
                                                   value="<?= escape($project['category'] ?? '') ?>" 
                                                   list="categoryList">
                                            <datalist id="categoryList">
                                                <option value="Konut">
                                                <option value="Ticari">
                                                <option value="Endüstriyel">
                                                <option value="Altyapı">
                                                <option value="Restorasyon">
                                            </datalist>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Müşteri</label>
                                            <input type="text" class="form-control" name="client" 
                                                   value="<?= escape($project['client'] ?? '') ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Konum</label>
                                            <input type="text" class="form-control" name="location" 
                                                   value="<?= escape($project['location'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Tamamlanma Tarihi</label>
                                            <input type="date" class="form-control" name="completion_date" 
                                                   value="<?= escape($project['completion_date'] ?? '') ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Bütçe (TL)</label>
                                            <input type="number" class="form-control" name="budget" step="0.01"
                                                   value="<?= escape($project['budget'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Sıra</label>
                                            <input type="number" class="form-control" name="order_index" 
                                                   value="<?= escape($project['order_index'] ?? 0) ?>" min="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- SEO Ayarları -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">SEO Ayarları</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Meta Başlık</label>
                                    <input type="text" class="form-control" name="meta_title" 
                                           value="<?= escape($project['meta_title'] ?? '') ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Meta Açıklama</label>
                                    <textarea class="form-control" name="meta_description" rows="3"><?= escape($project['meta_description'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <!-- Ana Görsel -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Ana Görsel</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <input type="file" class="form-control" name="featured_image" 
                                           accept="image/*" onchange="previewImage(this, 'featuredPreview')">
                                    <small class="form-text text-muted">
                                        Önerilen boyut: 800x600px<br>
                                        Maksimum: <?= formatFileSize(MAX_FILE_SIZE) ?>
                                    </small>
                                </div>
                                
                                <?php if (isset($project['featured_image']) && $project['featured_image']): ?>
                                    <img src="../../<?= escape($project['featured_image']) ?>" 
                                         class="img-fluid rounded" id="featuredPreview" 
                                         alt="<?= escape($project['title']) ?>">
                                <?php else: ?>
                                    <img id="featuredPreview" class="img-fluid rounded" style="display: none;">
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Ek Görseller -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Ek Görseller</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <input type="file" class="form-control" name="project_images[]" 
                                           accept="image/*" multiple>
                                    <small class="form-text text-muted">
                                        Birden fazla resim seçebilirsiniz
                                    </small>
                                </div>
                                
                                <?php if (isset($project_images) && !empty($project_images)): ?>
                                    <div class="row" id="existingImages">
                                        <?php foreach ($project_images as $image): ?>
                                            <div class="col-6 mb-2" id="image-<?= $image['id'] ?>">
                                                <div class="position-relative">
                                                    <img src="../../<?= escape($image['image_path']) ?>" 
                                                         class="img-fluid rounded" 
                                                         alt="<?= escape($image['alt_text']) ?>">
                                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1" 
                                                            onclick="deleteProjectImage(<?= $image['id'] ?>)">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Ayarlar -->
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Ayarlar</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" class="form-check-input" name="is_featured" 
                                           <?= ($project['is_featured'] ?? 0) ? 'checked' : '' ?>>
                                    <label class="form-check-label">Öne Çıkan Proje</label>
                                </div>
                                
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" name="is_active" 
                                           <?= ($project['is_active'] ?? 1) ? 'checked' : '' ?>>
                                    <label class="form-check-label">Aktif</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="projects.php" class="btn btn-secondary">
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

<?php
$additional_js = '
<script>
function deleteProjectImage(imageId) {
    if (confirm("Bu resmi silmek istediğinizden emin misiniz?")) {
        $.post("projects.php?action=delete_image", {
            csrf_token: "' . $_SESSION[CSRF_TOKEN_NAME] . '",
            image_id: imageId
        }, function(response) {
            if (response.success) {
                $("#image-" + imageId).fadeOut(function() {
                    $(this).remove();
                });
            } else {
                alert("Hata: " + response.message);
            }
        }, "json");
    }
}
</script>
';

include 'includes/footer.php';
?>
