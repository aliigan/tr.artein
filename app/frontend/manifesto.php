<?php
/**
 * BuildTech CMS - Arte in Manifestosu Sayfası
 */

define('FRONTEND_ACCESS', true);
require_once '../shared/config/frontend_config.php';

// Sayfa bilgileri
$pageTitle = 'Arte in Manifestosu - Arte In';
$pageDescription = 'Arte In manifestomuz: Sanat ve mühendisliğin buluştuğu noktada değerlerimiz ve felsefemiz.';

// Site ayarlarını al
$settings = getSiteSettings();

// Manifesto slider'larını al
$manifesto_sliders = getActiveManifestoSliders();

// Manifesto içeriğini veritabanından al
$manifesto_content = $database->fetchOne("SELECT * FROM manifesto_content ORDER BY id DESC LIMIT 1") ?: [
    'title' => 'Arte in Manifestosu',
    'subtitle' => 'Sanat ve mühendisliğin buluştuğu noktada değerlerimiz ve felsefemiz',
    'content' => '<p>Biz Arte In olarak, inşaat sektöründe sadece binalar inşa etmiyoruz; yaşam alanları yaratıyoruz. Her projemizde sanat ve mühendisliğin mükemmel uyumunu sağlayarak, müşterilerimizin hayallerini gerçeğe dönüştürüyoruz.</p>',
    'image' => 'assets/images/manifesto-bg.jpg'
];

// Sayfa özel stilleri
$pageSpecificStyles = '
        .page-header {
            background: linear-gradient(rgba(17, 55, 54, 0.55), rgba(17, 55, 54, 0.45)), url("../../assets/images/kalitevemukemmellik2.jpg");
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

        .manifesto-content {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 20px;
            padding: 4rem;
            box-shadow: 0 20px 40px rgba(17, 55, 54, 0.15);
            margin-bottom: 4rem;
            border: 2px solid rgba(17, 55, 54, 0.1);
            position: relative;
            overflow: hidden;
        }

        .manifesto-content::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #113736 0%, #1e5f5d 50%, #113736 100%);
        }

        .manifesto-title {
            font-family: "Milano Sans", "Montserrat", sans-serif !important;
            font-size: 2.8rem;
            color: #113736;
            margin-bottom: 2rem;
            text-align: center;
            font-weight: 400;
            position: relative;
        }

        .manifesto-title::after {
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

        .manifesto-text {
            font-size: 1.15rem;
            line-height: 1.8;
            color: #2c3e50;
            text-align: justify;
            font-family: "Montserrat", sans-serif;
        }


        .principles-section {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 20px;
            padding: 4rem 2rem;
            box-shadow: 0 20px 40px rgba(17, 55, 54, 0.1);
            border: 2px solid rgba(17, 55, 54, 0.1);
            position: relative;
            overflow: hidden;
        }

        .principles-section::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #113736 0%, #1e5f5d 50%, #113736 100%);
            border-radius: 20px 20px 0 0;
        }

        .principles-title {
            font-family: "Milano Sans", "Montserrat", sans-serif !important;
            font-size: 2.5rem;
            color: #113736;
            text-align: center;
            margin-bottom: 3rem;
            font-weight: 400;
            position: relative;
        }

        .principles-title::after {
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

        .principles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2.5rem;
            margin-top: 3rem;
        }

        .principle-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 18px;
            padding: 2.5rem;
            box-shadow: 0 15px 30px rgba(17, 55, 54, 0.1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid rgba(17, 55, 54, 0.1);
            position: relative;
            overflow: hidden;
        }

        .principle-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 8px;
            height: 100%;
            background: linear-gradient(180deg, #113736 0%, #1e5f5d 100%);
            transition: width 0.3s ease;
        }

        .principle-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 50px rgba(17, 55, 54, 0.2);
        }

        .principle-card:hover::before {
            width: 12px;
        }


        .principle-card h4 {
            font-family: "Milano Sans", "Montserrat", sans-serif !important;
            font-size: 1.5rem;
            color: #113736;
            margin-bottom: 1rem;
            font-weight: 400;
        }

        .principle-card p {
            font-family: "Montserrat", sans-serif;
            color: #2c3e50;
            line-height: 1.7;
            font-size: 1.05rem;
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

        .quote-section {
            background: #f8f9fa;
            border-left: 5px solid #113736;
            padding: 2rem;
            margin: 2rem 0;
            font-style: italic;
            font-size: 1.1rem;
            position: relative;
        }

        .quote-section::before {
            content: "“";
            font-size: 4rem;
            color: #113736;
            position: absolute;
            top: -10px;
            left: 20px;
            opacity: 0.3;
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
        
        .breadcrumb-item a {
            color: #010101;
            text-decoration: none;
        }
        
        .breadcrumb-item.active {
            color: #010101;
        }

        /* Manifesto Slider Specific Styles */
        #manifesto-hero .carousel-caption {
            max-width: 80%;
            margin: 0 auto;
            text-align: center;
        }

        #manifesto-hero .display-text {
            font-family: "Milano Sans", "Montserrat", sans-serif !important;
            font-weight: 400 !important;
            font-size: 3.5rem !important;
            line-height: 1.2 !important;
            margin-bottom: 1.5rem !important;
            text-shadow: 0 4px 16px rgba(0,0,0,0.6) !important;
            white-space: normal;
            word-wrap: break-word;
            hyphens: auto;
        }

        #manifesto-hero .carousel-caption .lead {
            font-family: "Montserrat", sans-serif !important;
            font-size: 1.3rem !important;
            line-height: 1.4 !important;
            margin-bottom: 2rem !important;
            text-shadow: 0 2px 10px rgba(0,0,0,0.5) !important;
            max-width: 85%;
            margin-left: auto;
            margin-right: auto;
            white-space: normal;
            word-wrap: break-word;
            hyphens: auto;
        }

        /* Responsive adjustments */
        @media (max-width: 1200px) {
            #manifesto-hero .display-text {
                font-size: 3rem !important;
            }
        }

        @media (max-width: 768px) {
            #manifesto-hero .carousel-caption {
                max-width: 95%;
            }
            
            #manifesto-hero .display-text {
                font-size: 2.5rem !important;
                line-height: 1.3 !important;
            }
            
            #manifesto-hero .carousel-caption .lead {
                font-size: 1.1rem !important;
                max-width: 95%;
            }
        }

        @media (max-width: 576px) {
            #manifesto-hero .display-text {
                font-size: 2rem !important;
            }
            
            #manifesto-hero .carousel-caption .lead {
                font-size: 1rem !important;
                max-width: 98%;
            }
        }
';

// Header'ı include et
include 'includes/header.php';
?>

    <!-- Manifesto Slider -->
    <section id="manifesto-hero" class="hero-section">
        <?php if (!empty($manifesto_sliders)): ?>
        <div id="manifestoCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <?php foreach ($manifesto_sliders as $index => $slider): ?>
                    <button type="button" data-bs-target="#manifestoCarousel" data-bs-slide-to="<?= $index ?>" 
                            <?= $index === 0 ? 'class="active"' : '' ?>></button>
                <?php endforeach; ?>
            </div>
            <div class="carousel-inner">
                <?php foreach ($manifesto_sliders as $index => $slider): ?>
                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>" 
                         style="background-image: url('../../<?= escape($slider['background_image']) ?>')">
                        <div class="carousel-caption">
                            <h1 class="display-text text-white"><?= escape($slider['title']) ?></h1>
                            <?php if ($slider['subtitle']): ?>
                                <p class="lead"><?= escape($slider['subtitle']) ?></p>
                            <?php endif; ?>
                            <?php if ($slider['button_text'] && $slider['button_link'] && ($slider['button_active'] ?? 1)): ?>
                                <a href="<?= escape($slider['button_link']) ?>" class="btn-artein-primary btn-lg mt-3">
                                    <?= escape($slider['button_text']) ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (count($manifesto_sliders) > 1): ?>
                <button class="carousel-control-prev" type="button" data-bs-target="#manifestoCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                    <span class="visually-hidden">Önceki</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#manifestoCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                    <span class="visually-hidden">Sonraki</span>
                </button>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <!-- Fallback header if no sliders -->
        <section class="page-header">
            <div class="container">
                <h1 class="page-title">Arte in Manifestosu</h1>
                <p class="page-subtitle">Sanat ve mühendisliğin buluştuğu noktada değerlerimiz</p>
            </div>
        </section>
        <?php endif; ?>
    </section>

    <!-- Breadcrumb -->
    <section class="breadcrumb-section">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Ana Sayfa</a></li>
                    <li class="breadcrumb-item"><a href="biz-kimiz.php">Hakkımızda</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Manifestomuz</li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Main Content -->
    <section class="content-section">
        <div class="container">
            <div class="manifesto-content" id="manifesto-content">
                <h2 class="manifesto-title"><?= escape($manifesto_content['title']) ?></h2>
                
                <?php if ($manifesto_content['subtitle']): ?>
                    <p class="lead text-center mb-4"><?= escape($manifesto_content['subtitle']) ?></p>
                <?php endif; ?>
                
                <div class="manifesto-text">
                    <?= $manifesto_content['content'] ?>
                </div>
            </div>

            <div class="principles-section">
                <h2 class="principles-title">Değerlerimiz</h2>
                <div class="principles-grid" id="principles">
                    <div class="principle-card">
                        <h4>Kalite</h4>
                        <p>Her projede en yüksek kalite standartlarını uygular, malzeme seçiminden işçiliğe kadar her detayda mükemmelliği hedefleriz.</p>
                    </div>

                    <div class="principle-card">
                        <h4>Güvenilirlik</h4>
                        <p>Verdiğimiz sözleri tutar, projelerimizi zamanında ve bütçe dahilinde teslim ederiz. Müşterilerimizle kurduğumuz güven ilişkisi bizim en değerli varlığımızdır.</p>
                    </div>

                    <div class="principle-card">
                        <h4>Şeffaflık</h4>
                        <p>Proje süreçlerinde tam şeffaflık sağlar, müşterilerimizi her aşamada bilgilendirir ve onların görüşlerini alırız.</p>
                    </div>

                    <div class="principle-card">
                        <h4>Sürekli Gelişim</h4>
                        <p>Teknolojideki gelişmeleri takip eder, ekibimizi sürekli eğitir ve hizmet kalitemizi sürekli iyileştiririz.</p>
                    </div>

                    <div class="principle-card">
                        <h4>Müşteri Odaklılık</h4>
                        <p>Müşteri memnuniyetini ön planda tutar, onların ihtiyaçlarını anlar ve en uygun çözümleri sunarız.</p>
                    </div>

                    <div class="principle-card">
                        <h4>Çevre Bilinci</h4>
                        <p>Sürdürülebilir inşaat yöntemlerini benimser, çevre dostu malzemeler kullanır ve gelecek nesillere yaşanabilir bir dünya bırakırız.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php
// Footer'ı include et
include 'includes/footer.php';
?>