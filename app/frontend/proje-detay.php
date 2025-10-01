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
            background: linear-gradient(135deg, rgba(17, 55, 54, 0.9), rgba(17, 55, 54, 0.8)), url("../../' . escape($project['featured_image']) . '");
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            color: var(--artein-white);
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
            background: linear-gradient(45deg, rgba(17, 55, 54, 0.2), rgba(30, 95, 93, 0.15));
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
            font-family: var(--font-display) !important;
            font-size: 4rem;
            font-weight: var(--fw-regular);
            letter-spacing: 2px;
            margin-bottom: 1.5rem;
            text-shadow: 0 4px 25px rgba(0,0,0,0.7), 0 2px 10px rgba(0,0,0,0.5);
            line-height: 1.1;
            color: var(--artein-white) !important;
        }

        .page-subtitle {
            font-family: var(--font-primary) !important;
            font-size: 1.4rem;
            font-weight: var(--fw-regular);
            opacity: 0.95;
            text-shadow: 0 2px 15px rgba(0,0,0,0.6), 0 1px 5px rgba(0,0,0,0.4);
            line-height: 1.6;
            margin-bottom: 2rem;
            color: var(--artein-white) !important;
        }
        
        @media (max-width: 768px) {
            .page-header {
                margin-top: -55px;
                min-height: calc(100vh - 55px);
                background-attachment: scroll;
            }
            
            .page-title {
                font-size: 2.5rem;
            }
            
            .page-subtitle {
                font-size: 1.2rem;
            }
        }

        .content-section {
            background: var(--artein-gray-100);
            padding: 4rem 0;
        }

        .project-detail-card {
            background: var(--artein-white);
            border-radius: var(--radius-xl);
            padding: 3rem;
            box-shadow: var(--shadow-lg);
            margin-bottom: 3rem;
            border: 1px solid var(--artein-gray-200);
            position: relative;
        }

        .project-detail-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(to bottom, var(--artein-dark), var(--artein-light));
            border-radius: var(--radius-sm) 0 0 var(--radius-sm);
        }

        .project-info-card {
            background: var(--artein-white);
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow-md);
            margin-bottom: 2rem;
            border: 1px solid var(--artein-gray-200);
            position: relative;
        }

        .project-info-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 3px;
            height: 100%;
            background: var(--artein-dark);
            border-radius: var(--radius-sm) 0 0 var(--radius-sm);
        }

        .project-info-card h4 {
            color: var(--artein-dark);
            font-family: var(--font-display) !important;
            font-weight: var(--fw-medium);
            margin-bottom: 1rem;
        }

        .project-info-card hr {
            border-color: var(--artein-light);
            margin: 1rem 0;
        }

        .project-info-card p {
            color: var(--artein-gray-700);
            margin-bottom: 0.5rem;
        }

        .project-info-card strong {
            color: var(--artein-dark);
            font-weight: var(--fw-medium);
        }

        .project-image {
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--artein-gray-200);
        }

        .section-title {
            font-family: var(--font-display) !important;
            color: var(--artein-dark);
            font-weight: var(--fw-medium);
            margin-bottom: 2rem;
            position: relative;
        }

        .section-title::after {
            content: "";
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(to right, var(--artein-dark), var(--artein-light));
            border-radius: var(--radius-sm);
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .gallery-item {
            border-radius: var(--radius-lg);
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--artein-gray-200);
        }

        .gallery-item:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: var(--shadow-xl);
        }

        .other-project-card {
            background: var(--artein-white);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
            border: 1px solid var(--artein-gray-200);
        }

        .other-project-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-xl);
        }

        .other-project-card h5 {
            color: var(--artein-dark);
            font-family: var(--font-display) !important;
            font-weight: var(--fw-medium);
        }

        .other-project-card .text-muted {
            color: var(--artein-gray-500) !important;
        }

        .btn-outline-primary {
            border-color: var(--artein-dark);
            color: var(--artein-dark);
            font-family: var(--font-primary) !important;
            font-weight: var(--fw-medium);
        }

        .btn-outline-primary:hover {
            background-color: var(--artein-dark);
            border-color: var(--artein-dark);
            color: var(--artein-white);
        }

        .btn-primary {
            background-color: var(--artein-dark);
            border-color: var(--artein-dark);
            font-family: var(--font-primary) !important;
            font-weight: var(--fw-medium);
        }

        .btn-primary:hover {
            background-color: var(--artein-black);
            border-color: var(--artein-black);
        }

        .badge {
            font-family: var(--font-primary) !important;
            font-weight: var(--fw-medium);
        }

        .badge.bg-success {
            background-color: var(--artein-green) !important;
        }

        .badge.bg-warning {
            background-color: var(--artein-orange) !important;
        }

        .badge.bg-info {
            background-color: var(--artein-dark) !important;
        }

        /* Custom status badges with brand colors */
        .badge.bg-completed {
            background-color: var(--artein-green) !important;
            color: var(--artein-white) !important;
        }

        .badge.bg-ongoing {
            background-color: var(--artein-orange) !important;
            color: var(--artein-white) !important;
        }

        .badge.bg-planned {
            background-color: var(--artein-dark) !important;
            color: var(--artein-white) !important;
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
                        <?php if (!empty($project['start_date']) && $project['start_date'] !== '0000-00-00'): ?>
                            <p><strong>Başlangıç:</strong> <?= date('d.m.Y', strtotime($project['start_date'])) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($project['end_date']) && $project['end_date'] !== '0000-00-00'): ?>
                            <p><strong>Bitiş:</strong> <?= date('d.m.Y', strtotime($project['end_date'])) ?></p>
                        <?php endif; ?>
                        <p><strong>Durum:</strong> 
                            <span class="badge bg-<?= $project['status'] === 'completed' ? 'completed' : ($project['status'] === 'ongoing' ? 'ongoing' : 'planned') ?>">
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