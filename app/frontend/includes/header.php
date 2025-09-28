<?php
/**
 * BuildTech CMS - Unified Frontend Header
 * Index.php'den alınan ortak header - tüm frontend sayfalar için
 */

if (!defined('FRONTEND_ACCESS')) {
    die('Direct access not allowed');
}

// Simple page view/unique visitor tracker for today's stats
try {
    if (isset($database)) {
        $today = date('Y-m-d');
        // Ensure row exists
        $database->execute("INSERT INTO site_stats (stat_date, page_views, unique_visitors, contact_forms) VALUES (?, 0, 0, 0) ON DUPLICATE KEY UPDATE stat_date = stat_date", [$today]);

        // Increment page views
        $database->execute("UPDATE site_stats SET page_views = page_views + 1 WHERE stat_date = ?", [$today]);

        // Unique visitor per day (cookie-based)
        $dailyCookie = 'uv_' . $today;
        if (empty($_COOKIE[$dailyCookie])) {
            $database->execute("UPDATE site_stats SET unique_visitors = unique_visitors + 1 WHERE stat_date = ?", [$today]);
            // Cookie expires at end of day
            $expire = strtotime('tomorrow 00:00:00');
            setcookie($dailyCookie, '1', $expire, '/', '', false, true);
        }
    }
} catch (Throwable $e) {
    // Fail silently; do not break frontend rendering
}

// Varsayılan değerleri ayarla
$pageTitle = $pageTitle ?? 'Arte In';
$pageDescription = $pageDescription ?? 'Modern İnşaat Çözümleri';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= generateMetaTags($pageTitle, $pageDescription) ?>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="../../assets/brand/logos/ArteIn_logos-05.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../assets/brand/logos/ArteIn_logos-05.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../../assets/brand/logos/ArteIn_logos-05.png">
    <link rel="shortcut icon" href="../../assets/brand/logos/ArteIn_logos-05.png">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Brand Fonts (önce yükle) -->
    <link rel="preload" href="../../assets/webfonts/Montserrat-Regular.ttf" as="font" type="font/ttf" crossorigin>
    <link rel="preload" href="../../assets/webfonts/Montserrat-Medium.ttf" as="font" type="font/ttf" crossorigin>
    <link rel="preload" href="../../assets/webfonts/Montserrat-SemiBold.ttf" as="font" type="font/ttf" crossorigin>
    <link rel="preload" href="../../assets/webfonts/MilanoSans-Regular.ttf" as="font" type="font/ttf" crossorigin>
    
    <!-- Brand CSS (fontları tanımla) -->
    <link rel="stylesheet" href="../../assets/css/artein-brand.css?v=<?= time() ?>">
    
    <!-- Font Awesome 6.5.1 (brand CSS'ten sonra) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer">
    
    <!-- Font Awesome Fallback -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.1/css/all.css" crossorigin="anonymous">
    <link rel="stylesheet" href="../../assets/css/all.min.css">
    
    <!-- Font Awesome Yükleme Kontrolü ve Optimizasyon -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Font Awesome ikonlarının yüklenmesini kontrol et
            function checkFontAwesome() {
                const testIcon = document.createElement('i');
                testIcon.className = 'fas fa-test';
                testIcon.style.position = 'absolute';
                testIcon.style.left = '-9999px';
                testIcon.style.visibility = 'hidden';
                document.body.appendChild(testIcon);
                
                const computedStyle = window.getComputedStyle(testIcon, ':before');
                const isLoaded = computedStyle.getPropertyValue('font-family').includes('Font Awesome');
                
                document.body.removeChild(testIcon);
                
                if (!isLoaded) {
                    console.log('Font Awesome yükleme hatası tespit edildi, tekrar yükleniyor...');
                    // Yerel Font Awesome dosyasını yükle
                    const link = document.createElement('link');
                    link.rel = 'stylesheet';
                    link.href = '../../assets/css/all.min.css';
                    link.onload = function() {
                        console.log('Font Awesome yerel dosya yüklendi');
                    };
                    document.head.appendChild(link);
                }
            }
            
            // Sayfa yüklendikten 500ms sonra kontrol et
            setTimeout(checkFontAwesome, 500);
            
            // Tüm Font Awesome ikonlarına debugging sınıfı ekle
            const icons = document.querySelectorAll('[class*="fa-"], .fa, .fas, .far, .fab');
            icons.forEach(icon => {
                icon.classList.add('fa-icon-loaded');
            });
        });
    </script>
    
    <!-- Font Awesome ve Brand Font Optimization CSS -->
    <style>
        /* Font Awesome Icon Families - En Güçlü Tanımlar */
        .fa, .fas, .far, .fab, .fal, .fad, .fat, 
        i.fa, i.fas, i.far, i.fab, i.fal, i.fad, i.fat,
        [class^="fa-"], [class*=" fa-"] {
            font-family: "Font Awesome 6 Free", "Font Awesome 6 Pro", "Font Awesome 6 Brands", "FontAwesome" !important;
            font-style: normal !important;
            font-variant: normal !important;
            text-rendering: auto !important;
            -webkit-font-smoothing: antialiased !important;
            -moz-osx-font-smoothing: grayscale !important;
            display: inline-block !important;
            line-height: 1 !important;
            text-transform: none !important;
        }
        
        /* Font Awesome Weight Fixes */
        .fas, .fa-solid { font-weight: 900 !important; }
        .far, .fa-regular { font-weight: 400 !important; }
        .fab, .fa-brands { 
            font-family: "Font Awesome 6 Brands", "Font Awesome 6 Free" !important;
            font-weight: 400 !important; 
        }
        .fal, .fa-light { font-weight: 300 !important; }
        .fad, .fa-duotone { font-weight: 900 !important; }
        .fat, .fa-thin { font-weight: 100 !important; }
        
        /* Brand Font Protection - Sadece Text İçin */
        body, p, h1, h2, h3, h4, h5, h6, span:not([class*="fa-"]), 
        div:not([class*="fa-"]), a:not([class*="fa-"]),
        .nav-link, .navbar-brand, .btn, .card, .form-control {
            font-family: var(--font-primary, 'Montserrat', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif) !important;
        }
        
        /* Display Font Milano Sans */
        .display-text, .page-title, .section-title, 
        .hero-title, .brand-title {
            font-family: var(--font-display, 'Milano Sans', 'Montserrat', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif) !important;
        }

        /* Başlıklar (h1-h6) Milano Sans */
        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-display, 'Milano Sans', 'Montserrat', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif) !important;
            font-weight: 400 !important;
        }

        /* Başlıklarda metin-imleci görünmesin, seçim serbest */
        h1, h2, h3, h4, h5, h6,
        .page-title, .section-title, .hero-title, .brand-title {
            cursor: default;
        }

        /* Global: seçim serbest, caret sadece form alanlarında görünsün */
        html, body { cursor: default; }
        /* Tüm elemanlarda caret'ı gizle */
        *:not(input):not(textarea):not([contenteditable="true"]) { caret-color: transparent; }
        /* Form alanlarında caret ve metin imleci açık kalsın */
        input, textarea, [contenteditable="true"] {
            caret-color: auto;
            cursor: text;
            -webkit-user-select: text;
            -ms-user-select: text;
            user-select: text;
        }
        /* Tıklanabilir link ve butonlar pointer göstersin */
        a, button, .btn, .nav-link, .dropdown-item {
            cursor: pointer;
        }

        /* Menüler Milano Sans Regular */
        .navbar .nav-link, .dropdown-item, .navbar .navbar-brand {
            font-family: var(--font-display, 'Milano Sans', 'Montserrat', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif) !important;
            font-weight: 400 !important;
        }
        
        /* Tüm Font Awesome İkonları için Güçlü Kurallar */
        .service-icon i, .footer-contact i, .social-link i,
        .value-icon i, .error-actions i, .filter-btn i,
        .view-btn i, .page-link i, .empty-state i,
        .alert i, .btn i, .project-meta i,
        i.fas, i.far, i.fab, i.fal, i.fad, i.fat {
            font-family: "Font Awesome 6 Free", "Font Awesome 6 Pro", "Font Awesome 6 Brands", "FontAwesome" !important;
            font-style: normal !important;
            font-variant: normal !important;
            text-rendering: auto !important;
            -webkit-font-smoothing: antialiased !important;
            -moz-osx-font-smoothing: grayscale !important;
            display: inline-block !important;
            text-transform: none !important;
        }
        
        /* Service İconları */
        .service-icon i {
            font-size: 2.5rem !important;
            color: var(--artein-dark) !important;
            font-weight: 900 !important;
            line-height: 1 !important;
        }
        
        /* Footer Contact İconları */
        .footer-contact i {
            font-size: 1.1rem !important;
            color: var(--artein-light) !important;
            font-weight: 900 !important;
            line-height: 1 !important;
            margin-right: 0.5rem !important;
        }
        
        /* Social Media İconları (Brands) */
        .social-link i {
            font-size: 1.5rem !important;
            color: white !important;
            font-weight: 400 !important;
            line-height: 1 !important;
        }
        
        /* Social Link Container Styles - Override */
        .social-link {
            background: var(--artein-dark) !important;
            border: 2px solid #fff !important;
        }
        
        .social-link:hover {
            background: #fff !important;
            color: var(--artein-dark) !important;
        }
        
        .social-link:hover i {
            color: var(--artein-dark) !important;
        }
        
        /* Button İconları */
        .btn i, .filter-btn i, .view-btn i, .page-link i {
            font-weight: 900 !important;
            line-height: 1 !important;
        }
        
        /* Brand İconları için özel kural */
        i.fab, .fab, [class*="fa-facebook"], [class*="fa-whatsapp"], 
        [class*="fa-instagram"], [class*="fa-twitter"], [class*="fa-linkedin"] {
            font-family: "Font Awesome 6 Brands", "Font Awesome 6 Free" !important;
            font-weight: 400 !important;
        }
        
        /* Light icons için font-weight */
        .fal {
            font-weight: 300 !important;
        }
        
        /* Duotone icons için font-weight */
        .fad {
            font-weight: 900 !important;
        }
        
        /* Thin icons için font-weight */
        .fat {
            font-weight: 100 !important;
        }
    </style>
    
    <!-- Ana CSS Dosyaları (sıralı yükleme) -->
    <link rel="stylesheet" href="../../assets/css/common.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../../assets/css/main.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../../assets/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../../assets/css/responsive-header.css?v=<?= time() ?>">
    <style>
        /* Brand Font Definitions - Optimize edilmiş */
        @font-face {
            font-family: 'Montserrat';
            src: url('../../assets/webfonts/Montserrat-Regular.ttf') format('truetype');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'Montserrat';
            src: url('../../assets/webfonts/Montserrat-Medium.ttf') format('truetype');
            font-weight: 500;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'Montserrat';
            src: url('../../assets/webfonts/Montserrat-SemiBold.ttf') format('truetype');
            font-weight: 600;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'Milano Sans';
            src: url('../../assets/webfonts/MilanoSans-Regular.ttf') format('truetype');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }

        :root {
            --font-primary: 'Milano Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-display: 'Montserrat', 'Milano Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --navbar-height: 65px;
        }

        /* Body override - diğer CSS dosyalarını geçersiz kıl */
        body {
            background-color: var(--artein-light) !important;
            font-family: var(--font-primary) !important;
            font-weight: 400 !important;
            line-height: 1.6 !important;
            overflow-x: hidden !important;
            padding-top: 0 !important;
            margin: 0 !important;
        }
        
        /* Tüm sayfalar için content wrapper - navbar yüksekliği kadar margin */
        .main-content {
            margin-top: 65px !important;
        }
        
        /* Ana sayfa için özel durum - hero section navbar altından başlasın */
        body.homepage .main-content {
            margin-top: 0 !important;
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-top: 55px !important;
            }
            body.homepage .main-content {
                margin-top: 0 !important;
            }
            :root { --navbar-height: 55px; }
        }
        
        /* Modern Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #113736, #1e5f5d);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #0a2524, #113736);
        }
        
        /* Smooth Page Transitions */
        * {
            transition: all 0.3s ease;
        }

        /* Anchor offset: başlıklar navbar altında kalmasın (daha az boşluk) */
        [id] { scroll-margin-top: calc(var(--navbar-height) ); }
        #services, #contact { scroll-margin-top: calc(var(--navbar-height) ) !important; }

        /* Tüm butonlar: #113736 */
        .btn,
        .btn-primary,
        .btn-secondary,
        .btn-outline-primary,
        .btn-outline-secondary,
        a.boxed-btn,
        a.bordered-btn,
        a.cart-btn,
        .carousel-caption .btn,
        input[type="submit"] {
            background-color: #113736 !important;
            border-color: #113736 !important;
            color: #ffffff !important;
        }

        .btn:hover,
        .btn-primary:hover,
        .btn-secondary:hover,
        .btn-outline-primary:hover,
        .btn-outline-secondary:hover,
        a.boxed-btn:hover,
        a.bordered-btn:hover,
        a.cart-btn:hover,
        .carousel-caption .btn:hover,
        input[type="submit"]:hover {
            background-color: #0a2524 !important;
            border-color: #0a2524 !important;
            color: #dfeade !important;
        }

        /* Navbar override - diğer CSS dosyalarını geçersiz kıl */
        .navbar {
            background: linear-gradient(135deg, var(--artein-dark) 0%, var(--artein-black) 100%) !important;
            padding: 0.4rem 0 !important;
            backdrop-filter: blur(10px) !important;
            box-shadow: 0 2px 20px rgba(17, 55, 54, 0.3) !important;
            transition: all 0.3s ease !important;
            min-height: 65px !important;
            height: 65px !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 1050 !important;
            width: 100% !important;
        }
        .navbar-brand {
            font-family: var(--font-display) !important;
            font-weight: 700;
            font-size: 1.8rem;
            color: #fff !important;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .navbar-brand:hover {
            transform: scale(1.05);
            color: var(--artein-light) !important;
        }
        .navbar-brand img {
            height: 32px;
            transition: all 0.3s ease;
        }
        .navbar-brand:hover img {
            transform: scale(1.1);
        }
        .nav-link {
            color: #fff !important;
            font-weight: 600;
            margin: 0 0.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-family: var(--font-display) !important;
            font-size: 0.95rem;
            position: relative;
            padding: 0.4rem 0.7rem !important;
            border-radius: 25px;
        }
        .nav-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--artein-light), #ffffff);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        .nav-link:hover::before {
            width: 80%;
        }
        .nav-link:hover {
            color: #dfeade !important;
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }
        .dropdown-menu {
            background: rgba(17, 55, 54, 0.95);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            padding: 0.5rem 0;
            margin-top: 0.5rem;
        }
        .dropdown-item {
            color: #fff !important;
            font-family: var(--font-display) !important;
            font-weight: 500 !important;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
            border-radius: 10px;
            margin: 0.2rem 0.5rem;
        }
        .dropdown-item:hover {
            background: linear-gradient(135deg, #1e5f5d, #113736);
            color: #dfeade !important;
            transform: translateX(5px);
        }

        /* Desktop: Hover ile dropdown açılması */
        @media (min-width: 992px) {
            .navbar .dropdown:hover .dropdown-menu {
                display: block;
                margin-top: 0.5rem;
            }
            .navbar .dropdown-toggle::after {
                transition: transform 0.2s ease;
            }
            .navbar .dropdown.show .dropdown-toggle::after,
            .navbar .dropdown:hover .dropdown-toggle::after {
                transform: rotate(180deg);
            }
        }

        /* Mobile responsive adjustments */
        @media (max-width: 768px) {
            .navbar {
                padding: 0.25rem 0 !important;
                min-height: 55px !important;
                height: 55px !important;
            }
            .navbar-brand {
                font-size: 1.5rem !important;
            }
            .navbar-brand img {
                height: 28px !important;
            }
            .nav-link {
                margin: 0 0.3rem !important;
                padding: 0.35rem 0.5rem !important;
                font-size: 0.9rem !important;
            }
            
            /* Custom Mobile Menu Styles */
            .custom-toggler {
                border: none !important;
                padding: 0.4rem !important;
                background: rgba(255, 255, 255, 0.1) !important;
                border-radius: 8px !important;
                transition: all 0.3s ease !important;
            }
            
            .custom-toggler:hover {
                background: rgba(255, 255, 255, 0.2) !important;
                transform: scale(1.05) !important;
            }
            
            .custom-toggler:focus {
                box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25) !important;
            }
            
            .navbar-toggler-icon {
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.85%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
                width: 1.5em !important;
                height: 1.5em !important;
            }
            
            /* Mobile Menu Collapse */
            .navbar-collapse {
                background: linear-gradient(135deg, rgba(17, 55, 54, 0.98) 0%, rgba(30, 95, 93, 0.98) 100%) !important;
                backdrop-filter: blur(15px) !important;
                border-radius: 15px !important;
                margin-top: 10px !important;
                padding: 1rem !important;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
                border: 1px solid rgba(255, 255, 255, 0.1) !important;
            }
            
            .navbar-nav {
                width: 100% !important;
                text-align: center !important;
            }
            
            .nav-item {
                margin: 0.3rem 0 !important;
                width: 100% !important;
            }
            
            .nav-link {
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                padding: 0.8rem 1rem !important;
                margin: 0 !important;
                border-radius: 10px !important;
                background: rgba(255, 255, 255, 0.05) !important;
                border: 1px solid rgba(255, 255, 255, 0.1) !important;
                transition: all 0.3s ease !important;
                font-weight: 600 !important;
                font-size: 1rem !important;
            }
            
            .nav-link:hover {
                background: rgba(255, 255, 255, 0.15) !important;
                transform: translateY(-2px) !important;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2) !important;
            }
            
            .nav-link.active {
                background: linear-gradient(135deg, #1e5f5d, #113736) !important;
                color: #dfeade !important;
                box-shadow: 0 5px 15px rgba(30, 95, 93, 0.3) !important;
            }
            
            .nav-link i {
                font-size: 1.1rem !important;
                margin-right: 0.5rem !important;
                width: 20px !important;
                text-align: center !important;
            }
        }

        /* Extra Small Devices */
        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1.3rem;
            }
            .nav-link {
                padding: 0.3rem 0.4rem !important;
                font-size: 0.85rem;
            }
            
            /* Extra Small Mobile Menu */
            .navbar-collapse {
                margin-top: 8px !important;
                padding: 0.8rem !important;
                border-radius: 12px !important;
            }
            
            .nav-link {
                padding: 0.7rem 0.8rem !important;
                font-size: 0.95rem !important;
                margin: 0.2rem 0 !important;
            }
            
            .nav-link i {
                font-size: 1rem !important;
                margin-right: 0.4rem !important;
                width: 18px !important;
            }
            
            .custom-toggler {
                padding: 0.3rem !important;
            }
            
            .navbar-toggler-icon {
                width: 1.3em !important;
                height: 1.3em !important;
            }
        }

        /* Page specific styles placeholder */
        <?php if (isset($pageSpecificStyles)): ?>
        <?= $pageSpecificStyles ?>
        <?php endif; ?>
    </style>
    
    <!-- Google reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    
    <!-- Çerez Yönetim Sistemi -->
    <script src="includes/cookie-manager.js"></script>
</head>
<body<?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? ' class="homepage"' : '' ?>>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <?php 
                $logoPath = getSetting('logo');
                if ($logoPath && file_exists($logoPath)): ?>
                    <img src="<?= escape($logoPath) ?>" alt="<?= escape(getSetting('company_name', 'Arte In')) ?>">
                <?php elseif (file_exists('../../assets/images/ArteIn_logos-04.png')): ?>
                    <img src="../../assets/images/ArteIn_logos-04.png" alt="Arte In">
                <?php else: ?>
                    <?= escape(getSetting('company_name', 'Arte In')) ?>
                <?php endif; ?>
            </a>
            
            <button class="navbar-toggler custom-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Menüyü aç/kapat">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : '' ?>" href="index.php">
                            <i class="fas fa-home me-1"></i>Ana Sayfa
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'projeler.php') ? 'active' : '' ?>" href="projeler.php">
                            <i class="fas fa-building me-1"></i>Projelerimiz
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#services">
                            <i class="fas fa-tools me-1"></i>Hizmetler
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= (in_array(basename($_SERVER['PHP_SELF']), ['biz-kimiz.php', 'manifesto.php'])) ? 'active' : '' ?>" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-info-circle me-1"></i>Hakkımızda
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="biz-kimiz.php">Biz Kimiz</a></li>
                            <li><a class="dropdown-item" href="manifesto.php">Arte in Manifestosu</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#contact">
                            <i class="fas fa-envelope me-1"></i>İletişim
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Main Content Wrapper -->
    <main class="main-content">