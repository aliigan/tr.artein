<?php
/**
 * BuildTech CMS - Contact Messages Management
 * Admin panelinde iletişim mesajları yönetimi
 */

define('ADMIN_PANEL', true);
require_once 'config/config.php';
requireLogin();

$page_title = 'İletişim Mesajları';
$breadcrumb = [
    ['title' => 'Ana Sayfa', 'url' => 'dashboard.php'],
    ['title' => 'İletişim Mesajları']
];

// İşlem türü
$action = $_GET['action'] ?? 'list';
$message_id = $_GET['id'] ?? 0;

// Mesaj silme işlemi
if ($action === 'delete' && $message_id) {
    $csrf_token = $_GET['token'] ?? '';
    
    if (validateCSRF($csrf_token)) {
        if ($database->execute("DELETE FROM contact_messages WHERE id = ?", [$message_id])) {
            setSuccessMessage('Mesaj başarıyla silindi.');
        } else {
            setErrorMessage('Mesaj silinirken bir hata oluştu.');
        }
    } else {
        setErrorMessage('Güvenlik hatası.');
    }
    
    header('Location: messages.php');
    exit;
}

// Mesaj okundu olarak işaretleme
if ($action === 'mark_read' && $message_id) {
    $csrf_token = $_GET['token'] ?? '';
    
    if (validateCSRF($csrf_token)) {
        if ($database->execute("UPDATE contact_messages SET is_read = 1 WHERE id = ?", [$message_id])) {
            setSuccessMessage('Mesaj okundu olarak işaretlendi.');
        } else {
            setErrorMessage('Mesaj güncellenirken bir hata oluştu.');
        }
    } else {
        setErrorMessage('Güvenlik hatası.');
    }
    
    header('Location: messages.php');
    exit;
}

// Mesaj okunmadı olarak işaretleme
if ($action === 'mark_unread' && $message_id) {
    $csrf_token = $_GET['token'] ?? '';
    
    if (validateCSRF($csrf_token)) {
        if ($database->execute("UPDATE contact_messages SET is_read = 0 WHERE id = ?", [$message_id])) {
            setSuccessMessage('Mesaj okunmadı olarak işaretlendi.');
        } else {
            setErrorMessage('Mesaj güncellenirken bir hata oluştu.');
        }
    } else {
        setErrorMessage('Güvenlik hatası.');
    }
    
    header('Location: messages.php');
    exit;
}

// Cevaplandı olarak işaretleme
if ($action === 'mark_replied' && $message_id) {
    $csrf_token = $_GET['token'] ?? '';
    
    if (validateCSRF($csrf_token)) {
        if ($database->execute("UPDATE contact_messages SET replied_at = NOW(), is_read = 1 WHERE id = ?", [$message_id])) {
            setSuccessMessage('Mesaj cevaplandı olarak işaretlendi.');
        } else {
            setErrorMessage('Mesaj güncellenirken bir hata oluştu.');
        }
    } else {
        setErrorMessage('Güvenlik hatası.');
    }
    
    header('Location: messages.php');
    exit;
}

// Toplu işlemler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_action'])) {
    $bulk_action = $_POST['bulk_action'];
    $selected_messages = $_POST['selected_messages'] ?? [];
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (validateCSRF($csrf_token) && !empty($selected_messages)) {
        $placeholders = str_repeat('?,', count($selected_messages) - 1) . '?';
        
        switch ($bulk_action) {
            case 'mark_read':
                if ($database->execute("UPDATE contact_messages SET is_read = 1 WHERE id IN ($placeholders)", $selected_messages)) {
                    setSuccessMessage(count($selected_messages) . ' mesaj okundu olarak işaretlendi.');
                }
                break;
                
            case 'mark_unread':
                if ($database->execute("UPDATE contact_messages SET is_read = 0 WHERE id IN ($placeholders)", $selected_messages)) {
                    setSuccessMessage(count($selected_messages) . ' mesaj okunmadı olarak işaretlendi.');
                }
                break;
                
            case 'delete':
                if ($database->execute("DELETE FROM contact_messages WHERE id IN ($placeholders)", $selected_messages)) {
                    setSuccessMessage(count($selected_messages) . ' mesaj silindi.');
                }
                break;
        }
    } else {
        setErrorMessage('Lütfen en az bir mesaj seçin.');
    }
    
    header('Location: messages.php');
    exit;
}

// Sayfalama ve filtreleme
$page = (int)($_GET['page'] ?? 1);
$limit = 20;
$offset = ($page - 1) * $limit;
$filter = $_GET['filter'] ?? 'all';

// Filtreleme koşulları
$whereClause = "1 = 1";
$params = [];

switch ($filter) {
    case 'unread':
        $whereClause = "is_read = 0";
        break;
    case 'read':
        $whereClause = "is_read = 1";
        break;
    case 'replied':
        $whereClause = "replied_at IS NOT NULL";
        break;
    case 'unreplied':
        $whereClause = "replied_at IS NULL";
        break;
}

// Sayfa verilerini hazırla
if ($action === 'list') {
    // Toplam mesaj sayısı
    $totalMessages = $database->fetchOne("SELECT COUNT(*) as count FROM contact_messages WHERE $whereClause", $params)['count'];
    $totalPages = ceil($totalMessages / $limit);
    
    // Mesajlar listesi
    $messages = $database->fetchAll("SELECT * FROM contact_messages WHERE $whereClause ORDER BY created_at DESC LIMIT $limit OFFSET $offset", $params);
    
    // İstatistikler
    $stats = [
        'total' => $database->fetchOne("SELECT COUNT(*) as count FROM contact_messages")['count'],
        'unread' => $database->fetchOne("SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0")['count'],
        'read' => $database->fetchOne("SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 1")['count'],
        'replied' => $database->fetchOne("SELECT COUNT(*) as count FROM contact_messages WHERE replied_at IS NOT NULL")['count'],
        'unreplied' => $database->fetchOne("SELECT COUNT(*) as count FROM contact_messages WHERE replied_at IS NULL")['count']
    ];
} elseif ($action === 'view' && $message_id) {
    // Mesaj detayı
    $message = $database->fetchOne("SELECT * FROM contact_messages WHERE id = ?", [$message_id]);
    if (!$message) {
        setErrorMessage('Mesaj bulunamadı.');
        header('Location: messages.php');
        exit;
    }
    
    // Mesajı okundu olarak işaretle
    if (!$message['is_read']) {
        $database->execute("UPDATE contact_messages SET is_read = 1 WHERE id = ?", [$message_id]);
    }
}

include 'includes/header.php';
?>

<?php if ($action === 'list'): ?>
<!-- Mesajlar Listesi -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?= $page_title ?></h1>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-primary" onclick="location.reload()">
            <i class="fas fa-sync-alt me-2"></i>Yenile
        </button>
    </div>
</div>

<?= displayMessages() ?>

<!-- İstatistikler -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-primary"><?= $stats['total'] ?></h5>
                <p class="card-text small">Toplam</p>
                <a href="messages.php" class="btn btn-sm btn-outline-primary <?= $filter === 'all' ? 'active' : '' ?>">Tümü</a>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-warning"><?= $stats['unread'] ?></h5>
                <p class="card-text small">Okunmamış</p>
                <a href="messages.php?filter=unread" class="btn btn-sm btn-outline-warning <?= $filter === 'unread' ? 'active' : '' ?>">Görüntüle</a>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-success"><?= $stats['read'] ?></h5>
                <p class="card-text small">Okunmuş</p>
                <a href="messages.php?filter=read" class="btn btn-sm btn-outline-success <?= $filter === 'read' ? 'active' : '' ?>">Görüntüle</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-info"><?= $stats['replied'] ?></h5>
                <p class="card-text small">Cevaplandı</p>
                <a href="messages.php?filter=replied" class="btn btn-sm btn-outline-info <?= $filter === 'replied' ? 'active' : '' ?>">Görüntüle</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-danger"><?= $stats['unreplied'] ?></h5>
                <p class="card-text small">Cevaplandırılmamış</p>
                <a href="messages.php?filter=unreplied" class="btn btn-sm btn-outline-danger <?= $filter === 'unreplied' ? 'active' : '' ?>">Görüntüle</a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($messages)): ?>
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">
                    <?php if ($filter === 'all'): ?>
                        Henüz mesaj alınmamış
                    <?php else: ?>
                        Bu filtrede mesaj bulunamadı
                    <?php endif; ?>
                </h5>
                <p class="text-muted">İletişim formundan gelen mesajlar burada görünecek.</p>
                <?php if ($filter !== 'all'): ?>
                    <a href="messages.php" class="btn btn-primary">Tüm Mesajları Görüntüle</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <form method="POST" id="messagesForm">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION[CSRF_TOKEN_NAME] ?>">
                
                <!-- Toplu İşlemler -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <input type="checkbox" id="selectAll" class="form-check-input">
                        <label for="selectAll" class="form-check-label small">Tümünü Seç</label>
                    </div>
                    <div class="d-flex gap-2">
                        <select name="bulk_action" class="form-select form-select-sm" style="width: auto;">
                            <option value="">Toplu İşlem Seç</option>
                            <option value="mark_read">Okundu İşaretle</option>
                            <option value="mark_unread">Okunmadı İşaretle</option>
                            <option value="delete">Sil</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-secondary" onclick="return confirm('Seçili mesajlarda işlem yapılacak. Emin misiniz?')">
                            Uygula
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="30"><input type="checkbox" id="selectAllHeader" class="form-check-input"></th>
                                <th width="40">Durum</th>
                                <th>Gönderen</th>
                                <th>Konu</th>
                                <th>Mesaj</th>
                                <th width="120">Tarih</th>
                                <th width="150">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($messages as $msg): ?>
                                <tr class="<?= !$msg['is_read'] ? 'table-warning' : '' ?>">
                                    <td>
                                        <input type="checkbox" name="selected_messages[]" value="<?= $msg['id'] ?>" class="form-check-input message-checkbox">
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column align-items-center">
                                            <?php if (!$msg['is_read']): ?>
                                                <i class="fas fa-circle text-warning" title="Okunmamış"></i>
                                            <?php else: ?>
                                                <i class="fas fa-circle text-muted" title="Okunmuş"></i>
                                            <?php endif; ?>
                                            
                                            <?php if ($msg['replied_at']): ?>
                                                <i class="fas fa-reply text-success mt-1" title="Cevaplandı"></i>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?= escape($msg['name']) ?></strong><br>
                                            <small class="text-muted">
                                                <i class="fas fa-envelope me-1"></i><?= escape($msg['email']) ?>
                                                <?php if ($msg['phone']): ?>
                                                    <br><i class="fas fa-phone me-1"></i><?= escape($msg['phone']) ?>
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <strong><?= $msg['subject'] ? escape($msg['subject']) : '<span class="text-muted">Konu yok</span>' ?></strong>
                                    </td>
                                    <td>
                                        <div class="message-preview">
                                            <?= escape(substr($msg['message'], 0, 100)) ?>
                                            <?= strlen($msg['message']) > 100 ? '...' : '' ?>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= formatDate($msg['created_at'], 'd.m.Y') ?><br>
                                            <?= formatDate($msg['created_at'], 'H:i') ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="messages.php?action=view&id=<?= $msg['id'] ?>" 
                                               class="btn btn-outline-primary" title="Görüntüle">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <?php if (!$msg['is_read']): ?>
                                                <a href="messages.php?action=mark_read&id=<?= $msg['id'] ?>&token=<?= $_SESSION[CSRF_TOKEN_NAME] ?>" 
                                                   class="btn btn-outline-success" title="Okundu İşaretle">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="messages.php?action=mark_unread&id=<?= $msg['id'] ?>&token=<?= $_SESSION[CSRF_TOKEN_NAME] ?>" 
                                                   class="btn btn-outline-warning" title="Okunmadı İşaretle">
                                                    <i class="fas fa-undo"></i>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if (!$msg['replied_at']): ?>
                                                <a href="messages.php?action=mark_replied&id=<?= $msg['id'] ?>&token=<?= $_SESSION[CSRF_TOKEN_NAME] ?>" 
                                                   class="btn btn-outline-info" title="Cevaplandı İşaretle">
                                                    <i class="fas fa-reply"></i>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <a href="messages.php?action=delete&id=<?= $msg['id'] ?>&token=<?= $_SESSION[CSRF_TOKEN_NAME] ?>" 
                                               class="btn btn-outline-danger" 
                                               onclick="return confirm('Bu mesajı silmek istediğinizden emin misiniz?')" 
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
            </form>

            <!-- Sayfalama -->
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Sayfa navigasyonu" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="messages.php?page=<?= $page - 1 ?><?= $filter !== 'all' ? '&filter=' . $filter : '' ?>">Önceki</a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                <a class="page-link" href="messages.php?page=<?= $i ?><?= $filter !== 'all' ? '&filter=' . $filter : '' ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="messages.php?page=<?= $page + 1 ?><?= $filter !== 'all' ? '&filter=' . $filter : '' ?>">Sonraki</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php elseif ($action === 'view'): ?>
<!-- Mesaj Detayı -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Mesaj Detayı</h1>
    <a href="messages.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Geri Dön
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-envelope me-2"></i>
                    <?= $message['subject'] ?: 'Konu Belirtilmemiş' ?>
                </h5>
                <div class="d-flex gap-2">
                    <?php if (!$message['replied_at']): ?>
                        <a href="messages.php?action=mark_replied&id=<?= $message['id'] ?>&token=<?= $_SESSION[CSRF_TOKEN_NAME] ?>" 
                           class="btn btn-sm btn-info">
                            <i class="fas fa-reply me-1"></i>Cevaplandı İşaretle
                        </a>
                    <?php endif; ?>
                    <a href="messages.php?action=delete&id=<?= $message['id'] ?>&token=<?= $_SESSION[CSRF_TOKEN_NAME] ?>" 
                       class="btn btn-sm btn-danger" 
                       onclick="return confirm('Bu mesajı silmek istediğinizden emin misiniz?')">
                        <i class="fas fa-trash me-1"></i>Sil
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="message-content">
                    <?= nl2br(escape($message['message'])) ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-user me-2"></i>Gönderen Bilgileri</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>İsim:</strong><br>
                    <?= escape($message['name']) ?>
                </div>
                
                <div class="mb-3">
                    <strong>E-posta:</strong><br>
                    <a href="mailto:<?= escape($message['email']) ?>" class="text-decoration-none">
                        <i class="fas fa-envelope me-1"></i><?= escape($message['email']) ?>
                    </a>
                </div>
                
                <?php if ($message['phone']): ?>
                <div class="mb-3">
                    <strong>Telefon:</strong><br>
                    <a href="tel:<?= escape($message['phone']) ?>" class="text-decoration-none">
                        <i class="fas fa-phone me-1"></i><?= escape($message['phone']) ?>
                    </a>
                </div>
                <?php endif; ?>
                
                <div class="mb-3">
                    <strong>Gönderim Tarihi:</strong><br>
                    <small class="text-muted">
                        <?= formatDate($message['created_at'], 'd.m.Y H:i') ?>
                    </small>
                </div>
                
                <?php if ($message['replied_at']): ?>
                <div class="mb-3">
                    <strong>Cevaplandı:</strong><br>
                    <small class="text-success">
                        <i class="fas fa-check me-1"></i>
                        <?= formatDate($message['replied_at'], 'd.m.Y H:i') ?>
                    </small>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Hızlı Cevap -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-reply me-2"></i>Hızlı Cevap</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="mailto:<?= escape($message['email']) ?>?subject=Re: <?= urlencode($message['subject'] ?: 'İletişim Mesajınız') ?>" 
                       class="btn btn-primary">
                        <i class="fas fa-envelope me-2"></i>E-posta Gönder
                    </a>
                    
                    <?php if ($message['phone']): ?>
                    <a href="tel:<?= escape($message['phone']) ?>" class="btn btn-success">
                        <i class="fas fa-phone me-2"></i>Ara
                    </a>
                    <?php endif; ?>
                    
                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $message['phone'] ?? '') ?>?text=<?= urlencode('Merhaba ' . $message['name'] . ', mesajınız için teşekkürler.') ?>" 
                       target="_blank" class="btn btn-success" 
                       <?= !$message['phone'] ? 'style="display:none;"' : '' ?>>
                        <i class="fab fa-whatsapp me-2"></i>WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<script>
// Tümünü seç/seçme
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.message-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

document.getElementById('selectAllHeader').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.message-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Otomatik yenileme (5 dakikada bir)
setTimeout(function() {
    if (window.location.search.indexOf('action=view') === -1) {
        location.reload();
    }
}, 300000); // 5 dakika
</script>

<style>
.message-preview {
    max-width: 300px;
    word-wrap: break-word;
}

.message-content {
    font-size: 1.1rem;
    line-height: 1.6;
    white-space: pre-wrap;
}

.table-warning {
    --bs-table-accent-bg: rgba(255, 193, 7, 0.1);
}
</style>

<?php
$todo_write = true;
include 'includes/footer.php';
?>
