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
        }

        .manifesto-content {
            background: white;
            border-radius: 15px;
            padding: 4rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 3rem;
        }

        .manifesto-title {
            font-family: var(--font-display) !important;
            font-size: 2.5rem;
            color: #113736;
            margin-bottom: 2rem;
            text-align: center;
        }

        .manifesto-text {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #333;
            text-align: justify;
        }


        .principles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .principle-card {
            background: white;
            border-radius: 15px;
            padding: 2.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            border-left: 5px solid #113736;
        }

        .principle-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }

        .principle-number {
            display: inline-block;
            width: 50px;
            height: 50px;
            background: #113736;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 50px;
            font-weight: bold;
            font-size: 1.2rem;
            margin-bottom: 1rem;
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
';

// Header'ı include et
include 'includes/header.php';
?>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1 class="page-title">Arte in Manifestosu</h1>
            <p class="page-subtitle">Sanat ve mühendisliğin buluştuğu noktada değerlerimiz</p>
        </div>
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
            <div class="manifesto-content">
                <h2 class="manifesto-title"><?= escape($manifesto_content['title']) ?></h2>
                
                <?php if ($manifesto_content['subtitle']): ?>
                    <p class="lead text-center mb-4"><?= escape($manifesto_content['subtitle']) ?></p>
                <?php endif; ?>
                
                <div class="manifesto-text">
                    <?= $manifesto_content['content'] ?>
                </div>
            </div>

            <div class="principles-grid">
                <div class="principle-card">
                    <div class="principle-number">1</div>
                    <h4>Kalite</h4>
                    <p>Her projede en yüksek kalite standartlarını uygular, malzeme seçiminden işçiliğe kadar her detayda mükemmelliği hedefleriz.</p>
                </div>

                <div class="principle-card">
                    <div class="principle-number">2</div>
                    <h4>Güvenilirlik</h4>
                    <p>Verdiğimiz sözleri tutar, projelerimizi zamanında ve bütçe dahilinde teslim ederiz. Müşterilerimizle kurduğumuz güven ilişkisi bizim en değerli varlığımızdır.</p>
                </div>

                <div class="principle-card">
                    <div class="principle-number">3</div>
                    <h4>Şeffaflık</h4>
                    <p>Proje süreçlerinde tam şeffaflık sağlar, müşterilerimizi her aşamada bilgilendirir ve onların görüşlerini alırız.</p>
                </div>

                <div class="principle-card">
                    <div class="principle-number">4</div>
                    <h4>Sürekli Gelişim</h4>
                    <p>Teknolojideki gelişmeleri takip eder, ekibimizi sürekli eğitir ve hizmet kalitemizi sürekli iyileştiririz.</p>
                </div>

                <div class="principle-card">
                    <div class="principle-number">5</div>
                    <h4>Müşteri Odaklılık</h4>
                    <p>Müşteri memnuniyetini ön planda tutar, onların ihtiyaçlarını anlar ve en uygun çözümleri sunarız.</p>
                </div>

                <div class="principle-card">
                    <div class="principle-number">6</div>
                    <h4>Çevre Bilinci</h4>
                    <p>Sürdürülebilir inşaat yöntemlerini benimser, çevre dostu malzemeler kullanır ve gelecek nesillere yaşanabilir bir dünya bırakırız.</p>
                </div>
            </div>
        </div>
    </section>

<?php
// Footer'ı include et
include 'includes/footer.php';
?>