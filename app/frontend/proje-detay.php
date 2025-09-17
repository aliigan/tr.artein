<?php
/**
 * BuildTech CMS - Proje Detay Sayfası
 */

define('FRONTEND_ACCESS', true);
require_once '../shared/config/frontend_config.php';

// Proje slug'ını al
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: 404.php');
    exit;
}

// Projeyi getir
$project = $database->fetchOne("SELECT * FROM projects WHERE slug = ? AND is_active = 1", [$slug]);

if (!$project) {
    header('Location: 404.php');
    exit;
}

// Proje görsellerini getir (şimdilik sadece featured_image kullanıyoruz)
$projectImages = [];

// Sayfa bilgileri
$pageTitle = $project['title'] . ' - Projeler - Arte In';
$pageDescription = $project['description'] ? substr(strip_tags($project['description']), 0, 160) : 'Arte In proje detayları';

// Site ayarlarını al
$settings = getSiteSettings();

// Diğer projeler
$otherProjects = $database->fetchAll("SELECT * FROM projects WHERE slug != ? AND is_active = 1 ORDER BY RAND() LIMIT 3", [$slug]);

// Sayfa özel stilleri
$pageSpecificStyles = '
        .page-header {
            background: linear-gradient(135deg, rgba(17, 55, 54, 0.85), rgba(17, 55, 54, 0.75)), url("../../' . escape($project['featured_image']) . '");
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            color: #fff;
            text-align: center;
            position: relative;
            overflow: hidden;
            margin-top: -65px;
        }

        .page-header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(17, 55, 54, 0.1), rgba(30, 95, 93, 0.1));
            z-index: 1;
        }

        .page-header-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .page-title {
            font-family: "Milano Sans", var(--font-display), sans-serif !important;
            font-size: 4rem;
            font-weight: 400;
            letter-spacing: 2px;
            margin-bottom: 1.5rem;
            text-shadow: 0 4px 25px rgba(0,0,0,0.7), 0 2px 10px rgba(0,0,0,0.5);
            line-height: 1.1;
            color: #ffffff !important;
        }

        .page-subtitle {
            font-family: "Montserrat", var(--font-primary), sans-serif !important;
            font-size: 1.4rem;
            font-weight: 400;
            opacity: 0.98;
            text-shadow: 0 2px 15px rgba(0,0,0,0.6), 0 1px 5px rgba(0,0,0,0.4);
            line-height: 1.6;
            margin-bottom: 2rem;
            color: #ffffff !important;
        }
        
        @media (max-width: 768px) {
            .page-header {
                margin-top: -55px;
                min-height: calc(100vh - 55px);
                background-attachment: scroll;
            }
        }

        .project-detail-card {
            background: white;
            border-radius: 15px;
            padding: 3rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 3rem;
        }

        .project-info-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .project-image {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }

        .gallery-item {
            border-radius: 10px;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .gallery-item:hover {
            transform: scale(1.05);
        }

        .other-project-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .other-project-card:hover {
            transform: translateY(-5px);
        }
';

// Sayfa özel JavaScript
$pageSpecificJS = '
    <script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>
    <script>
        $(document).ready(function() {
            $(".gallery-popup").magnificPopup({
                type: "image",
                gallery: {
                    enabled: true
                }
            });
        });
    </script>
';

// Header'ı include et
include 'includes/header.php';
?>

    <!-- Page Header -->
    <section class="page-header">
        <div class="page-header-content">
            <h1 class="page-title"><?= escape($project['title']) ?></h1>
            <p class="page-subtitle"><?= escape($project['category']) ?></p>
        </div>
    </section>

    <!-- Breadcrumb 
    <section class="breadcrumb-section">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Ana Sayfa</a></li>
                    <li class="breadcrumb-item"><a href="projeler.php">Projeler</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= escape($project['title']) ?></li>
                </ol>
            </nav>
        </div>
    </section>-->

    <!-- Main Content -->
    <section class="content-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="project-detail-card">
                        <div class="project-image mb-4">
                            <img src="../../<?= escape($project['featured_image']) ?>" alt="<?= escape($project['title']) ?>" class="img-fluid">
                        </div>
                        
                        <div class="project-content">
                            <?= $project['content'] ?>
                        </div>

                        <?php if (!empty($projectImages)): ?>
                            <h3 class="section-title">Proje Galerisi</h3>
                            <div class="gallery-grid">
                                <?php foreach ($projectImages as $image): ?>
                                    <div class="gallery-item">
                                        <a href="../../<?= escape($image['image_path']) ?>" class="gallery-popup">
                                            <img src="../../<?= escape($image['image_path']) ?>" alt="<?= escape($image['alt_text']) ?>" class="img-fluid">
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="project-info-card">
                        <h4>Proje Bilgileri</h4>
                        <hr>
                        <p><strong>Kategori:</strong> <?= escape($project['category']) ?></p>
                        <p><strong>Lokasyon:</strong> <?= escape($project['location']) ?></p>
                        <p><strong>Başlangıç:</strong> <?= date('d.m.Y', strtotime($project['start_date'])) ?></p>
                        <p><strong>Bitiş:</strong> <?= date('d.m.Y', strtotime($project['end_date'])) ?></p>
                        <p><strong>Durum:</strong> 
                            <span class="badge bg-<?= $project['status'] === 'completed' ? 'success' : ($project['status'] === 'ongoing' ? 'warning' : 'info') ?>">
                                <?= $project['status'] === 'completed' ? 'Tamamlandı' : ($project['status'] === 'ongoing' ? 'Devam Ediyor' : 'Planlanıyor') ?>
                            </span>
                        </p>
                    </div>

                    <div class="project-info-card">
                        <h4>İletişim</h4>
                        <hr>
                        <p>Bu proje hakkında daha fazla bilgi almak için bizimle iletişime geçin.</p>
                        <a href="index.php#contact" class="btn btn-primary w-100">İletişime Geç</a>
                    </div>
                </div>
            </div>

            <?php if (!empty($otherProjects)): ?>
                <div class="row mt-5">
                    <div class="col-12">
                        <h2 class="section-title">Diğer Projeler</h2>
                        <div class="row">
                            <?php foreach ($otherProjects as $otherProject): ?>
                                <div class="col-lg-4 col-md-6 mb-4">
                                    <div class="other-project-card">
                                        <div class="project-image" style="height: 200px; background-image: url('../../<?= escape($otherProject['featured_image']) ?>'); background-size: cover; background-position: center;"></div>
                                        <div class="p-3">
                                            <h5><?= escape($otherProject['title']) ?></h5>
                                            <p class="text-muted"><?= escape($otherProject['category']) ?></p>
                                            <a href="proje-detay.php?slug=<?= escape($otherProject['slug']) ?>" class="btn btn-outline-primary btn-sm">Detayları Gör</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

<?php
// Footer'ı include et
include 'includes/footer.php';
?>