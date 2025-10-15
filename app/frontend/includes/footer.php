    </main>
    <!-- End Main Content Wrapper -->
    
    <style>
        /* Index.php footer styles */
        .footer {
            background-color: var(--artein-dark) !important;
            padding: 3rem 0;
        }
        .footer-logo {
            margin-bottom: 1.5rem;
        }
        .footer-logo img {
            height: 60px;
        }
        .footer-contact {
            margin-bottom: 2rem;
        }
        .footer-contact p {
            margin-bottom: 0.5rem;
            color: var(--artein-white);
        }
        .footer-contact i {
            margin-right: 0.5rem;
            color: var(--artein-light);
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
            font-family: "Font Awesome 6 Free", "Font Awesome 6 Pro", "Font Awesome 6 Brands" !important;
            font-weight: 900 !important;
            font-style: normal !important;
            font-variant: normal !important;
            text-rendering: auto !important;
            -webkit-font-smoothing: antialiased !important;
            -moz-osx-font-smoothing: grayscale !important;
            display: inline-block !important;
            line-height: 1 !important;
        }
        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        .social-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            color: var(--artein-white);
            font-size: 1.2rem;
            text-decoration: none;
            transition: all 0.3s ease;
            background: transparent !important;
            border: 1px solid var(--artein-white) !important;
            opacity: 0.3;
            box-shadow: none !important;
        }
        .social-link:hover {
            transform: translateY(-2px);
            background: transparent !important;
            color: var(--artein-white);
            opacity: 1;
        }
        
        /* Sosyal medya ikonları görünürlük */
        .social-links {
            display: flex !important;
            gap: 1rem !important;
            margin-top: 1.5rem !important;
        }
        
        .social-link {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 50px !important;
            height: 50px !important;
            border-radius: 50% !important;
            background: var(--artein-dark) !important;
            border: 2px solid var(--artein-white) !important;
            text-decoration: none !important;
            color: var(--artein-white) !important;
        }
        
        
        /* Mobile Responsive Footer */
        @media (max-width: 768px) {
            .footer {
                padding: 2rem 0 !important;
                text-align: center;
            }
            
            .footer-logo {
                margin-bottom: 1rem !important;
            }
            
            .footer-logo img {
                height: 50px !important;
            }
            
            .footer-contact {
                margin-bottom: 1.5rem !important;
            }
            
            .footer-contact p {
                margin-bottom: 0.8rem !important;
                font-size: 0.95rem !important;
            }
            
            .footer-contact i {
                margin-right: 0.8rem !important;
                font-size: 1.2rem !important;
                width: 25px !important;
            }
            
            .social-links {
                justify-content: center !important;
                gap: 0.8rem !important;
                margin: 1.5rem 0 !important;
            }
            
            .social-link {
                width: 45px !important;
                height: 45px !important;
                font-size: 1.1rem !important;
            }
            
            .col-lg-6 {
                margin-bottom: 2rem;
            }
            
            .col-lg-6:last-child {
                margin-bottom: 1rem;
            }
            
            h5 {
                font-size: 1.1rem !important;
                margin-bottom: 1rem !important;
            }
            
            .list-unstyled li {
                margin-bottom: 0.5rem;
            }
            
            .list-unstyled a {
                font-size: 0.95rem !important;
                padding: 0.3rem 0 !important;
                display: inline-block !important;
            }
            
            hr {
                margin: 1.5rem 0 !important;
            }
            
            .col-md-6 p {
                font-size: 0.9rem !important;
                margin-bottom: 0.5rem !important;
            }
        }
        
        @media (max-width: 576px) {
            .footer {
                padding: 1.5rem 0 !important;
            }
            
            .footer-logo img {
                height: 45px !important;
            }
            
            .footer-contact p {
                font-size: 0.9rem !important;
            }
            
            .footer-contact i {
                font-size: 1.1rem !important;
                width: 22px !important;
                margin-right: 0.6rem !important;
            }
            
            .social-links {
                gap: 0.6rem !important;
                margin: 1rem 0 !important;
                justify-content: center !important;
            }
            
            .footer-logo {
                text-align: center !important;
            }
            
            .footer-contact {
                text-align: center !important;
            }
            
            h5 {
                text-align: center !important;
            }
            
            .social-link {
                width: 35px !important;
                height: 35px !important;
                font-size: 0.9rem !important;
            }
            
            .col-lg-6 {
                margin-bottom: 1.5rem;
            }
            
            h5 {
                font-size: 1rem !important;
            }
            
            .list-unstyled a {
                font-size: 0.9rem !important;
            }
            
            .col-md-6 p {
                font-size: 0.85rem !important;
            }
            
            .col-md-6 small {
                font-size: 0.8rem !important;
            }
        }
    </style>

    <!-- Footer -->
    <footer class="footer text-white">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="footer-logo">
                        <?php 
                        $logoPath = getSetting('logo');
                        if ($logoPath && file_exists($logoPath)): ?>
                            <img src="<?= escape($logoPath) ?>" alt="<?= escape(getSetting('company_name', 'Arte In')) ?>">
                        <?php elseif (file_exists('../../assets/images/ArteIn_logos-01.png')): ?>
                            <img src="../../assets/images/ArteIn_logos-01.png" alt="Arte In">
                        <?php else: ?>
                            <h3 style="color: var(--artein-white);"><?= escape(getSetting('company_name', 'Arte In')) ?></h3>
                        <?php endif; ?>
                    </div>
                    <p style="color: var(--artein-white); opacity: 0.8;"><?= escape(getSetting('site_description', 'Modern İnşaat Çözümleri')) ?></p>
                </div>
                
                <div class="col-lg-6">
                    <h5 class="mb-3 text-lg-end" style="color: var(--artein-white);">İletişim Bilgileri</h5>
                    <div class="footer-contact text-lg-end">
                        <p>
                            <i class="fas fa-map-marker-alt"></i>
                            <a href="https://www.google.com/maps/search/<?= urlencode(getSetting('company_address', 'İstanbul, Türkiye')) ?>" 
                               target="_blank" 
                               style="color: var(--artein-white); text-decoration: none;">
                                <?= escape(getSetting('company_address', 'İstanbul, Türkiye')) ?>
                            </a>
                        </p>
                        <p>
                            <i class="fas fa-phone"></i>
                            <a href="tel:<?= getSetting('company_phone', '+90 555 888 99 88') ?>" 
                               style="color: var(--artein-white); text-decoration: none;">
                                <?= escape(getSetting('company_phone', '+90 555 888 99 88')) ?>
                            </a>
                        </p>
                        <p>
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:<?= getSetting('company_email', 'info@artein.com') ?>" 
                               style="color: var(--artein-white); text-decoration: none;">
                                <?= escape(getSetting('company_email', 'info@artein.com')) ?>
                            </a>
                        </p>
                    </div>
                    
                    <div class="social-links justify-content-lg-end">
                        <a href="<?= escape(getSetting('social_facebook', getSetting('facebook_url', 'https://www.facebook.com/artein'))) ?>" target="_blank" title="Facebook" class="social-link facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        
                        <a href="<?= escape(getSetting('social_instagram', getSetting('instagram_url', 'https://www.instagram.com/artein'))) ?>" target="_blank" title="Instagram" class="social-link instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', getSetting('whatsapp_number', getSetting('company_phone', '+90 555 888 99 88'))) ?>" target="_blank" title="WhatsApp" class="social-link whatsapp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
                
            </div>
            
            <hr class="my-4" style="border-color: var(--artein-white); opacity: 0.2;">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-center text-md-start" style="color: var(--artein-white);">
                        &copy; <?= date('Y') ?> 
                        <strong><?= escape(getSetting('company_name', 'Arte In Construction')) ?></strong>. 
                        Tüm hakları saklıdır.
                    </p>
                </div>
                <div class="col-md-6">
                    <p class="mb-0 text-center text-md-end" style="color: var(--artein-white); opacity: 0.7;">
                        <small>
                            <a href="#" onclick="showCookieSettings(); return false;" 
                               class="text-decoration-underline" 
                               style="color: var(--artein-white); opacity: 0.8;">
                                <i class="fas fa-cookie-bite me-1"></i>Çerez Ayarları
                            </a>
                        </small>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- contact-form.js disabled - using custom form handling in index.php -->
    <!-- <script src="../../assets/js/contact-form.js"></script> -->
    
    <?php if (!empty($_SESSION['contact_success'])): ?>
    <!-- Contact success toast -->
    <div class="toast align-items-center border-0 position-fixed bottom-0 end-0 m-3" id="contactToast" role="alert" aria-live="assertive" aria-atomic="true" 
         style="z-index: 9999; background-color: var(--artein-dark); color: var(--artein-white);">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-check-circle me-2"></i><?= escape($_SESSION['contact_success']) ?>
            </div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close" 
                    style="filter: invert(1);"></button>
        </div>
    </div>
    <?php unset($_SESSION['contact_success']); ?>
    <?php endif; ?>
    
    <!-- Page specific JavaScript -->
    <?php if (isset($pageSpecificJS)): ?>
    <?= $pageSpecificJS ?>
    <?php endif; ?>
    
    <script>
        // Index.php style Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.background = 'rgba(17, 55, 54, 0.95)';
                navbar.style.backdropFilter = 'blur(20px)';
                navbar.style.boxShadow = '0 2px 30px rgba(17, 55, 54, 0.4)';
            } else {
                navbar.style.background = 'var(--artein-dark)';
                navbar.style.backdropFilter = 'blur(10px)';
                navbar.style.boxShadow = '0 2px 20px rgba(17, 55, 54, 0.3)';
            }
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Auto-collapse mobile navbar when a menu item is clicked
        (function() {
            const navbarCollapseEl = document.getElementById('navbarNav');
            if (!navbarCollapseEl) return;
            const clickableSelectors = '.navbar-collapse .nav-link, .navbar-collapse .dropdown-item';
            document.querySelectorAll(clickableSelectors).forEach(function(el) {
                el.addEventListener('click', function(e) {
                    // Dropdown başlığında menüyü kapatma (sadece alt öğeler açılmalı)
                    if (this.classList.contains('dropdown-toggle') || this.getAttribute('data-bs-toggle') === 'dropdown') {
                        return;
                    }
                    const isShown = navbarCollapseEl.classList.contains('show');
                    if (isShown && typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
                        const instance = bootstrap.Collapse.getInstance(navbarCollapseEl) || new bootstrap.Collapse(navbarCollapseEl, { toggle: false });
                        instance.hide();
                    }
                });
            });
        })();
    </script>
    <script>
        // Lightweight polling for content updates (every 15s)
        (function() {
            let lastVersion = 0;
            const endpoint = '../../app/shared/updates.php';
            function showRefreshToast() {
                const existing = document.getElementById('contentUpdateToast');
                if (existing) return;
                const toastHtml = '<div class="toast align-items-center border-0 position-fixed bottom-0 end-0 m-3" id="contentUpdateToast" role="alert" aria-live="assertive" aria-atomic="true" style="z-index: 9999; background-color: var(--artein-dark); color: var(--artein-white);">' +
                    '<div class="d-flex">' +
                    '<div class="toast-body">' +
                    '<i class="fas fa-sync-alt me-2"></i>Yeni içerik mevcut. Görüntüyü güncellemek ister misiniz?' +
                    '</div>' +
                    '<div class="d-flex align-items-center me-2">' +
                    '<button type="button" class="btn btn-sm btn-light me-2" id="refreshNowBtn">Güncelle</button>' +
                    '<button type="button" class="btn-close btn-close-white m-auto" data-bs-dismiss="toast" aria-label="Close"></button>' +
                    '</div>' +
                    '</div>' +
                    '</div>';
                document.body.insertAdjacentHTML('beforeend', toastHtml);
                const toastEl = document.getElementById('contentUpdateToast');
                const toast = new bootstrap.Toast(toastEl, { autohide: false });
                toast.show();
                document.getElementById('refreshNowBtn').addEventListener('click', function() { window.location.reload(); });
            }
            function poll() {
                fetch(endpoint, { cache: 'no-store' })
                    .then(r => r.ok ? r.json() : null)
                    .then(data => {
                        if (!data || !data.success) return;
                        if (lastVersion === 0) { lastVersion = data.version; return; }
                        if (data.version > lastVersion) { showRefreshToast(); lastVersion = data.version; }
                    })
                    .catch(() => {});
            }
            setInterval(poll, 15000);
            poll();
        })();
    </script>
    
    <!-- Çerez Banner'ı -->
    <?php include 'cookie-banner.php'; ?>
</body>
</html>
