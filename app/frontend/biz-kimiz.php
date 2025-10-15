<?php
/**
 * BuildTech CMS - Biz Kimiz Sayfası
 */

define('FRONTEND_ACCESS', true);
require_once '../shared/config/frontend_config.php';

// Sayfa bilgileri
$pageTitle = 'Biz Kimiz - Arte In';
$pageDescription = 'Arte In olarak kim olduğumuzu, misyonumuzu ve vizyonumuzu keşfedin.';

// Hakkımızda içeriğini veritabanından al
$about_content = $database->fetchOne("SELECT * FROM about_us_content WHERE id = 1") ?: [
    'title' => 'Hakkımızda',
    'subtitle' => 'Arte In olarak inşaat sektöründe kalite ve güvenin adıyız',
    'content' => '<p>Biz Arte In olarak, inşaat sektöründe sadece binalar inşa etmiyoruz; yaşam alanları yaratıyoruz. Her projemizde sanat ve mühendisliğin mükemmel uyumunu sağlayarak, müşterilerimizin hayallerini gerçeğe dönüştürüyoruz.</p>',
    'image' => 'assets/images/about.jpg'
];

// Site ayarlarını al
$settings = getSiteSettings();

// Sayfa özel stilleri
$pageSpecificStyles = '
        .page-header {
            background: linear-gradient(rgba(17, 55, 54, 0.55), rgba(17, 55, 54, 0.45)), url("../../assets/images/bizkimiz.jpg");
            background-size: cover;
            background-position: center;
            min-height: 380px;
            display: flex;
            align-items: center;
            color: #fff;
            text-align: center;
            margin-top: -65px;
            padding-top: 165px;
            padding-bottom: 40px;
        }
        
        @media (max-width: 768px) {
            .page-header {
                margin-top: -55px;
                padding-top: 155px;
                min-height: 300px;
                padding-bottom: 30px;
            }
        }

        .page-title {
            font-family: var(--font-display) !important;
            font-size: 3rem;
            letter-spacing: 1px;
            margin-bottom: 0.75rem;
            text-shadow: 0 4px 16px rgba(0,0,0,0.45);
            color: #ffffff;
        }

        .page-subtitle {
            font-size: 1.2rem;
            opacity: 0.95;
            text-shadow: 0 2px 10px rgba(0,0,0,0.4);
            color: #ffffff;
        }

        .content-section {
            padding: 80px 0;
            background: linear-gradient(135deg, #dfeade 0%, #c8d5c6 100%);
            position: relative;
        }

        .content-section::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #113736 0%, #1e5f5d 50%, #113736 100%);
        }

        .about-content {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 20px;
            padding: 4rem;
            box-shadow: 0 20px 40px rgba(17, 55, 54, 0.15);
            margin-bottom: 4rem;
            border: 2px solid rgba(17, 55, 54, 0.1);
            position: relative;
            overflow: hidden;
        }

        .about-content::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #113736 0%, #1e5f5d 50%, #113736 100%);
        }

        .about-title {
            font-family: "Milano Sans", "Montserrat", sans-serif !important;
            font-size: 2.8rem;
            color: #113736;
            margin-bottom: 2rem;
            text-align: center;
            font-weight: 400;
            position: relative;
        }

        .about-title::after {
            content: "";
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, #113736 0%, #1e5f5d 100%);
            border-radius: 2px;
        }

        .about-text {
            font-size: 1.15rem;
            line-height: 1.8;
            color: #2c3e50;
            text-align: justify;
            font-family: "Montserrat", sans-serif;
        }

        .about-text p {
            margin-bottom: 1.5rem;
        }

        .about-text p:last-child {
            margin-bottom: 0;
        }

        .about-text h1, .about-text h2, .about-text h3, .about-text h4, .about-text h5, .about-text h6 {
            font-family: "Milano Sans", "Montserrat", sans-serif !important;
            color: #113736;
            margin-bottom: 1rem;
            margin-top: 2rem;
        }

        .about-text h1:first-child, .about-text h2:first-child, .about-text h3:first-child {
            margin-top: 0;
        }

        .about-text strong, .about-text b {
            color: #113736;
            font-weight: 600;
        }

        .about-text ul, .about-text ol {
            margin-bottom: 1.5rem;
            padding-left: 2rem;
        }

        .about-text li {
            margin-bottom: 0.5rem;
        }

        .about-main-image {
            max-width: 600px;
            max-height: 400px;
            width: 100%;
            height: auto;
            object-fit: cover;
        }

        .vision-mission-section {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 20px;
            padding: 4rem 2rem;
            box-shadow: 0 20px 40px rgba(17, 55, 54, 0.1);
            border: 2px solid rgba(17, 55, 54, 0.1);
            position: relative;
            overflow: hidden;
        }

        .vision-mission-section::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #113736 0%, #1e5f5d 50%, #113736 100%);
            border-radius: 20px 20px 0 0;
        }

        .vision-mission-title {
            font-family: "Milano Sans", "Montserrat", sans-serif !important;
            font-size: 2.5rem;
            color: #113736;
            text-align: center;
            margin-bottom: 3rem;
            font-weight: 400;
            position: relative;
        }

        .vision-mission-title::after {
            content: "";
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 120px;
            height: 4px;
            background: linear-gradient(90deg, #113736 0%, #1e5f5d 100%);
            border-radius: 2px;
        }

        .vision-mission-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 3rem;
            margin-top: 3rem;
        }

        .vision-mission-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 18px;
            padding: 3rem;
            box-shadow: 0 15px 30px rgba(17, 55, 54, 0.1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid rgba(17, 55, 54, 0.1);
            position: relative;
            overflow: hidden;
            transform: translateX(0);
        }

        .vision-mission-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 8px;
            height: 100%;
            background: linear-gradient(180deg, #113736 0%, #1e5f5d 100%);
            transition: width 0.3s ease;
        }

        .vision-mission-card:hover {
            transform: translateX(15px) translateY(-5px);
            box-shadow: 0 25px 50px rgba(17, 55, 54, 0.2);
        }

        .vision-mission-card:hover::before {
            width: 12px;
        }

        .vision-mission-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #113736 0%, #1e5f5d 100%);
            color: white;
            border-radius: 50%;
            font-size: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 25px rgba(17, 55, 54, 0.3);
            transition: all 0.3s ease;
        }

        .vision-mission-card:hover .vision-mission-icon {
            transform: scale(1.1);
            box-shadow: 0 15px 35px rgba(17, 55, 54, 0.4);
        }

        .vision-mission-card h4 {
            font-family: "Milano Sans", "Montserrat", sans-serif !important;
            font-size: 1.8rem;
            color: #113736;
            margin-bottom: 1.5rem;
            font-weight: 400;
        }

        .vision-mission-card p {
            font-family: "Montserrat", sans-serif;
            color: #2c3e50;
            line-height: 1.7;
            font-size: 1.1rem;
        }

        .team-section {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 20px;
            padding: 4rem 2rem;
            margin-top: 3rem;
            box-shadow: 0 20px 40px rgba(17, 55, 54, 0.1);
            border: 2px solid rgba(17, 55, 54, 0.1);
            position: relative;
            overflow: hidden;
        }

        .team-section::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #113736 0%, #1e5f5d 50%, #113736 100%);
            border-radius: 20px 20px 0 0;
        }

        .team-title {
            font-family: "Milano Sans", "Montserrat", sans-serif !important;
            font-size: 2.5rem;
            color: #113736;
            text-align: center;
            margin-bottom: 3rem;
            font-weight: 400;
            position: relative;
        }

        .team-title::after {
            content: "";
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 120px;
            height: 4px;
            background: linear-gradient(90deg, #113736 0%, #1e5f5d 100%);
            border-radius: 2px;
        }

        .team-feature {
            text-align: center;
            padding: 2rem;
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .team-feature:hover {
            transform: translateY(-5px);
            background: rgba(17, 55, 54, 0.05);
        }

        .team-feature i {
            font-size: 3rem;
            color: #113736;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .team-feature:hover i {
            color: #1e5f5d;
            transform: scale(1.1);
        }

        .team-feature h5 {
            font-family: "Milano Sans", "Montserrat", sans-serif !important;
            color: #113736;
            font-weight: 400;
            margin-bottom: 1rem;
        }

        .team-feature p {
            font-family: "Montserrat", sans-serif;
            color: #2c3e50;
        }

        .breadcrumb {
            background: transparent;
            padding: 1rem 0;
        }

        .breadcrumb-item a {
            color: #010101;
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: #010101;
        }
        
        .breadcrumb-section {
            background: #dfeade !important;
            padding: 16px 0;
            border-bottom: 1px solid #e9ecef33;
        }
        
        .breadcrumb {
            margin-bottom: 0;
            background: transparent;
            padding: 0;
        }
        .breadcrumb .breadcrumb-item,
        .breadcrumb .breadcrumb-item a {
            color: #010101;
        }
        .breadcrumb .breadcrumb-item a:hover { 
            color: #113736;
            text-decoration: underline; 
        }

        /* Mobile responsiveness fixes for vision/mission grid */
        .vision-mission-grid {
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        }
        @media (max-width: 768px) {
            .about-content { padding: 2rem; }
            .vision-mission-section { padding: 2.5rem 1.25rem; }
            .vision-mission-card { padding: 1.75rem; }
            .vision-mission-card h4 { font-size: 1.35rem; }
            .vision-mission-card p { font-size: 1rem; }
        }
        @media (max-width: 576px) {
            .vision-mission-grid { grid-template-columns: 1fr; gap: 1.25rem; }
            .vision-mission-card { padding: 1.25rem; }
        }
        /* Prevent text/box overflow on very narrow screens */
        .vision-mission-card,
        .about-text,
        .about-content {
            word-wrap: break-word;
            overflow-wrap: anywhere;
            hyphens: auto;
        }
';

// Header'ı include et
include 'includes/header.php';
?>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1 class="page-title">Biz Kimiz</h1>
            <p class="page-subtitle">Arte In olarak hikayemizi ve değerlerimizi keşfedin</p>
        </div>
    </section>

    <!-- Breadcrumb -->
    <section class="breadcrumb-section">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Ana Sayfa</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Biz Kimiz</li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Main Content -->
    <section class="content-section">
        <div class="container">
            <div class="about-content">
                <h2 class="about-title"><?= escape($about_content['title']) ?></h2>
                
                <?php if ($about_content['subtitle']): ?>
                    <p class="lead text-center mb-4"><?= escape($about_content['subtitle']) ?></p>
                <?php endif; ?>
                
                <div class="about-text">
                    <?= $about_content['content'] ?>
                </div>
                
                <?php if ($about_content['image']): ?>
                    <div class="text-center mt-4">
                        <img src="../../<?= escape($about_content['image']) ?>" 
                             alt="<?= escape($about_content['title']) ?>" 
                             class="about-main-image rounded shadow">
                    </div>
                <?php endif; ?>
            </div>

            <div class="vision-mission-section">
                <h2 class="vision-mission-title">Vizyonumuz & Misyonumuz</h2>
                <div class="vision-mission-grid">
                    <div class="vision-mission-card">
                        <div class="vision-mission-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <h4>Vizyonumuz</h4>
                        <p>İnşaat sektöründe kalite ve estetiği bir araya getirerek, müşterilerimizin hayallerini gerçeğe dönüştüren öncü bir şirket olmak. Sanat ve mühendisliğin mükemmel uyumunu sağlayarak, sektörde fark yaratan projeler üretmek.</p>
                    </div>

                    <div class="vision-mission-card">
                        <div class="vision-mission-icon">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <h4>Misyonumuz</h4>
                        <p>Müşteri memnuniyetini ön planda tutarak, yenilikçi tasarım ve kaliteli işçilikle her projede mükemmelliği hedeflemek. Modern teknoloji ile geleneksel değerleri harmanlayarak, yaşam alanlarını daha güzel ve fonksiyonel hale getirmek.</p>
                    </div>
                </div>
            </div>

            <div class="team-section">
                <h2 class="team-title">Ekibimiz</h2>
                <p class="text-center mb-4">Deneyimli ve uzman ekibimiz, her projede en yüksek kalite standartlarını sağlamak için çalışmaktadır. Mimarlarımızdan mühendislerimize, işçilerimizden proje yöneticilerimize kadar tüm ekibimiz, müşteri memnuniyetini ön planda tutarak hizmet vermektedir.</p>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="team-feature">
                            <i class="fas fa-users"></i>
                            <h5>Uzman Ekip</h5>
                            <p>Alanında deneyimli profesyoneller</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="team-feature">
                            <i class="fas fa-tools"></i>
                            <h5>Modern Teknoloji</h5>
                            <p>En son teknoloji ve ekipmanlar</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="team-feature">
                            <i class="fas fa-award"></i>
                            <h5>Kalite Garantisi</h5>
                            <p>Her projede kalite standartları</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php
// Footer'ı include et
include 'includes/footer.php';
?>