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

$page_title = 'Şifre Değiştir';
$breadcrumb = [
    ['title' => 'Ana Sayfa', 'url' => 'dashboard.php'],
    ['title' => 'Şifre Değiştir']
];

// Form işlemleri
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!validateCSRF($csrf_token)) {
        setErrorMessage('Güvenlik hatası. Lütfen tekrar deneyin.');
    } else {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // Validasyon
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            setErrorMessage('Tüm alanlar zorunludur.');
        } elseif (strlen($new_password) < 6) {
            setErrorMessage('Yeni şifre en az 6 karakter olmalıdır.');
        } elseif ($new_password !== $confirm_password) {
            setErrorMessage('Yeni şifre ve şifre tekrarı eşleşmiyor.');
        } else {
            // Mevcut şifreyi kontrol et
            $admin = $database->fetchOne("SELECT * FROM admin_users WHERE id = ?", [$_SESSION['admin_id']]);
            
            if ($admin && password_verify($current_password, $admin['password'])) {
                // Yeni şifreyi hashle ve güncelle
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                if ($database->execute("UPDATE admin_users SET password = ? WHERE id = ?", [$hashed_password, $_SESSION['admin_id']])) {
                    setSuccessMessage('Şifreniz başarıyla değiştirildi.');
                } else {
                    setErrorMessage('Şifre güncellenirken hata oluştu.');
                }
            } else {
                setErrorMessage('Mevcut şifre yanlış.');
            }
        }
    }
    
    header('Location: change-password.php');
    exit;
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-key me-2"></i>
                    Şifre Değiştir
                </h1>
            </div>

            <?php displayMessages(); ?>

            <!-- Şifre Değiştirme Formu -->
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-lock me-2"></i>
                                Şifre Değiştirme
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" class="needs-validation" novalidate>
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION[CSRF_TOKEN_NAME] ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label">Mevcut Şifre</label>
                                    <input type="password" class="form-control" name="current_password" required>
                                    <div class="invalid-feedback">
                                        Mevcut şifrenizi girin.
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Yeni Şifre</label>
                                    <input type="password" class="form-control" name="new_password" 
                                           minlength="6" required>
                                    <div class="invalid-feedback">
                                        Yeni şifre en az 6 karakter olmalıdır.
                                    </div>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Şifre en az 6 karakter olmalıdır.
                                    </small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Yeni Şifre Tekrar</label>
                                    <input type="password" class="form-control" name="confirm_password" 
                                           minlength="6" required>
                                    <div class="invalid-feedback">
                                        Şifre tekrarını girin.
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Şifreyi Değiştir
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Güvenlik İpuçları -->
            <div class="row justify-content-center mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-shield-alt me-2"></i>
                                Güvenlik İpuçları
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Şifrenizi düzenli olarak değiştirin
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Güçlü ve karmaşık şifreler kullanın
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Şifrenizi kimseyle paylaşmayın
                                </li>
                                <li class="mb-0">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Oturumunuzu kullanmadığınızda kapatın
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.form-control:focus {
    border-color: #113736;
    box-shadow: 0 0 0 0.2rem rgba(17, 55, 54, 0.25);
}

.btn-primary {
    background-color: #113736;
    border-color: #113736;
}

.btn-primary:hover {
    background-color: #0d2a29;
    border-color: #0d2a29;
}
</style>

<?php include 'includes/footer.php'; ?>
