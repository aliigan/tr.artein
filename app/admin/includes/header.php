<?php
/**
 * BuildTech CMS - Modern Admin Header
 * Redesigned admin panel header with improved UX
 */

if (!defined('ADMIN_PANEL')) {
    die('Direct access not allowed');
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Admin Panel' ?> - BuildTech CMS</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="../../assets/brand/logos/ArteIn_logos-05.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../assets/brand/logos/ArteIn_logos-05.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../../assets/brand/logos/ArteIn_logos-05.png">
    <link rel="shortcut icon" href="../../assets/brand/logos/ArteIn_logos-05.png">
    
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    
    <style>

        :root {
            --primary-color: #113736;
            --secondary-color: #dfeade;
            --sidebar-width: 280px;
            --accent-color: #1e5f5d;
            --light-bg: #f8f9fa;
            --card-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --card-shadow-hover: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            --border-radius: 0.75rem;
            --transition: all 0.3s ease;
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        /* Headings default */
        h1, h2, h3, h4, h5, h6 {
            font-family: inherit;
            font-weight: 700;
        }

        /* Inherit default font for UI elements */
        .nav-link, .btn, .form-control, .form-select, .form-label, .breadcrumb, .dropdown-item, small, label, input, select, textarea {
            font-family: inherit;
        }
        
        /* Modern Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            color: white;
            overflow-y: auto;
            transition: var(--transition);
            z-index: 1000;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 3px;
        }
        
        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.15);
            text-align: center;
            background: rgba(255,255,255,0.05);
        }
        .sidebar-logo img {
            max-height: 48px;
            width: auto;
            display: inline-block;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.25));
        }
        
        .sidebar-header h4 {
            margin: 0;
            font-weight: 700;
            font-size: 1.4rem;
            letter-spacing: -0.5px;
            
        }
        
        .sidebar-header small {
            opacity: 0.8;
            font-size: 0.85rem;
            margin-top: 0.5rem;
            display: block;
        }
        
        .sidebar-nav {
            padding: 1.5rem 0;
        }
        
        /* Navigation Sections */
        .nav-section {
            margin: 1rem 0;
        }
        
        .nav-section-title {
            padding: 0.5rem 1.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.6);
            margin-bottom: 0.5rem;
            
        }
        
        .nav-item {
            margin: 0.25rem 1rem;
        }
        
        .nav-sub-item {
            margin-left: 2rem;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.875rem 1rem;
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            border-radius: var(--border-radius);
            transition: var(--transition);
            font-weight: 500;
            position: relative;
        }
        
        .nav-link:hover {
            color: white;
            background: rgba(255,255,255,0.15);
            transform: translateX(4px);
        }
        
        .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--secondary-color);
            border-radius: 0 2px 2px 0;
        }
        
        .nav-link i {
            font-size: 1.1rem;
            margin-right: 0.875rem;
            width: 20px;
            text-align: center;
        }
        
        .nav-link span {
            font-size: 0.925rem;
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            background: var(--light-bg);
            transition: var(--transition);
        }
        
        .top-navbar {
            background: white;
            padding: 1rem 2rem;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--card-shadow);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .content-wrapper {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        /* Modern Cards */
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            overflow: hidden;
        }
        
        .card:hover {
            box-shadow: var(--card-shadow-hover);
            transform: translateY(-2px);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            border: none;
            padding: 1.25rem 1.5rem;
            font-weight: 600;
        }
        
        .card-header h5 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        /* Modern Buttons */
        .btn {
            border-radius: var(--border-radius);
            padding: 0.625rem 1.25rem;
            font-weight: 500;
            transition: var(--transition);
            border: none;
        }
        
        .btn-primary {
            background: var(--primary-color);
            box-shadow: 0 2px 4px rgba(17, 55, 54, 0.3);
        }
        
        .btn-primary:hover {
            background: var(--accent-color);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(17, 55, 54, 0.4);
        }
        
        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background: var(--primary-color);
            transform: translateY(-1px);
        }
        
        /* Form Elements */
        .form-control, .form-select {
            border-radius: var(--border-radius);
            border: 1px solid #e1e5e9;
            padding: 0.75rem 1rem;
            transition: var(--transition);
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(17, 55, 54, 0.25);
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }
        
        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            border-left: 4px solid var(--primary-color);
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--card-shadow-hover);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .content-wrapper {
                padding: 1rem;
            }
            
            .top-navbar {
                padding: 1rem;
            }
        }
        
        /* Mobile Toggle */
        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--primary-color);
            font-size: 1.25rem;
        }
        
        @media (max-width: 768px) {
            .mobile-toggle {
                display: block;
            }
        }
        
        /* Breadcrumb */
        .breadcrumb {
            background: none;
            padding: 0;
            margin-bottom: 1.5rem;
        }
        
        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        /* Alerts */
        .alert {
            border-radius: var(--border-radius);
            border: none;
            padding: 1rem 1.25rem;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #f8d7da, #f1aeb5);
            color: #721c24;
        }
        
        /* Navigation Divider */
        .nav-divider {
            height: 1px;
            background: rgba(255,255,255,0.15);
            margin: 1rem 1.5rem;
        }
        
        /* Quick Actions */
        .quick-actions {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
        }
        
        .quick-action-btn {
            display: inline-flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            background: var(--light-bg);
            color: var(--primary-color);
            text-decoration: none;
            border-radius: var(--border-radius);
            transition: var(--transition);
            margin: 0.25rem;
            font-weight: 500;
        }
        
        .quick-action-btn:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-1px);
        }
        
        .quick-action-btn i {
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo mb-2">
                <?php 
                $logoPath = function_exists('getSetting') ? getSetting('logo') : null;
                if ($logoPath && file_exists($logoPath)): ?>
                    <img src="<?= escape($logoPath) ?>" alt="Logo">
                <?php elseif (file_exists('../../assets/brand/logos/ArteIn_logos-04.png')): ?>
                    <img src="../../assets/brand/logos/ArteIn_logos-04.png" alt="Arte In">
                <?php else: ?>
                    <h4 class="mb-0">ARTE IN</h4>
                <?php endif; ?>
            </div>
            <small>ADMIN PANEL</small>
        </div>
        
        <div class="sidebar-nav">
            <!-- Dashboard -->
            <div class="nav-item">
                <a href="dashboard.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            
            <!-- Site Management (Merged Content & Settings) -->
            <div class="nav-item">
                <a href="site-management.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'site-management.php' ? 'active' : '' ?>">
                    <i class="fas fa-globe"></i>
                    <span>Site Yönetimi</span>
                </a>
            </div>
            
            <!-- Content Management -->
            <div class="nav-section">
                <div class="nav-section-title">İçerik</div>
                
                <div class="nav-item nav-sub-item">
                    <a href="projects.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'projects.php' ? 'active' : '' ?>">
                        <i class="fas fa-building"></i>
                        <span>Projeler</span>
                </a>
            </div>
            
                <div class="nav-item nav-sub-item">
                    <a href="services.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'services.php' ? 'active' : '' ?>">
                        <i class="fas fa-tools"></i>
                        <span>Hizmetler</span>
                </a>
            </div>
            
                <div class="nav-item nav-sub-item">
                    <a href="sliders.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'sliders.php' ? 'active' : '' ?>">
                        <i class="fas fa-images"></i>
                        <span>Ana Sayfa Slider</span>
                </a>
            </div>
            </div>
            
            <!-- Media & Communication -->
            <div class="nav-section">
                <div class="nav-section-title">Medya & İletişim</div>
            
                <div class="nav-item nav-sub-item">
                <a href="media.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'media.php' ? 'active' : '' ?>">
                    <i class="fas fa-photo-video"></i>
                        <span>Medya Galerisi</span>
                </a>
            </div>
            
                <div class="nav-item nav-sub-item">
                <a href="messages.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'active' : '' ?>">
                    <i class="fas fa-envelope"></i>
                        <span>İletişim Mesajları</span>
                </a>
            </div>
            </div>
            
            <div class="nav-divider"></div>
            
            <!-- External Links -->
            <div class="nav-item">
                <a href="<?= SITE_URL ?>" target="_blank" class="nav-link">
                    <i class="fas fa-external-link-alt"></i>
                    <span>Siteyi Görüntüle</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="logout.php" class="nav-link" onclick="return confirm('Çıkış yapmak istediğinizden emin misiniz?')">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Çıkış Yap</span>
                </a>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="top-navbar">
            <div class="d-flex align-items-center">
                <button class="btn btn-link mobile-toggle me-3" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                <h6 class="mb-0 text-muted">Hoş geldiniz, <?= escape($_SESSION['admin_name']) ?></h6>
                    <?php if (isset($breadcrumb) && !empty($breadcrumb)): ?>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <?php foreach ($breadcrumb as $index => $item): ?>
                                    <?php if ($index === count($breadcrumb) - 1): ?>
                                        <li class="breadcrumb-item active" aria-current="page"><?= escape($item['title']) ?></li>
                                    <?php else: ?>
                                        <li class="breadcrumb-item">
                                            <a href="<?= escape($item['url'] ?? '#') ?>"><?= escape($item['title']) ?></a>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ol>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="d-flex align-items-center">
                <span class="text-muted me-3">
                    <i class="fas fa-clock me-1"></i>
                    <?= date('d.m.Y H:i') ?>
                </span>
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i>
                        <?= escape($_SESSION['admin_username']) ?>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="site-management.php"><i class="fas fa-cog me-2"></i>Site Yönetimi</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Çıkış Yap</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Content Wrapper -->
        <div class="content-wrapper">
            
            <script>
                function toggleSidebar() {
                    const sidebar = document.getElementById('sidebar');
                    sidebar.classList.toggle('mobile-open');
                }
                
                // Close sidebar when clicking outside on mobile
                document.addEventListener('click', function(event) {
                    const sidebar = document.getElementById('sidebar');
                    const toggle = document.querySelector('.mobile-toggle');
                    
                    if (window.innerWidth <= 768 && 
                        !sidebar.contains(event.target) && 
                        !toggle.contains(event.target)) {
                        sidebar.classList.remove('mobile-open');
                    }
                });
                
                // Delete confirmation function
                function confirmDelete(message) {
                    return confirm(message || 'Bu öğeyi silmek istediğinizden emin misiniz?');
                }
            </script>