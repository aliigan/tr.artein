<?php
/**
 * KVKK Uyumlu Çerez Onay Banner'ı
 */
?>

<!-- Çerez Onay Banner'ı -->
<div id="cookieBanner" class="cookie-banner" style="display: none;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="cookie-content">
                    <h6 class="mb-2">
                        <i class="fas fa-cookie-bite me-2"></i>
                        Çerez Kullanımı
                    </h6>
                    <p class="mb-0 small">
                        Web sitemizde size en iyi deneyimi sunabilmek için çerezler kullanıyoruz. 
                        Zorunlu çerezler site işlevselliği için gereklidir. 
                        <a href="#" data-bs-toggle="modal" data-bs-target="#cookieModal" class="text-decoration-underline">
                            Çerez ayarlarınızı yönetin
                        </a>
                    </p>
                </div>
            </div>
            <div class="col-lg-4 text-end">
                <div class="cookie-buttons">
                    <button type="button" class="btn btn-outline-light btn-sm me-2" id="rejectCookies">
                        <i class="fas fa-times me-1"></i>Reddet
                    </button>
                    <button type="button" class="btn btn-outline-light btn-sm me-2" data-bs-toggle="modal" data-bs-target="#cookieModal">
                        <i class="fas fa-cog me-1"></i>Ayarlar
                    </button>
                    <button type="button" class="btn btn-success btn-sm" id="acceptAllCookies">
                        <i class="fas fa-check me-1"></i>Tümünü Kabul Et
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Çerez Yönetim Modal'ı -->
<div class="modal fade" id="cookieModal" tabindex="-1" aria-labelledby="cookieModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cookieModalLabel">
                    <i class="fas fa-cookie-bite me-2"></i>Çerez Ayarları
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <p class="text-muted">
                        Web sitemizde farklı türde çerezler kullanıyoruz. Aşağıdaki kategorilerden 
                        hangi çerezlerin kullanılmasını istediğinizi seçebilirsiniz.
                    </p>
                </div>

                <!-- Zorunlu Çerezler -->
                <div class="cookie-category mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">
                            <i class="fas fa-shield-alt text-primary me-2"></i>
                            Zorunlu Çerezler
                        </h6>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="necessaryCookies" checked disabled>
                        </div>
                    </div>
                    <p class="small text-muted mb-0">
                        Bu çerezler web sitesinin temel işlevselliği için gereklidir ve devre dışı bırakılamaz. 
                        Site güvenliği, form işlemleri ve temel navigasyon için kullanılır.
                    </p>
                </div>

                <!-- Analitik Çerezler -->
                <div class="cookie-category mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">
                            <i class="fas fa-chart-line text-info me-2"></i>
                            Analitik Çerezler
                        </h6>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="analyticsCookies">
                        </div>
                    </div>
                    <p class="small text-muted mb-0">
                        Web sitesinin nasıl kullanıldığını anlamamıza yardımcı olur. 
                        Anonim istatistikler toplar ve site performansını iyileştirmemize olanak sağlar.
                    </p>
                </div>

                <!-- Pazarlama Çerezleri -->
                <div class="cookie-category mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">
                            <i class="fas fa-bullhorn text-warning me-2"></i>
                            Pazarlama Çerezleri
                        </h6>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="marketingCookies">
                        </div>
                    </div>
                    <p class="small text-muted mb-0">
                        Kişiselleştirilmiş reklamlar ve pazarlama içerikleri sunmak için kullanılır. 
                        Bu çerezler olmadan da siteyi kullanabilirsiniz.
                    </p>
                </div>

                <!-- Sosyal Medya Çerezleri -->
                <div class="cookie-category mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">
                            <i class="fas fa-share-alt text-success me-2"></i>
                            Sosyal Medya Çerezleri
                        </h6>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="socialCookies">
                        </div>
                    </div>
                    <p class="small text-muted mb-0">
                        Sosyal medya platformları ile etkileşim kurmanızı sağlar. 
                        Paylaşım butonları ve sosyal medya entegrasyonları için kullanılır.
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>İptal
                </button>
                <button type="button" class="btn btn-outline-primary" id="saveCookieSettings">
                    <i class="fas fa-save me-1"></i>Ayarları Kaydet
                </button>
                <button type="button" class="btn btn-success" id="acceptAllModal">
                    <i class="fas fa-check me-1"></i>Tümünü Kabul Et
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.cookie-banner {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(135deg, #2c3e50, #34495e);
    color: white;
    padding: 15px 0;
    z-index: 9999;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
    border-top: 3px solid #3498db;
    transform: translateY(100%);
    opacity: 0;
    transition: all 0.3s ease-in-out;
}

.cookie-content h6 {
    color: #ecf0f1;
    font-weight: 600;
}

.cookie-content a {
    color: #3498db;
}

.cookie-content a:hover {
    color: #5dade2;
}

.cookie-buttons .btn {
    border-radius: 20px;
    font-size: 0.85rem;
    padding: 0.4rem 1rem;
}

.cookie-category {
    padding: 15px;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    background-color: #f8f9fa;
}

.cookie-category h6 {
    color: #2c3e50;
    font-weight: 600;
}

.form-check-input:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

@media (max-width: 768px) {
    .cookie-banner .row {
        text-align: center;
    }
    
    .cookie-buttons {
        margin-top: 10px;
    }
    
    .cookie-buttons .btn {
        margin: 2px;
        font-size: 0.8rem;
        padding: 0.3rem 0.8rem;
    }
}
</style>
