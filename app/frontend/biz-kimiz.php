<?php
/**
 * BuildTech CMS - Biz Kimiz Sayfası
 */

define('FRONTEND_ACCESS', true);
require_once '../shared/config/frontend_config.php';

// Sayfa bilgileri
$pageTitle = 'Biz Kimiz - Arte In';
$pageDescription = 'Arte In olarak kim olduğumuzu, misyonumuzu ve vizyonumuzu keşfedin.';

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
        }

        .section-title {
            font-family: var(--font-display) !important;
            font-size: 2.5rem;
            color: #113736;
            margin-bottom: 2rem;
            text-align: center;
        }

        .content-card {
            background: white;
            border-radius: 15px;
            padding: 3rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .value-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .value-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }

        .value-icon {
            font-size: 3rem;
            color: #113736;
            margin-bottom: 1rem;
        }

        .team-section {
            background: white;
            border-radius: 15px;
            padding: 3rem;
            margin-top: 3rem;
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
            <div class="content-card">
                <h2 class="section-title">Hikayemiz</h2>
                <p class="lead">Arte In, sanat ve mühendisliğin buluştuğu noktada doğan bir vizyonun ürünüdür. 2020 yılında kurulan şirketimiz, inşaat sektöründe kalite ve estetiği bir araya getiren projelerle sektörde fark yaratmaktadır.</p>
                
                <p>Müşteri memnuniyetini ön planda tutan yaklaşımımız, yenilikçi tasarım anlayışımız ve kaliteli işçiliğimizle her projede mükemmelliği hedefliyoruz. Modern teknoloji ile geleneksel değerleri harmanlayarak, yaşam alanlarınızı daha güzel ve fonksiyonel hale getiriyoruz.</p>
            </div>

            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h4>Vizyonumuz</h4>
                    <p>İnşaat sektöründe kalite ve estetiği bir araya getirerek, müşterilerimizin hayallerini gerçeğe dönüştüren öncü bir şirket olmak.</p>
                </div>

                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h4>Misyonumuz</h4>
                    <p>Müşteri memnuniyetini ön planda tutarak, yenilikçi tasarım ve kaliteli işçilikle her projede mükemmelliği hedeflemek.</p>
                </div>

                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h4>Değerlerimiz</h4>
                    <p>Kalite, güvenilirlik, şeffaflık ve sürekli gelişim ilkeleriyle hareket ederek, müşterilerimizle uzun vadeli ilişkiler kurmak.</p>
                </div>
            </div>

            <div class="team-section">
                <h2 class="section-title">Ekibimiz</h2>
                <p>Deneyimli ve uzman ekibimiz, her projede en yüksek kalite standartlarını sağlamak için çalışmaktadır. Mimarlarımızdan mühendislerimize, işçilerimizden proje yöneticilerimize kadar tüm ekibimiz, müşteri memnuniyetini ön planda tutarak hizmet vermektedir.</p>
                
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="text-center">
                            <i class="fas fa-users fa-3x text-primary mb-3"></i>
                            <h5>Uzman Ekip</h5>
                            <p>Alanında deneyimli profesyoneller</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <i class="fas fa-tools fa-3x text-primary mb-3"></i>
                            <h5>Modern Teknoloji</h5>
                            <p>En son teknoloji ve ekipmanlar</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <i class="fas fa-award fa-3x text-primary mb-3"></i>
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