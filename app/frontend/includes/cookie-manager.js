/**
 * KVKK Uyumlu Çerez Yönetim Sistemi - Düzeltilmiş Versiyon
 */

class CookieManager {
    constructor() {
        this.cookieName = 'cookieConsent';
        this.cookieExpiry = 365; // Gün cinsinden
        this.init();
    }

    init() {
        // Sayfa yüklendiğinde çerez durumunu kontrol et
        this.checkCookieConsent();
        
        // Event listener'ları ekle
        this.bindEvents();
    }

    bindEvents() {
        // Banner butonları
        document.addEventListener('click', (e) => {
            if (e.target && e.target.id === 'acceptAllCookies') {
                this.acceptAllCookies();
            }
            
            if (e.target && e.target.id === 'rejectCookies') {
                this.rejectAllCookies();
            }
            
            if (e.target && e.target.id === 'saveCookieSettings') {
                this.saveCustomSettings();
            }
            
            if (e.target && e.target.id === 'acceptAllModal') {
                this.acceptAllCookies();
            }
        });
    }

    checkCookieConsent() {
        const consent = this.getCookie(this.cookieName);
        
        if (!consent) {
            // İlk ziyaret - banner'ı göster
            this.showBanner();
        } else {
            // Çerez onayı var - ayarları uygula
            try {
                const settings = JSON.parse(consent);
                this.applySettings(settings);
            } catch (e) {
                console.error('Çerez ayarları okunamadı:', e);
                this.showBanner();
            }
        }
    }

    showBanner() {
        const banner = document.getElementById('cookieBanner');
        if (banner) {
            banner.style.display = 'block';
            // Smooth slide up animasyonu
            setTimeout(() => {
                banner.style.transform = 'translateY(0)';
                banner.style.opacity = '1';
            }, 100);
        }
    }

    hideBanner() {
        const banner = document.getElementById('cookieBanner');
        if (banner) {
            banner.style.transform = 'translateY(100%)';
            banner.style.opacity = '0';
            setTimeout(() => {
                banner.style.display = 'none';
            }, 300);
        }
    }

    acceptAllCookies() {
        console.log('Tüm çerezler kabul ediliyor...');
        
        const settings = {
            necessary: true,
            analytics: true,
            marketing: true,
            social: true,
            timestamp: new Date().toISOString()
        };

        this.setCookie(this.cookieName, JSON.stringify(settings), this.cookieExpiry);
        this.applySettings(settings);
        this.hideBanner();
        this.closeModal();
        
        // Başarı mesajı
        this.showNotification('Tüm çerezler kabul edildi.', 'success');
    }

    rejectAllCookies() {
        console.log('Sadece zorunlu çerezler kabul ediliyor...');
        
        const settings = {
            necessary: true, // Zorunlu çerezler her zaman aktif
            analytics: false,
            marketing: false,
            social: false,
            timestamp: new Date().toISOString()
        };

        this.setCookie(this.cookieName, JSON.stringify(settings), this.cookieExpiry);
        this.applySettings(settings);
        this.hideBanner();
        
        // Bilgilendirme mesajı
        this.showNotification('Sadece zorunlu çerezler kabul edildi.', 'info');
    }

    saveCustomSettings() {
        console.log('Özel çerez ayarları kaydediliyor...');
        
        const settings = {
            necessary: true, // Her zaman true
            analytics: document.getElementById('analyticsCookies')?.checked || false,
            marketing: document.getElementById('marketingCookies')?.checked || false,
            social: document.getElementById('socialCookies')?.checked || false,
            timestamp: new Date().toISOString()
        };

        this.setCookie(this.cookieName, JSON.stringify(settings), this.cookieExpiry);
        this.applySettings(settings);
        this.hideBanner();
        this.closeModal();
        
        this.showNotification('Çerez ayarları kaydedildi.', 'success');
    }

    applySettings(settings) {
        console.log('Çerez ayarları uygulanıyor:', settings);
        
        // Google Analytics
        if (settings.analytics) {
            this.loadGoogleAnalytics();
        } else {
            this.disableGoogleAnalytics();
        }

        // Google reCAPTCHA (her zaman yükle - güvenlik için)
        if (settings.necessary) {
            this.loadGoogleRecaptcha();
        }

        // Diğer üçüncü parti servisler
        if (settings.marketing) {
            this.loadMarketingScripts();
        }

        if (settings.social) {
            this.loadSocialScripts();
        }
    }

    loadGoogleAnalytics() {
        // Google Analytics yükleme kodu buraya eklenebilir
        console.log('Google Analytics yüklendi');
    }

    disableGoogleAnalytics() {
        // Google Analytics'i devre dışı bırak
        console.log('Google Analytics devre dışı bırakıldı');
    }

    loadGoogleRecaptcha() {
        // Google reCAPTCHA zaten header'da yükleniyor
        console.log('Google reCAPTCHA aktif');
    }

    loadMarketingScripts() {
        // Pazarlama scriptleri buraya eklenebilir
        console.log('Pazarlama scriptleri yüklendi');
    }

    loadSocialScripts() {
        // Sosyal medya scriptleri buraya eklenebilir
        console.log('Sosyal medya scriptleri yüklendi');
    }

    closeModal() {
        const modal = document.getElementById('cookieModal');
        if (modal) {
            const modalInstance = bootstrap.Modal.getInstance(modal);
            if (modalInstance) {
                modalInstance.hide();
            } else {
                // Modal henüz açılmamışsa, Bootstrap 5 ile kapat
                const bsModal = new bootstrap.Modal(modal);
                bsModal.hide();
            }
        }
    }

    showNotification(message, type = 'info') {
        // Basit alert ile bildirim göster (Bootstrap toast yerine)
        const alertClass = type === 'success' ? 'alert-success' : 
                          type === 'danger' ? 'alert-danger' : 'alert-info';
        
        // Mevcut alert'leri temizle
        const existingAlerts = document.querySelectorAll('.cookie-notification');
        existingAlerts.forEach(alert => alert.remove());
        
        // Yeni alert oluştur
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert ${alertClass} cookie-notification position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 10000; min-width: 300px;';
        alertDiv.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        // 3 saniye sonra otomatik kaldır
        setTimeout(() => {
            if (alertDiv.parentElement) {
                alertDiv.remove();
            }
        }, 3000);
    }

    // Çerez işlemleri
    setCookie(name, value, days) {
        const expires = new Date();
        expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/;SameSite=Lax`;
        console.log('Çerez kaydedildi:', name, value);
    }

    getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    deleteCookie(name) {
        document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;`;
        console.log('Çerez silindi:', name);
    }

    // Çerez ayarlarını sıfırla
    resetCookieSettings() {
        this.deleteCookie(this.cookieName);
        location.reload();
    }
}

// Sayfa yüklendiğinde çerez yöneticisini başlat
document.addEventListener('DOMContentLoaded', function() {
    console.log('Çerez yöneticisi başlatılıyor...');
    window.cookieManager = new CookieManager();
});

// Global fonksiyonlar (diğer scriptlerden erişim için)
window.resetCookieSettings = function() {
    if (window.cookieManager) {
        window.cookieManager.resetCookieSettings();
    }
};

window.showCookieSettings = function() {
    const modal = new bootstrap.Modal(document.getElementById('cookieModal'));
    modal.show();
};

// Debug için global erişim
window.cookieManager = null;