<?php
/**
 * BuildTech CMS - 404 Not Found Sayfası
 */

define('FRONTEND_ACCESS', true);
require_once '../shared/config/frontend_config.php';

// HTTP 404 status kodu gönder
http_response_code(404);

// Sayfa bilgileri
$pageTitle = '404 - Sayfa Bulunamadı - Arte In';
$pageDescription = 'Aradığınız sayfa bulunamadı. Ana sayfaya dönün veya menüden istediğiniz sayfaya ulaşın.';

// Site ayarlarını al
$settings = getSiteSettings();

// Popüler sayfalar
$popularPages = [
    ['title' => 'Ana Sayfa', 'url' => 'index.php', 'icon' => 'fas fa-home'],
    ['title' => 'Biz Kimiz', 'url' => 'biz-kimiz.php', 'icon' => 'fas fa-users'],
    ['title' => 'Projeler', 'url' => 'projeler.php', 'icon' => 'fas fa-building'],
    ['title' => 'İletişim', 'url' => 'index.php#contact', 'icon' => 'fas fa-envelope']
];

// Son projeler
$recentProjects = $database->fetchAll("SELECT * FROM projects WHERE is_active = 1 ORDER BY created_at DESC LIMIT 3");

// Sayfa özel stilleri
$pageSpecificStyles = '
        .error-section {
            padding: 100px 0;
            text-align: center;
        }

        .error-code {
            font-family: var(--font-display) !important;
            font-size: 8rem;
            color: #113736;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .error-title {
            font-family: var(--font-display) !important;
            font-size: 2.5rem;
            color: #113736;
            margin-bottom: 1rem;
        }

        .error-description {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 3rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .error-actions {
            margin-bottom: 4rem;
        }

        .error-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            transition: transform 0.3s;
        }

        .error-card:hover {
            transform: translateY(-5px);
        }

        .error-card h5 {
            color: #113736;
            margin-bottom: 1rem;
        }

        .error-card a {
            color: #113736;
            text-decoration: none;
            display: block;
            padding: 0.5rem 0;
            transition: color 0.3s;
        }

        .error-card a:hover {
            color: #1e5f5d;
        }

        .error-card i {
            margin-right: 0.5rem;
            width: 20px;
        }
';

// Header'ı include et
include 'includes/header.php';
?>

    <!-- Error Section -->
    <section class="error-section">
        <div class="container">
            <div class="error-code">404</div>
            <h1 class="error-title">Sayfa Bulunamadı</h1>
            <p class="error-description">
                Aradığınız sayfa mevcut değil veya taşınmış olabilir. 
                Ana sayfaya dönün veya aşağıdaki bağlantıları kullanarak istediğiniz sayfaya ulaşın.
            </p>
            
            <div class="error-actions">
                <a href="index.php" class="btn btn-primary btn-lg me-3">
                    <i class="fas fa-home me-2"></i>Ana Sayfaya Dön
                </a>
                <a href="javascript:history.back()" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Geri Git
                </a>
            </div>
        </div>
    </section>

    <!-- Help Section -->
    <section class="content-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="error-card">
                        <h5><i class="fas fa-link"></i>Popüler Sayfalar</h5>
                        <?php foreach ($popularPages as $page): ?>
                            <a href="<?= escape($page['url']) ?>">
                                <i class="<?= escape($page['icon']) ?>"></i><?= escape($page['title']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="error-card">
                        <h5><i class="fas fa-building"></i>Son Projeler</h5>
                        <?php if (!empty($recentProjects)): ?>
                            <?php foreach ($recentProjects as $project): ?>
                                <a href="proje-detay.php?slug=<?= escape($project['slug']) ?>">
                                    <i class="fas fa-arrow-right"></i><?= escape($project['title']) ?>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">Henüz proje bulunmuyor.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-12">
                    <div class="error-card text-center">
                        <h5><i class="fas fa-search"></i>Aradığınızı Bulamıyor musunuz?</h5>
                        <p class="mb-3">İletişim sayfamızdan bizimle iletişime geçin, size yardımcı olmaktan memnuniyet duyarız.</p>
                        <a href="index.php#contact" class="btn btn-primary">
                            <i class="fas fa-envelope me-2"></i>İletişime Geç
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php
// Footer'ı include et
include 'includes/footer.php';
?>