<?php
/**
 * BuildTech CMS - Admin Login
 * Admin giriş sayfası
 */

require_once 'config/config.php';

// Zaten giriş yapmışsa dashboard'a yönlendir
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // CSRF token kontrolü
    if (!validateCSRF($csrf_token)) {
        $error = 'Güvenlik hatası. Lütfen tekrar deneyin.';
    } elseif (empty($username) || empty($password)) {
        $error = 'Kullanıcı adı ve şifre gereklidir.';
    } else {
        // Kullanıcı doğrulama
        $sql = "SELECT id, username, password, full_name, role FROM admin_users WHERE username = ? AND id > 0";
        $user = $database->fetchOne($sql, [$username]);
        
        if ($user && password_verify($password, $user['password'])) {
            // Giriş başarılı
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_name'] = $user['full_name'];
            $_SESSION['admin_role'] = $user['role'];
            $_SESSION['last_activity'] = time();
            
            // Son giriş zamanını güncelle
            $database->execute("UPDATE admin_users SET last_login = NOW() WHERE id = ?", [$user['id']]);
            
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Geçersiz kullanıcı adı veya şifre.';
        }
    }
}

// Timeout mesajı
if (isset($_GET['timeout'])) {
    $error = 'Oturumunuz sona erdi. Lütfen tekrar giriş yapın.';
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Girişi - Arte In Construction</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="../../assets/brand/logos/ArteIn_logos-05.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../assets/brand/logos/ArteIn_logos-05.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../../assets/brand/logos/ArteIn_logos-05.png">
    <link rel="shortcut icon" href="../../assets/brand/logos/ArteIn_logos-05.png">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #113736;
            --secondary-color: #dfeade;
            --accent-color: #1e5f5d;
            --light-bg: #f8f9fa;
            --text-dark: #2c3e50;
            --text-light: #6c757d;
            --shadow: 0 10px 30px rgba(17, 55, 54, 0.15);
            --shadow-hover: 0 15px 40px rgba(17, 55, 54, 0.25);
            --border-radius: 15px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, rgba(17, 55, 54, 0.9), rgba(30, 95, 93, 0.8)), 
                        url('../../assets/brand/logos/ArteIn_logos-02.png');
            background-size: contain;
            background-position: center center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Montserrat', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            width: 100%;
            max-width: 420px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideUp 0.6s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 2.5rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .login-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(223, 234, 222, 0.1), rgba(17, 55, 54, 0.2));
            z-index: 1;
        }
        
        .login-header .content {
            position: relative;
            z-index: 2;
        }
        
        .login-logo img {
            height: 56px;
            width: auto;
            margin-bottom: 1rem;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .login-header h4 {
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-size: 1.8rem;
        }
        
        .login-header small {
            opacity: 0.8;
            font-size: 0.95rem;
        }
        
        .login-body {
            padding: 2.5rem 2rem;
        }
        
        .form-control {
            border-radius: 12px;
            border: 2px solid #e9ecef;
            padding: 0.875rem 1rem;
            transition: var(--transition);
            font-size: 0.95rem;
            background: rgba(255, 255, 255, 0.8);
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(17, 55, 54, 0.15);
            background: white;
        }
        
        .form-control::placeholder {
            color: var(--text-light);
            font-weight: 400;
        }
        
        .btn-login {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border: none;
            border-radius: 12px;
            padding: 0.875rem 2rem;
            font-weight: 600;
            font-size: 1rem;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }
        
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-login:hover::before {
            left: 100%;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .input-group-text {
            background: var(--secondary-color);
            border: 2px solid #e9ecef;
            border-right: none;
            color: var(--primary-color);
            font-weight: 500;
        }
        
        .input-group .form-control {
            border-left: none;
        }
        
        .input-group:focus-within .input-group-text {
            border-color: var(--primary-color);
            background: var(--primary-color);
            color: white;
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            font-weight: 500;
            animation: shake 0.5s ease-in-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        /* Responsive */
        @media (max-width: 480px) {
            .login-container {
                margin: 10px;
                max-width: calc(100% - 20px);
            }
            
            .login-header, .login-body {
                padding: 1.5rem;
            }
            
            .login-header i {
                font-size: 3rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="content">
                <div class="login-logo">
                    <?php 
                    $logoPath = function_exists('getSetting') ? getSetting('logo') : null;
                    if ($logoPath && file_exists($logoPath)): ?>
                        <img src="<?= escape($logoPath) ?>" alt="Arte In">
                    <?php elseif (file_exists('../../assets/brand/logos/ArteIn_logos-04.png')): ?>
                        <img src="../../assets/brand/logos/ArteIn_logos-04.png" alt="Arte In">
                    <?php endif; ?>
                </div>
                <h4 class="mb-0">Arte In Construction</h4>
                <small>Admin Paneli</small>
            </div>
        </div>
        
        <div class="login-body">
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?= escape($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION[CSRF_TOKEN_NAME] ?>">
                
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" class="form-control" name="username" placeholder="Kullanıcı Adı" 
                               value="<?= escape($_POST['username'] ?? '') ?>" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" class="form-control" name="password" placeholder="Şifre" required>
                    </div>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Giriş Yap
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
