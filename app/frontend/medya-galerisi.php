<?php
/**
 * BuildTech CMS - Media Gallery
 * Medya galerisi sayfası
 */

define('FRONTEND_ACCESS', true);
require_once '../shared/config/frontend_config.php';

$page_title = 'Medya Galerisi';
$page_description = 'ArteIn inşaat projeleri ve çalışmalarımızdan görüntüler.';

// Medya dosyalarını getir
$mediaFiles = $database->fetchAll("SELECT * FROM media_files ORDER BY created_at DESC");

// Medya türlerine göre ayır
$photos = array_filter($mediaFiles, function($media) {
    return $media['media_type'] === 'photo';
});

$videos = array_filter($mediaFiles, function($media) {
    return $media['media_type'] === 'video';
});

include 'includes/header.php';
?>

<!-- Page Header -->
<section class="page-header" style="background: var(--artein-dark); padding: 80px 0 60px;">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="page-title text-white mb-3" style="font-family: 'Milano Sans', sans-serif; font-size: 2.5rem;">
                    Medya Galerisi
                </h1>
                <p class="page-subtitle text-white-50 mb-0">
                    Projelerimizden ve çalışmalarımızdan seçkin görüntüler
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Media Gallery Section -->
<section class="py-5">
    <div class="container">
        <!-- Filter Buttons -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="filter-buttons text-center">
                    <button class="filter-btn active" data-filter="all">
                        <i class="fas fa-th me-2"></i>Tümü (<?= count($mediaFiles) ?>)
                    </button>
                    <button class="filter-btn" data-filter="photo">
                        <i class="fas fa-image me-2"></i>Fotoğraflar (<?= count($photos) ?>)
                    </button>
                    <button class="filter-btn" data-filter="video">
                        <i class="fas fa-video me-2"></i>Videolar (<?= count($videos) ?>)
                    </button>
                </div>
            </div>
        </div>

        <!-- Media Grid -->
        <div class="media-grid">
            <?php if (empty($mediaFiles)): ?>
                <div class="empty-state text-center py-5">
                    <i class="fas fa-photo-video fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Henüz medya dosyası bulunmuyor</h5>
                    <p class="text-muted">Yakında projelerimizden görüntüler paylaşılacak.</p>
                </div>
            <?php else: ?>
                <div class="row" id="mediaGrid">
                    <?php foreach ($mediaFiles as $media): ?>
                        <div class="col-lg-4 col-md-6 mb-4 media-item" data-media-type="<?= $media['media_type'] ?>" data-media-id="<?= $media['id'] ?>">
                            <div class="media-card">
                                <div class="media-thumbnail" onclick="openMediaViewer(<?= $media['id'] ?>)">
                                    <?php if ($media['media_type'] === 'video'): ?>
                                        <video class="w-100 h-100" style="object-fit: cover;">
                                            <source src="../../<?= escape($media['file_path']) ?>" type="video/<?= $media['file_type'] ?>">
                                        </video>
                                        <div class="play-overlay">
                                            <i class="fas fa-play"></i>
                                        </div>
                                        <div class="media-badge">
                                            <i class="fas fa-video"></i>
                                        </div>
                                    <?php else: ?>
                                        <img src="../../<?= escape($media['file_path']) ?>" 
                                             class="w-100 h-100" style="object-fit: cover;" 
                                             alt="<?= escape($media['alt_text'] ?: $media['original_name']) ?>">
                                        <div class="media-badge">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="media-overlay">
                                        <i class="fas fa-search-plus"></i>
                                    </div>
                                </div>
                                
                                <div class="media-info">
                                    <h6 class="media-title">
                                        <?= escape($media['title'] ?: $media['original_name']) ?>
                                    </h6>
                                    <?php if ($media['description']): ?>
                                        <p class="media-description">
                                            <?= escape(substr($media['description'], 0, 80)) ?>
                                            <?= strlen($media['description']) > 80 ? '...' : '' ?>
                                        </p>
                                    <?php endif; ?>
                                    <div class="media-meta">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            <?= date('d.m.Y', strtotime($media['created_at'])) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Media Viewer Modal -->
<div class="modal fade" id="mediaViewerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white" id="mediaViewerTitle"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 d-flex align-items-center justify-content-center" style="min-height: 80vh;">
                <div id="mediaViewerContent" class="text-center w-100">
                    <!-- Media content will be loaded here -->
                </div>
            </div>
                    <div class="modal-footer border-0">
                        <div id="mediaViewerDescription">
                            <!-- Description will be loaded here -->
                        </div>
                    </div>
        </div>
    </div>
</div>

<style>
/* Filter Buttons */
.filter-buttons {
    margin-bottom: 2rem;
}

.filter-btn {
    background: transparent;
    color: var(--artein-dark);
    border: 2px solid var(--artein-light);
    padding: 12px 24px;
    margin: 0 8px;
    border-radius: 30px;
    transition: all 0.3s ease;
    font-weight: 500;
    cursor: pointer;
}

.filter-btn:hover {
    background: var(--artein-light);
    color: var(--artein-dark);
    border-color: var(--artein-light);
}

.filter-btn.active {
    background: var(--artein-dark);
    color: white;
    border-color: var(--artein-dark);
}

/* Media Cards */
.media-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    height: 100%;
}

.media-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.15);
}

.media-thumbnail {
    position: relative;
    height: 200px;
    overflow: hidden;
    cursor: pointer;
}

.media-thumbnail img,
.media-thumbnail video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.media-card:hover .media-thumbnail img,
.media-card:hover .media-thumbnail video {
    transform: scale(1.05);
}

.media-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background: rgba(0,0,0,0.7);
    color: white;
    padding: 6px 10px;
    border-radius: 20px;
    font-size: 12px;
}

.play-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(0,0,0,0.6);
    color: white;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.media-card:hover .play-overlay {
    opacity: 1;
}

.media-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    color: white;
    font-size: 24px;
}

.media-card:hover .media-overlay {
    opacity: 1;
}

.media-info {
    padding: 20px;
}

.media-title {
    font-family: 'Milano Sans', sans-serif;
    font-size: 16px;
    font-weight: 600;
    color: var(--artein-dark);
    margin-bottom: 8px;
    line-height: 1.4;
}

.media-description {
    color: #666;
    font-size: 14px;
    line-height: 1.5;
    margin-bottom: 12px;
}

.media-meta {
    border-top: 1px solid #f0f0f0;
    padding-top: 12px;
}

/* Modal */
#mediaViewerModal .modal-content {
    border-radius: 16px;
    border: none;
}

#mediaViewerModal .modal-header {
    background: var(--artein-dark);
    border-radius: 16px 16px 0 0;
}

#mediaViewerModal video {
    max-width: 100%;
    max-height: 80vh;
    border-radius: 8px;
}

#mediaViewerModal img {
    max-width: 100%;
    max-height: 80vh;
    object-fit: contain;
    border-radius: 8px;
}

.modal-fullscreen {
    width: 100vw;
    max-width: none;
    height: 100vh;
    margin: 0;
}

        .modal-fullscreen .modal-content {
            height: 100vh;
            border: 0;
            border-radius: 0;
        }
        
        /* Modal açıklama alanı firma renkleri */
        #mediaViewerDescription {
            background: linear-gradient(135deg, var(--artein-dark) 0%, var(--artein-light) 100%);
            color: var(--artein-white);
            padding: 1rem;
            border-radius: 8px;
            margin: 0.5rem;
        }
        
        #mediaViewerDescription p {
            color: var(--artein-white);
            margin-bottom: 0.5rem;
        }
        
        #mediaViewerDescription small {
            color: rgba(255, 255, 255, 0.8);
        }
        
        #mediaViewerDescription i {
            color: var(--artein-light);
        }

/* Responsive */
@media (max-width: 768px) {
    .filter-btn {
        margin: 4px;
        padding: 10px 16px;
        font-size: 14px;
    }
    
    .media-thumbnail {
        height: 180px;
    }
    
    .media-info {
        padding: 16px;
    }
}
</style>

<script>
// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const mediaItems = document.querySelectorAll('.media-item');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter media items
            mediaItems.forEach(item => {
                const mediaType = item.getAttribute('data-media-type');
                
                if (filter === 'all' || mediaType === filter) {
                    item.style.display = 'block';
                    item.style.animation = 'fadeIn 0.3s ease';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
});

// Media viewer functionality
function openMediaViewer(mediaId) {
    // Find media data from the current page data
    const mediaItems = document.querySelectorAll('.media-item');
    let mediaData = null;
    
    mediaItems.forEach(item => {
        if (item.getAttribute('data-media-id') == mediaId) {
            const title = item.querySelector('.media-title').textContent;
            const description = item.querySelector('.media-description')?.textContent || '';
            const date = item.querySelector('.media-meta small').textContent;
            const mediaType = item.getAttribute('data-media-type');
            const img = item.querySelector('img');
            const video = item.querySelector('video source');
            
            // Extract file path from src
            let filePath = '';
            if (img) {
                console.log('Image src:', img.src); // Debug
                // If src contains full URL, extract just the path part
                if (img.src.includes('assets/uploads/')) {
                    filePath = img.src.split('assets/uploads/')[1];
                    filePath = 'assets/uploads/' + filePath;
                } else {
                    filePath = img.src;
                }
                console.log('Extracted path:', filePath); // Debug
            } else if (video) {
                console.log('Video src:', video.src); // Debug
                // If src contains full URL, extract just the path part
                if (video.src.includes('assets/uploads/')) {
                    filePath = video.src.split('assets/uploads/')[1];
                    filePath = 'assets/uploads/' + filePath;
                } else {
                    filePath = video.src;
                }
                console.log('Extracted path:', filePath); // Debug
            }
            
            mediaData = {
                id: mediaId,
                title: title,
                description: description,
                created_at: date,
                media_type: mediaType,
                file_path: filePath,
                file_type: video ? video.type.split('/')[1] : 'jpg',
                alt_text: img ? img.alt : ''
            };
        }
    });
    
    if (mediaData) {
        const modal = new bootstrap.Modal(document.getElementById('mediaViewerModal'));
        
        // Set title
        document.getElementById('mediaViewerTitle').textContent = 
            mediaData.title || 'Medya';
        
        // Set content
        const content = document.getElementById('mediaViewerContent');
        console.log('Media data:', mediaData); // Debug log
        console.log('File path:', mediaData.file_path); // Debug log
        console.log('Full URL:', `../../${mediaData.file_path}`); // Debug log
        
                if (mediaData.media_type === 'video') {
                    content.innerHTML = `
                        <video controls autoplay muted class="w-100 h-100" style="max-height: 80vh; max-width: 100%;">
                            <source src="../../${mediaData.file_path}" type="video/${mediaData.file_type}">
                            Tarayıcınız video oynatmayı desteklemiyor.
                        </video>
                    `;
        } else {
            content.innerHTML = `
                <img src="../../${mediaData.file_path}" class="img-fluid" 
                     alt="${mediaData.alt_text || mediaData.title}"
                     style="max-height: 80vh; max-width: 100%; object-fit: contain;">
            `;
        }
        
        // Set description
        const description = document.getElementById('mediaViewerDescription');
        if (mediaData.description) {
            description.innerHTML = `
                <div class="row">
                    <div class="col-md-8">
                        <p class="mb-0">${mediaData.description}</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <small>
                            <i class="fas fa-calendar me-1"></i>
                            ${mediaData.created_at}
                        </small>
                    </div>
                </div>
            `;
        } else {
            description.innerHTML = `
                <div class="text-end">
                    <small>
                        <i class="fas fa-calendar me-1"></i>
                        ${mediaData.created_at}
                    </small>
                </div>
            `;
        }
        
        modal.show();
    } else {
        alert('Medya verisi bulunamadı.');
    }
}

// Add fadeIn animation
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
`;
document.head.appendChild(style);
</script>

<?php include 'includes/footer.php'; ?>
