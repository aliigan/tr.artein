<?php
/**
 * BuildTech CMS - Services Management
 * Admin panelinde hizmetler CRUD işlemleri
 */

define('ADMIN_PANEL', true);
require_once 'config/config.php';
requireLogin();

$page_title = 'Hizmetler Yönetimi';
$breadcrumb = [
    ['title' => 'Ana Sayfa', 'url' => 'dashboard.php'],
    ['title' => 'Hizmetler']
];

// İşlem türü
$action = $_GET['action'] ?? 'list';
$service_id = $_GET['id'] ?? 0;

// Hizmet ekleme/güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $icon = trim($_POST['icon'] ?? '');
    $order_index = (int)($_POST['order_index'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // CSRF token kontrolü
    if (!validateCSRF($csrf_token)) {
        setErrorMessage('Güvenlik hatası. Lütfen tekrar deneyin.');
    } elseif (empty($title)) {
        setErrorMessage('Hizmet başlığı zorunludur.');
    } else {
        if ($action === 'add') {
            // Yeni hizmet ekleme
            $sql = "INSERT INTO services (title, description, icon, order_index, is_active) VALUES (?, ?, ?, ?, ?)";
            
            if ($database->execute($sql, [$title, $description, $icon, $order_index, $is_active])) {
                setSuccessMessage('Hizmet başarıyla eklendi.');
                header('Location: services.php');
                exit;
            } else {
                setErrorMessage('Hizmet eklenirken bir hata oluştu.');
            }
        } elseif ($action === 'edit' && $service_id) {
            // Hizmet güncelleme
            $sql = "UPDATE services SET title = ?, description = ?, icon = ?, order_index = ?, is_active = ?, updated_at = NOW() WHERE id = ?";
            
            if ($database->execute($sql, [$title, $description, $icon, $order_index, $is_active, $service_id])) {
                setSuccessMessage('Hizmet başarıyla güncellendi.');
                header('Location: services.php');
                exit;
            } else {
                setErrorMessage('Hizmet güncellenirken bir hata oluştu.');
            }
        }
    }
}

// Hizmet silme işlemi
if ($action === 'delete' && $service_id) {
    $csrf_token = $_GET['token'] ?? '';
    
    if (validateCSRF($csrf_token)) {
        if ($database->execute("DELETE FROM services WHERE id = ?", [$service_id])) {
            setSuccessMessage('Hizmet başarıyla silindi.');
        } else {
            setErrorMessage('Hizmet silinirken bir hata oluştu.');
        }
    } else {
        setErrorMessage('Güvenlik hatası.');
    }
    
    header('Location: services.php');
    exit;
}

// Hizmet durumu değiştirme
if ($action === 'toggle' && $service_id) {
    $csrf_token = $_GET['token'] ?? '';
    
    if (validateCSRF($csrf_token)) {
        $service = $database->fetchOne("SELECT is_active FROM services WHERE id = ?", [$service_id]);
        if ($service) {
            $new_status = $service['is_active'] ? 0 : 1;
            if ($database->execute("UPDATE services SET is_active = ? WHERE id = ?", [$new_status, $service_id])) {
                setSuccessMessage('Hizmet durumu güncellendi.');
            } else {
                setErrorMessage('Durum güncellenirken bir hata oluştu.');
            }
        }
    } else {
        setErrorMessage('Güvenlik hatası.');
    }
    
    header('Location: services.php');
    exit;
}

// Sayfa verilerini hazırla
if ($action === 'list') {
    // Hizmetler listesi
    $services = $database->fetchAll("SELECT * FROM services ORDER BY order_index ASC, created_at DESC");
} elseif ($action === 'add') {
    // Yeni hizmet formu
    $service = [
        'id' => 0,
        'title' => '',
        'description' => '',
        'icon' => '',
        'order_index' => 0,
        'is_active' => 1
    ];
} elseif ($action === 'edit' && $service_id) {
    // Hizmet düzenleme formu
    $service = $database->fetchOne("SELECT * FROM services WHERE id = ?", [$service_id]);
    if (!$service) {
        setErrorMessage('Hizmet bulunamadı.');
        header('Location: services.php');
        exit;
    }
}

// FontAwesome ikonları
$common_icons = [
    'fas fa-building' => 'Bina',
    'fas fa-tasks' => 'Görevler',
    'fas fa-drafting-compass' => 'Tasarım',
    'fas fa-handshake' => 'Anlaşma',
    'fas fa-tools' => 'Araçlar',
    'fas fa-hard-hat' => 'İnşaat Şapkası',
    'fas fa-hammer' => 'Çekiç',
    'fas fa-wrench' => 'İngiliz Anahtarı',
    'fas fa-cogs' => 'Dişliler',
    'fas fa-chart-line' => 'Grafik',
    'fas fa-lightbulb' => 'Ampul',
    'fas fa-shield-alt' => 'Kalkan',
    'fas fa-award' => 'Ödül',
    'fas fa-leaf' => 'Yaprak',
    'fas fa-recycle' => 'Geri Dönüşüm',
    'fas fa-home' => 'Ev',
    'fas fa-city' => 'Şehir',
    'fas fa-industry' => 'Endüstri',
    'fas fa-road' => 'Yol',
    'fas fa-bridge' => 'Köprü'
];

include 'includes/header.php';
?>

<?php if ($action === 'list'): ?>
<!-- Hizmetler Listesi -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?= $page_title ?></h1>
    <a href="services.php?action=add" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Yeni Hizmet Ekle
    </a>
</div>

<?= displayMessages() ?>

<div class="card">
    <div class="card-body">
        <?php if (empty($services)): ?>
            <div class="text-center py-5">
                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Henüz hizmet eklenmemiş</h5>
                <p class="text-muted">İlk hizmetinizi eklemek için yukarıdaki butonu kullanın.</p>
                <a href="services.php?action=add" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Hizmet Ekle
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="50">Sıra</th>
                            <th width="60">İkon</th>
                            <th>Başlık</th>
                            <th>Açıklama</th>
                            <th width="100">Durum</th>
                            <th width="120">Tarih</th>
                            <th width="150">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-services">
                        <?php foreach ($services as $service): ?>
                            <tr data-id="<?= $service['id'] ?>">
                                <td>
                                    <span class="badge bg-secondary"><?= $service['order_index'] ?></span>
                                    <i class="fas fa-grip-vertical text-muted ms-2" style="cursor: move;"></i>
                                </td>
                                <td>
                                    <?php if ($service['icon']): ?>
                                        <i class="<?= escape($service['icon']) ?> fa-lg text-primary"></i>
                                    <?php else: ?>
                                        <i class="fas fa-question-circle fa-lg text-muted"></i>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= escape($service['title']) ?></strong>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= escape(substr($service['description'], 0, 100)) ?>
                                        <?= strlen($service['description']) > 100 ? '...' : '' ?>
                                    </small>
                                </td>
                                <td>
                                    <a href="services.php?action=toggle&id=<?= $service['id'] ?>&token=<?= $_SESSION[CSRF_TOKEN_NAME] ?>" 
                                       class="btn btn-sm <?= $service['is_active'] ? 'btn-success' : 'btn-secondary' ?>" 
                                       title="Durumu Değiştir">
                                        <i class="fas fa-<?= $service['is_active'] ? 'eye' : 'eye-slash' ?>"></i>
                                        <?= $service['is_active'] ? 'Aktif' : 'Pasif' ?>
                                    </a>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= formatDate($service['created_at'], 'd.m.Y') ?>
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="services.php?action=edit&id=<?= $service['id'] ?>" 
                                           class="btn btn-outline-primary" title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="services.php?action=delete&id=<?= $service['id'] ?>&token=<?= $_SESSION[CSRF_TOKEN_NAME] ?>" 
                                           class="btn btn-outline-danger" 
                                           onclick="return confirm('Bu hizmeti silmek istediğinizden emin misiniz?')" 
                                           title="Sil">
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
<!-- Hizmet Ekleme/Düzenleme Formu -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?= $action === 'add' ? 'Yeni Hizmet Ekle' : 'Hizmet Düzenle' ?></h1>
    <a href="services.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Geri Dön
    </a>
</div>

<?= displayMessages() ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION[CSRF_TOKEN_NAME] ?>">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Hizmet Başlığı *</label>
                                <input type="text" class="form-control" name="title" 
                                       value="<?= escape($service['title']) ?>" required maxlength="255">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Sıra Numarası</label>
                                <input type="number" class="form-control" name="order_index" 
                                       value="<?= $service['order_index'] ?>" min="0">
                                <small class="text-muted">Küçük sayılar önce görünür</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Açıklama</label>
                        <textarea class="form-control" name="description" rows="4" maxlength="500"><?= escape($service['description']) ?></textarea>
                        <small class="text-muted">Maksimum 500 karakter</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">İkon</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i id="icon-preview" class="<?= $service['icon'] ?: 'fas fa-question-circle' ?> text-primary"></i>
                            </span>
                            <input type="text" class="form-control" name="icon" id="icon-input"
                                   value="<?= escape($service['icon']) ?>" 
                                   placeholder="Örn: fas fa-building">
                            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#iconModal">
                                <i class="fas fa-search"></i> Seç
                            </button>
                        </div>
                        <small class="text-muted">FontAwesome ikon sınıfı girin</small>
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="is_active" id="is_active" 
                                   <?= $service['is_active'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active">
                                Aktif (Sitede gösterilsin)
                            </label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            <?= $action === 'add' ? 'Hizmet Ekle' : 'Değişiklikleri Kaydet' ?>
                        </button>
                        <a href="services.php" class="btn btn-secondary">İptal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Bilgilendirme</h6>
            </div>
            <div class="card-body">
                <h6>Hizmet Ekleme İpuçları:</h6>
                <ul class="small text-muted mb-0">
                    <li>Başlık kısa ve açıklayıcı olmalı</li>
                    <li>Açıklama 1-2 cümle ile sınırlı tutun</li>
                    <li>Uygun ikon seçin</li>
                    <li>Sıra numarası ile görünüm sırasını belirleyin</li>
                </ul>
                
                <hr>
                
                <h6>İkon Kullanımı:</h6>
                <p class="small text-muted">
                    FontAwesome ikonlarını kullanabilirsiniz. 
                    Örnek: <code>fas fa-building</code>
                </p>
                
                <div class="d-flex gap-2 flex-wrap">
                    <span class="badge bg-light text-dark"><i class="fas fa-building"></i> fas fa-building</span>
                    <span class="badge bg-light text-dark"><i class="fas fa-tools"></i> fas fa-tools</span>
                    <span class="badge bg-light text-dark"><i class="fas fa-handshake"></i> fas fa-handshake</span>
                </div>
            </div>
        </div>

        <?php if ($action === 'edit'): ?>
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Hizmet İstatistikleri</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h5 class="text-primary mb-0"><?= $service['order_index'] ?></h5>
                            <small class="text-muted">Sıra</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h5 class="text-<?= $service['is_active'] ? 'success' : 'secondary' ?> mb-0">
                            <?= $service['is_active'] ? 'Aktif' : 'Pasif' ?>
                        </h5>
                        <small class="text-muted">Durum</small>
                    </div>
                </div>
                <hr>
                <small class="text-muted">
                    <strong>Oluşturulma:</strong> <?= formatDate($service['created_at']) ?><br>
                    <strong>Son Güncelleme:</strong> <?= formatDate($service['updated_at']) ?>
                </small>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- İkon Seçim Modalı -->
<div class="modal fade" id="iconModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">İkon Seç</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2">
                    <?php foreach ($common_icons as $icon_class => $icon_name): ?>
                        <div class="col-md-4 col-sm-6">
                            <button type="button" class="btn btn-outline-secondary w-100 icon-select-btn" 
                                    data-icon="<?= $icon_class ?>">
                                <i class="<?= $icon_class ?> me-2"></i>
                                <?= $icon_name ?>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>

<script>
// İkon önizleme
document.getElementById('icon-input').addEventListener('input', function() {
    const iconClass = this.value || 'fas fa-question-circle';
    document.getElementById('icon-preview').className = iconClass + ' text-primary';
});

// İkon seçim modalı
document.querySelectorAll('.icon-select-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const iconClass = this.dataset.icon;
        document.getElementById('icon-input').value = iconClass;
        document.getElementById('icon-preview').className = iconClass + ' text-primary';
        bootstrap.Modal.getInstance(document.getElementById('iconModal')).hide();
    });
});

// Sürükle-bırak sıralama
<?php if ($action === 'list' && !empty($services)): ?>
$(document).ready(function() {
    $('#sortable-services').sortable({
        handle: '.fa-grip-vertical',
        update: function(event, ui) {
            const order = $(this).sortable('toArray', {attribute: 'data-id'});
            
            $.ajax({
                url: 'ajax/update_service_order.php',
                method: 'POST',
                data: {
                    order: order,
                    csrf_token: '<?= $_SESSION[CSRF_TOKEN_NAME] ?>'
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                }
            });
        }
    });
});
<?php endif; ?>
</script>

<?php include 'includes/footer.php'; ?>
