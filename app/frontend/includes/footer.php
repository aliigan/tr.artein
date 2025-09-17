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
            color: #fff;
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
            width: 50px;
            height: 50px;
            border-radius: 50%;
            color: #fff;
            font-size: 1.2rem;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .social-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        .social-link.facebook {
            background: linear-gradient(135deg, var(--artein-dark), #1877f2);
        }
        .social-link.whatsapp {
            background: linear-gradient(135deg, var(--artein-dark), #25d366);
        }
        .social-link.instagram {
            background: linear-gradient(135deg, var(--artein-dark), #e4405f);
        }
        .social-link.linkedin {
            background: linear-gradient(135deg, var(--artein-dark), #0077b5);
        }
        .social-link.youtube {
            background: linear-gradient(135deg, var(--artein-dark), #ff0000);
        }
        .social-link:hover {
            transform: translateY(-3px) scale(1.1);
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }
        .social-link:hover::before {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .social-link i {
            font-family: "Font Awesome 6 Free", "Font Awesome 6 Pro", "Font Awesome 6 Brands" !important;
            font-weight: 400 !important;
            font-style: normal !important;
            font-variant: normal !important;
            text-rendering: auto !important;
            -webkit-font-smoothing: antialiased !important;
            -moz-osx-font-smoothing: grayscale !important;
            display: inline-block !important;
            line-height: 1 !important;
        }
        
        /* Hızlı bağlantılar: hover'da altı çizili */
        .list-unstyled a {
            text-decoration: none !important;
        }
        .list-unstyled a:hover {
            text-decoration: underline !important;
            text-underline-offset: 3px;
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
            
            .col-lg-4 {
                margin-bottom: 2rem;
            }
            
            .col-lg-4:last-child {
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
            }
            
            .social-link {
                width: 40px !important;
                height: 40px !important;
                font-size: 1rem !important;
            }
            
            .col-lg-4 {
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
                <div class="col-lg-4">
                    <div class="footer-logo">
                        <?php 
                        $logoPath = getSetting('logo');
                        if ($logoPath && file_exists($logoPath)): ?>
                            <img src="<?= escape($logoPath) ?>" alt="<?= escape(getSetting('company_name', 'Arte In')) ?>">
                        <?php elseif (file_exists('../../assets/images/ArteIn_logos-01.png')): ?>
                            <img src="../../assets/images/ArteIn_logos-01.png" alt="Arte In">
                        <?php else: ?>
                            <h3 class="text-white"><?= escape(getSetting('company_name', 'Arte In')) ?></h3>
                        <?php endif; ?>
                    </div>
                    <p><?= escape(getSetting('site_description', 'Modern İnşaat Çözümleri')) ?></p>
                    
                    <div class="social-links">
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
                
                <div class="col-lg-4">
                    <h5 class="mb-3">İletişim Bilgileri</h5>
                    <div class="footer-contact">
                        <p>
                            <i class="fas fa-map-marker-alt"></i>
                            <a href="https://www.google.com/maps/search/<?= urlencode(getSetting('company_address', 'İstanbul, Türkiye')) ?>" 
                               target="_blank" 
                               class="text-white text-decoration-none">
                                <?= escape(getSetting('company_address', 'İstanbul, Türkiye')) ?>
                            </a>
                        </p>
                        <p>
                            <i class="fas fa-phone"></i>
                            <a href="tel:<?= getSetting('company_phone', '+90 555 888 99 88') ?>" 
                               class="text-white text-decoration-none">
                                <?= escape(getSetting('company_phone', '+90 555 888 99 88')) ?>
                            </a>
                        </p>
                        <p>
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:<?= getSetting('company_email', 'info@artein.com') ?>" 
                               class="text-white text-decoration-none">
                                <?= escape(getSetting('company_email', 'info@artein.com')) ?>
                            </a>
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <h5 class="mb-3">Hızlı Bağlantılar</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-white text-decoration-none">Ana Sayfa</a></li>
                        <li><a href="hakkimizda.php" class="text-white text-decoration-none">Hakkımızda</a></li>
                        <li><a href="projeler.php" class="text-white text-decoration-none">Projeler</a></li>
                        <li><a href="index.php#contact" class="text-white text-decoration-none">İletişim</a></li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4" style="border-color: rgba(255,255,255,0.2);">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-center text-md-start">
                        &copy; <?= date('Y') ?> 
                        <strong><?= escape(getSetting('company_name', 'Arte In Construction')) ?></strong>. 
                        Tüm hakları saklıdır.
                    </p>
                </div>
                <div class="col-md-6">
                    <p class="mb-0 text-center text-md-end text-muted">
                        <small>
                            Modern İnşaat Çözümleri | Kalite ve Güvenin Adresi
                            <br>
                            <a href="#" onclick="showCookieSettings(); return false;" class="text-decoration-underline">
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
    <script src="../../assets/js/contact-form.js"></script>
    
    <?php if (!empty($_SESSION['contact_success'])): ?>
    <!-- Contact success toast -->
    <div class="toast align-items-center text-bg-success border-0 position-fixed bottom-0 end-0 m-3" id="contactToast" role="alert" aria-live="assertive" aria-atomic="true" style="z-index: 9999;">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-check-circle me-2"></i><?= escape($_SESSION['contact_success']) ?>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
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
                navbar.style.background = 'linear-gradient(135deg, #113736 0%, #1e5f5d 100%)';
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
    </script>
    
    <!-- Çerez Banner'ı -->
    <?php include 'cookie-banner.php'; ?>
</body>
</html>
