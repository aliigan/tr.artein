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
            background: var(--artein-dark);
            border: 2px solid #fff;
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
            background: var(--artein-dark);
            border: 2px solid #fff;
        }
        .social-link.whatsapp {
            background: var(--artein-dark);
            border: 2px solid #fff;
        }
        .social-link.instagram {
            background: var(--artein-dark);
            border: 2px solid #fff;
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
            background: #fff;
            color: var(--artein-dark);
        }
        .social-link:hover::before {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .social-icon {
            width: 24px !important;
            height: 24px !important;
            color: white !important;
            transition: all 0.3s ease !important;
            display: block !important;
        }
        
        .social-link:hover .social-icon {
            color: var(--artein-dark) !important;
        }
        
        /* Debug: Sosyal medya ikonları görünürlük */
        .social-links {
            display: flex !important;
            gap: 1rem !important;
            margin-top: 1.5rem !important;
            background: rgba(255,0,0,0.1) !important; /* Debug: Kırmızı arka plan */
        }
        
        .social-link {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 50px !important;
            height: 50px !important;
            border-radius: 50% !important;
            background: var(--artein-dark) !important;
            border: 2px solid #fff !important;
            text-decoration: none !important;
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
            }
            
            .social-link {
                width: 40px !important;
                height: 40px !important;
                font-size: 1rem !important;
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
                            <h3 class="text-white"><?= escape(getSetting('company_name', 'Arte In')) ?></h3>
                        <?php endif; ?>
                    </div>
                    <p><?= escape(getSetting('site_description', 'Modern İnşaat Çözümleri')) ?></p>
                    
                    <div class="social-links">
                        <a href="<?= escape(getSetting('social_facebook', getSetting('facebook_url', 'https://www.facebook.com/artein'))) ?>" target="_blank" title="Facebook" class="social-link facebook">
                            <svg class="social-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        
                        <a href="<?= escape(getSetting('social_instagram', getSetting('instagram_url', 'https://www.instagram.com/artein'))) ?>" target="_blank" title="Instagram" class="social-link instagram">
                            <svg class="social-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </a>
                        
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', getSetting('whatsapp_number', getSetting('company_phone', '+90 555 888 99 88'))) ?>" target="_blank" title="WhatsApp" class="social-link whatsapp">
                            <svg class="social-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                            </svg>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-6">
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
