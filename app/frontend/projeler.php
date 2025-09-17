<?php
/**
 * BuildTech CMS - Projeler Sayfası
 */

define('FRONTEND_ACCESS', true);
require_once '../shared/config/frontend_config.php';

// Sayfalama ayarları
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 9;
$offset = ($page - 1) * $limit;

// Filtreleme
$category = $_GET['category'] ?? '';
$whereClause = "WHERE is_active = 1";
$params = [];

if ($category) {
    $whereClause .= " AND category = ?";
    $params[] = $category;
}

// Toplam proje sayısı
$totalProjects = $database->fetchOne("SELECT COUNT(*) as count FROM projects $whereClause", $params)['count'];
$totalPages = ceil($totalProjects / $limit);

// Projeleri getir
$projects = $database->fetchAll("SELECT * FROM projects $whereClause ORDER BY order_index ASC, created_at DESC LIMIT $limit OFFSET $offset", $params);

// Kategorileri getir
$categories = $database->fetchAll("SELECT DISTINCT category FROM projects WHERE is_active = 1 ORDER BY category");

// Sayfa bilgileri
$pageTitle = 'Projeler - Arte In';
$pageDescription = 'Arte In tarafından gerçekleştirilen inşaat ve mühendislik projelerini keşfedin.';

// Site ayarlarını al
$settings = getSiteSettings();

// Sayfa özel stilleri
$pageSpecificStyles = '
        /* Page Header - Full Page */
        .page-header {
            background: linear-gradient(135deg, rgba(17, 55, 54, 0.85), rgba(17, 55, 54, 0.75)), url("../../assets/uploads/projects/project1.jpg");
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


        /* Content Section */
        .content-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        /* Statistics Section */
        .stats-section {
            background: linear-gradient(135deg, #113736 0%, #1e5f5d 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 60px;
        }

        .stat-item {
            text-align: center;
            animation: fadeInUp 1s ease-out;
        }

        .stat-number {
            font-family: var(--font-display), sans-serif;
            font-size: 3rem;
            font-weight: 700;
            color: #dfeade;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
            font-weight: 600;
        }

        /* Filter Section */
        .filter-section {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 3rem;
            animation: slideInLeft 1s ease-out;
        }

        .filter-title {
            font-family: var(--font-display), sans-serif;
            font-size: 1.8rem;
            color: #113736;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .filter-buttons {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1rem;
        }

        .filter-btn {
            background: white;
            border: 2px solid #113736;
            color: #113736;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 600;
            position: relative;
            overflow: hidden;
        }

        .filter-btn::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #113736, #1e5f5d);
            transition: left 0.4s ease;
            z-index: -1;
        }

        .filter-btn:hover::before,
        .filter-btn.active::before {
            left: 0;
        }

        .filter-btn:hover,
        .filter-btn.active {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(17, 55, 54, 0.3);
        }

        /* Project Cards - Projeler sayfasına özel */
        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
            align-items: stretch;
        }

        .project-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(17, 55, 54, 0.1);
            position: relative;
            animation: fadeInUp 1s ease-out;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .project-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .project-image {
            height: 280px;
            background-size: cover;
            background-position: center;
            position: relative;
            overflow: hidden;
        }

        .project-image::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(17, 55, 54, 0.1), rgba(30, 95, 93, 0.1));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .project-card:hover .project-image::before {
            opacity: 1;
        }

        .project-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(17, 55, 54, 0.9), rgba(30, 95, 93, 0.8));
            opacity: 0;
            transition: all 0.4s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(5px);
        }

        .project-card:hover .project-overlay {
            opacity: 1;
        }

        .overlay-content {
            text-align: center;
            color: white;
            transform: translateY(20px);
            transition: transform 0.4s ease;
        }

        .project-card:hover .overlay-content {
            transform: translateY(0);
        }

        .project-info {
            padding: 2rem;
            position: relative;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .project-category-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(135deg, #113736, #1e5f5d);
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
            box-shadow: 0 3px 10px rgba(17, 55, 54, 0.4);
            z-index: 10;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .project-title {
            font-family: var(--font-display), sans-serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: #113736;
            margin-bottom: 0.75rem;
            line-height: 1.3;
        }

        .project-description {
            color: #666;
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .project-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid #e9ecef;
            margin-top: auto;
        }

        .project-date {
            color: #888;
            font-size: 0.9rem;
        }

        .view-btn {
            background: linear-gradient(135deg, #113736, #1e5f5d);
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .view-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(17, 55, 54, 0.3);
            color: white;
        }

        /* Pagination */
        .pagination {
            justify-content: center;
            margin-top: 4rem;
        }

        .page-link {
            color: #113736;
            border: 2px solid #113736;
            margin: 0 0.25rem;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .page-link:hover {
            background: linear-gradient(135deg, #113736, #1e5f5d);
            border-color: #113736;
            color: white;
            transform: translateY(-2px);
        }

        .page-item.active .page-link {
            background: linear-gradient(135deg, #113736, #1e5f5d);
            border-color: #113736;
            color: white;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .empty-state i {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            color: #666;
            margin-bottom: 1rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {            
            .page-title {
                font-size: 2.8rem;
                margin-bottom: 1rem;
                text-shadow: 2px 2px 8px rgba(0,0,0,0.8);
            }
            
            .page-subtitle {
                font-size: 1.1rem;
                padding: 0 1rem;
                max-width: 90%;
            }
            
            .breadcrumb-section {
                padding: 12px 0;
            }
            
            .breadcrumb {
                font-size: 0.85rem;
            }
            
            .projects-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .project-card {
                min-height: 400px;
            }
            
            .project-image {
                height: 250px;
            }
            
            .filter-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .filter-btn {
                width: 200px;
            }
            
            .project-category-badge {
                top: 10px;
                right: 10px;
                padding: 0.3rem 0.6rem;
                font-size: 0.7rem;
            }
        }
';

// Sayfa özel JavaScript
$pageSpecificJS = '
    <script>
        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: "0px 0px -50px 0px"
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = "1";
                    entry.target.style.transform = "translateY(0)";
                }
            });
        }, observerOptions);

        // Observe project cards
        document.addEventListener("DOMContentLoaded", function() {
            const projectCards = document.querySelectorAll(".project-card");
            projectCards.forEach(card => {
                card.style.opacity = "0";
                card.style.transform = "translateY(30px)";
                card.style.transition = "opacity 0.6s ease, transform 0.6s ease";
                observer.observe(card);
            });

            // Smooth scroll for filter buttons
            const filterButtons = document.querySelectorAll(".filter-btn");
            filterButtons.forEach(btn => {
                btn.addEventListener("click", function(e) {
                    // Add loading effect
                    this.style.transform = "scale(0.95)";
                    setTimeout(() => {
                        this.style.transform = "scale(1)";
                    }, 150);
                });
            });

            // Add hover effect to project cards
            const projectCards = document.querySelectorAll(".project-card");
            projectCards.forEach(card => {
                card.addEventListener("mouseenter", function() {
                    this.style.transform = "translateY(-15px) scale(1.02)";
                });
                
                card.addEventListener("mouseleave", function() {
                    this.style.transform = "translateY(0) scale(1)";
                });
            });

            // Counter animation for statistics
            const statNumbers = document.querySelectorAll(".stat-number");
            const animateCounter = (element, target) => {
                let current = 0;
                const increment = target / 50;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    element.textContent = Math.floor(current);
                }, 30);
            };

            const statsObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const target = parseInt(entry.target.textContent);
                        if (!isNaN(target)) {
                            animateCounter(entry.target, target);
                        }
                        statsObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });

            statNumbers.forEach(stat => {
                statsObserver.observe(stat);
            });
        });

        // Add loading state for pagination
        document.querySelectorAll(".page-link").forEach(link => {
            link.addEventListener("click", function(e) {
                // Add loading effect
                this.innerHTML = "<i class=\"fas fa-spinner fa-spin me-1\"></i>Yükleniyor...";
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
            <h1 class="page-title">Projelerimiz</h1>
            <p class="page-subtitle">Gerçekleştirdiğimiz inşaat ve mühendislik projelerini keşfedin. Her proje, kalite ve estetiğin mükemmel uyumunu yansıtır.</p>
        </div>
    </section>

    <!-- Breadcrumb 
    <section class="breadcrumb-section">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Ana Sayfa</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Projeler</li>
                </ol>
            </nav>
        </div>
    </section>-->

    <!-- Statistics Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="stat-item">
                        <div class="stat-number"><?= $totalProjects ?></div>
                        <div class="stat-label">Toplam Proje</div>
                    </div>
                </div>
     
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="stat-item">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Müşteri Memnuniyeti</div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="stat-item">
                        <div class="stat-number">15+</div>
                        <div class="stat-label">Yıllık Deneyim</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="content-section">
        <div class="container">
          
            <!-- Projects Grid -->
            <?php if (!empty($projects)): ?>
                <div class="projects-grid">
                    <?php foreach ($projects as $index => $project): ?>
                        <div class="project-card" style="animation-delay: <?= $index * 0.1 ?>s">
                            <div class="project-image" style="background-image: url('../../<?= escape($project['featured_image']) ?>')">
                                <div class="project-category-badge"><?= escape($project['category']) ?></div>
                                <div class="project-overlay">
                                    <div class="overlay-content">
                                        <h4 class="text-white mb-3"><?= escape($project['title']) ?></h4>
                                        <a href="proje-detay.php?slug=<?= escape($project['slug']) ?>" class="view-btn">
                                            <i class="fas fa-eye me-2"></i>Detayları Gör
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="project-info">
                                <h3 class="project-title"><?= escape($project['title']) ?></h3>
                                <p class="project-description"><?= escape(substr($project['description'], 0, 150)) ?>...</p>
                                <div class="project-meta">
                                    <span class="project-date">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        <?= date('d.m.Y', strtotime($project['created_at'])) ?>
                                    </span>
                                    <a href="proje-detay.php?slug=<?= escape($project['slug']) ?>" class="view-btn">
                                        <i class="fas fa-arrow-right me-1"></i>Detay
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Proje sayfalama">
                        <ul class="pagination">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="projeler.php?page=<?= $page - 1 ?><?= $category ? '&category=' . urlencode($category) : '' ?>">
                                        <i class="fas fa-chevron-left me-1"></i>Önceki
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php 
                            $startPage = max(1, $page - 2);
                            $endPage = min($totalPages, $page + 2);
                            
                            if ($startPage > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="projeler.php?page=1<?= $category ? '&category=' . urlencode($category) : '' ?>">1</a>
                                </li>
                                <?php if ($startPage > 2): ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                    <a class="page-link" href="projeler.php?page=<?= $i ?><?= $category ? '&category=' . urlencode($category) : '' ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($endPage < $totalPages): ?>
                                <?php if ($endPage < $totalPages - 1): ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                <?php endif; ?>
                                <li class="page-item">
                                    <a class="page-link" href="projeler.php?page=<?= $totalPages ?><?= $category ? '&category=' . urlencode($category) : '' ?>"><?= $totalPages ?></a>
                                </li>
                            <?php endif; ?>

                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="projeler.php?page=<?= $page + 1 ?><?= $category ? '&category=' . urlencode($category) : '' ?>">
                                        Sonraki<i class="fas fa-chevron-right ms-1"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <h3>Henüz proje bulunmuyor</h3>
                    <p class="text-muted">Seçtiğiniz kategoride henüz proje bulunmuyor. Tüm projeleri görmek için "Tümü" butonuna tıklayın.</p>
                    <a href="projeler.php" class="btn btn-primary mt-3">
                        <i class="fas fa-th-large me-2"></i>Tüm Projeleri Gör
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>

<?php
// Footer'ı include et
include 'includes/footer.php';
?>
?>