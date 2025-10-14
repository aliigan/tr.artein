<?php
/**
 * BuildTech CMS - Modern Admin Dashboard
 * Redesigned dashboard with improved statistics and quick actions
 */

define('ADMIN_PANEL', true);
require_once 'config/config.php';
requireLogin();

$page_title = 'Dashboard';
$breadcrumb = [
    ['title' => 'Ana Sayfa', 'url' => 'dashboard.php'],
    ['title' => 'Dashboard']
];

// İstatistikleri çek
$stats = [
    'projects' => $database->fetchOne("SELECT COUNT(*) as count FROM projects WHERE is_active = 1")['count'] ?? 0,
    'total_projects' => $database->fetchOne("SELECT COUNT(*) as count FROM projects")['count'] ?? 0,
    'sliders' => $database->fetchOne("SELECT COUNT(*) as count FROM sliders WHERE is_active = 1")['count'] ?? 0,
    'services' => $database->fetchOne("SELECT COUNT(*) as count FROM services WHERE is_active = 1")['count'] ?? 0,
    'messages' => $database->fetchOne("SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0")['count'] ?? 0,
    'total_messages' => $database->fetchOne("SELECT COUNT(*) as count FROM contact_messages")['count'] ?? 0,
    'media_files' => $database->fetchOne("SELECT COUNT(*) as count FROM media_files")['count'] ?? 0,
];

// Bugünkü istatistikler
$today = date('Y-m-d');
$todayStats = $database->fetchOne("SELECT * FROM site_stats WHERE stat_date = ?", [$today]);
if (!$todayStats) {
    $database->execute("INSERT INTO site_stats (stat_date, page_views, unique_visitors, contact_forms) VALUES (?, 0, 0, 0)", [$today]);
    $todayStats = ['page_views' => 0, 'unique_visitors' => 0, 'contact_forms' => 0];
}

// Son aktiviteler
$recentMessages = $database->fetchAll("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 3");
$recentProjects = $database->fetchAll("SELECT * FROM projects ORDER BY created_at DESC LIMIT 3");
$recentMedia = $database->fetchAll("SELECT * FROM media_files ORDER BY created_at DESC LIMIT 3");

// Haftalık istatistik trendi
$weekStats = $database->fetchAll("SELECT stat_date, page_views, unique_visitors FROM site_stats WHERE stat_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) ORDER BY stat_date DESC");

include 'includes/header.php';
?>

<!-- Welcome Section -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h1>
        <p class="text-muted mb-0">ARTE IN YÖNETİM PANELİNE HOŞGELDİNİZ</p>
    </div>
    <div>
        <a href="site-management.php" class="btn btn-primary me-2">
            <i class="fas fa-globe me-2"></i>Site Yönetimi
        </a>
        <a href="projects.php?action=add" class="btn btn-outline-primary">
            <i class="fas fa-plus me-2"></i>Yeni Proje
        </a>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Hızlı İşlemler</h5>
        <small class="text-muted">Son güncelleme: <?= date('H:i') ?></small>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="projects.php" class="quick-action-btn">
            <i class="fas fa-building"></i>
            Projeleri Yönet
        </a>
        <a href="services.php" class="quick-action-btn">
            <i class="fas fa-tools"></i>
            Hizmetleri Düzenle
        </a>
        <a href="sliders.php" class="quick-action-btn">
            <i class="fas fa-images"></i>
            Slider Yönet
        </a>
        <a href="media.php" class="quick-action-btn">
            <i class="fas fa-upload"></i>
            Medya Yükle
        </a>
        <a href="messages.php" class="quick-action-btn">
            <i class="fas fa-envelope"></i>
            Mesajları Gör
            <?php if ($stats['messages'] > 0): ?>
                <span class="badge bg-danger ms-1"><?= $stats['messages'] ?></span>
            <?php endif; ?>
        </a>
        <a href="https://artein.tr" target="_blank" class="quick-action-btn">
            <i class="fas fa-external-link-alt"></i>
            Siteyi Görüntüle
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="stat-number"><?= $stats['projects'] ?></h3>
                    <p class="stat-label">Aktif Projeler</p>
                    <small class="text-muted">Toplam: <?= $stats['total_projects'] ?></small>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-building"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="stat-number"><?= $stats['services'] ?></h3>
                    <p class="stat-label">Hizmetler</p>
                    <small class="text-muted">Aktif hizmet sayısı</small>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-tools"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="stat-number"><?= $stats['messages'] ?></h3>
                    <p class="stat-label">Okunmamış</p>
                    <small class="text-muted">Toplam: <?= $stats['total_messages'] ?> mesaj</small>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-envelope"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="stat-number"><?= $stats['media_files'] ?></h3>
                    <p class="stat-label">Medya Dosyası</p>
                    <small class="text-muted">Toplam dosya sayısı</small>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-photo-video"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Today's Statistics -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Bugünkü Site İstatistikleri</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="border-end">
                            <h4 class="text-primary mb-1"><?= $todayStats['page_views'] ?></h4>
                            <p class="text-muted mb-0">Sayfa Görüntüleme</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border-end">
                            <h4 class="text-success mb-1"><?= $todayStats['unique_visitors'] ?></h4>
                            <p class="text-muted mb-0">Tekil Ziyaretçi</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <h4 class="text-warning mb-1"><?= $todayStats['contact_forms'] ?></h4>
                        <p class="text-muted mb-0">İletişim Formu</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-calendar-day me-2"></i>Sistem Durumu</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <div class="status-indicator bg-success"></div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Site Durumu</h6>
                        <small class="text-muted">Aktif ve çalışıyor</small>
                    </div>
                </div>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <div class="status-indicator bg-success"></div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Veritabanı</h6>
                        <small class="text-muted">Bağlantı başarılı</small>
                    </div>
                </div>
                
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="status-indicator <?= $stats['messages'] > 0 ? 'bg-warning' : 'bg-success' ?>"></div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">Mesajlar</h6>
                        <small class="text-muted">
                            <?= $stats['messages'] > 0 ? $stats['messages'] . ' okunmamış mesaj' : 'Tüm mesajlar okundu' ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="row">
    <!-- Recent Messages -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-envelope me-2"></i>Son Mesajlar</h5>
                <a href="messages.php" class="btn btn-sm btn-outline-primary">Tümünü Gör</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentMessages)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">Henüz mesaj yok</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentMessages as $message): ?>
                            <a href="messages.php?action=view&id=<?= $message['id'] ?>" 
                               class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?= escape($message['name']) ?></h6>
                                    <small><?= formatDate($message['created_at'], 'd.m H:i') ?></small>
                                </div>
                                <p class="mb-1 text-truncate"><?= escape($message['subject'] ?: 'Konu yok') ?></p>
                                <small class="<?= $message['is_read'] ? 'text-muted' : 'text-primary fw-bold' ?>">
                                    <?= $message['is_read'] ? 'Okundu' : 'Okunmadı' ?>
                                        </small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Recent Projects -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-building me-2"></i>Son Projeler</h5>
                <a href="projects.php" class="btn btn-sm btn-outline-primary">Tümünü Gör</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentProjects)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-building fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">Henüz proje yok</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentProjects as $project): ?>
                            <a href="projects.php?action=edit&id=<?= $project['id'] ?>" 
                               class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?= escape($project['title']) ?></h6>
                                    <small><?= formatDate($project['created_at'], 'd.m') ?></small>
                                </div>
                                <p class="mb-1 text-truncate"><?= escape($project['category']) ?></p>
                                <small class="<?= $project['is_active'] ? 'text-success' : 'text-muted' ?>">
                                    <?= $project['is_active'] ? 'Aktif' : 'Pasif' ?>
                                        </small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
        </div>
    </div>
</div>

    <!-- Recent Media -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-photo-video me-2"></i>Son Medya</h5>
                <a href="media.php" class="btn btn-sm btn-outline-primary">Tümünü Gör</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentMedia)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-photo-video fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">Henüz medya yok</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentMedia as $media): ?>
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?= escape($media['original_name']) ?></h6>
                                    <small><?= formatDate($media['created_at'], 'd.m') ?></small>
                                        </div>
                                <p class="mb-1 text-truncate"><?= escape($media['file_type']) ?></p>
                                <small class="text-muted"><?= formatFileSize($media['file_size']) ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<style>
.status-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.stat-icon {
    font-size: 2rem;
    opacity: 0.6;
    color: var(--primary-color);
}

.list-group-item {
    border-left: none;
    border-right: none;
}

.list-group-item:first-child {
    border-top: none;
}

.list-group-item:last-child {
    border-bottom: none;
}
</style>