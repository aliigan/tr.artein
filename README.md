# ArteIn CMS - Modern PHP CMS

## 🚀 Hızlı Başlangıç

### Gereksinimler
- **PHP**: 7.4 veya üzeri
- **MySQL**: 5.7 veya üzeri  
- **Web Server**: Apache/Nginx
- **Geliştirme Ortamı**: XAMPP/WAMP/MAMP

### Kurulum Adımları

#### 1. Web Server Kurulumu
1. XAMPP'ı indirin: https://www.apachefriends.org/
2. Apache ve MySQL servislerini başlatın
3. Proje klasörünü `htdocs` dizinine yerleştirin

#### 2. Veritabanı Kurulumu
1. phpMyAdmin'i açın: http://localhost/phpmyadmin
2. Yeni veritabanı oluşturun: `buildtech_cms`
3. SQL dosyasını import edin: `app/shared/database/buildtech_cms.sql`
4. Varsayılan admin kullanıcısı otomatik oluşturulacak

#### 3. Konfigürasyon
1. `app/admin/config/database.php` dosyasını düzenleyin:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'buildtech_cms');
define('DB_USER', 'root');
define('DB_PASS', ''); // MySQL şifreniz
```

2. `app/admin/config/config.php` dosyasında site URL'ini güncelleyin:
```php
define('SITE_URL', 'http://localhost/tr.artein');
```

#### 4. Dosya İzinleri
Upload klasörlerini oluşturun:
- `assets/uploads/`
- `assets/uploads/sliders/`
- `assets/uploads/projects/`
- `assets/uploads/media/`

#### 5. Siteye Erişim
- **🌐 Frontend (Ana Sayfa)**: http://localhost/tr.artein
- **🔧 Admin Paneli**: http://localhost/tr.artein/app/admin/login.php

### Varsayılan Giriş Bilgileri
- **Kullanıcı Adı**: `admin`
- **Şifre**: `admin123`

## 📁 Proje Yapısı

```
tr.artein/
├── 🎯 app/                      # UYGULAMA KATMANI
│   ├── 🔧 admin/               # ADMIN PANELİ
│   │   ├── ajax/               # AJAX endpoint'leri
│   │   ├── config/             # Admin konfigürasyonu
│   │   │   ├── config.php      # Geliştirme ayarları
│   │   │   ├── production.php  # Production ayarları
│   │   │   ├── database.php    # Veritabanı ayarları
│   │   │   └── functions.php   # Yardımcı fonksiyonlar
│   │   ├── includes/           # Admin şablonları
│   │   ├── dashboard.php       # Ana kontrol paneli
│   │   ├── projects.php        # Proje yönetimi
│   │   ├── sliders.php         # Slider yönetimi
│   │   ├── media.php           # Medya galerisi
│   │   ├── settings.php        # Site ayarları
│   │   ├── content.php         # İçerik yönetimi
│   │   ├── site-management.php # Site yönetimi
│   │   ├── messages.php        # Mesaj yönetimi
│   │   ├── services.php        # Hizmet yönetimi
│   │   ├── login.php           # Admin girişi
│   │   └── logout.php          # Admin çıkışı
│   ├── 🌐 frontend/            # KULLANICI ARAYÜZÜ
│   │   ├── includes/           # Frontend şablonları
│   │   │   ├── header.php      # Ortak header
│   │   │   ├── footer.php      # Ortak footer
│   │   │   ├── cookie-banner.php # Cookie bildirimi
│   │   │   └── cookie-manager.js # Cookie yönetimi
│   │   ├── index.php           # Ana sayfa
│   │   ├── projeler.php        # Projeler sayfası
│   │   ├── proje-detay.php     # Proje detay sayfası
│   │   ├── biz-kimiz.php       # Hakkımızda sayfası
│   │   ├── manifesto.php       # Manifesto sayfası
│   │   ├── medya-galerisi.php  # Medya galerisi
│   │   └── 404.php             # 404 hata sayfası
│   └── 🤝 shared/              # ORTAK KAYNAKLAR
│       ├── config/             # Ortak konfigürasyon
│       │   └── frontend_config.php
│       ├── database/           # SQL dosyaları
│       │   ├── buildtech_cms.sql
│       │   └── buildtech_cms_fixed.sql
│       └── realtime/           # Gerçek zamanlı özellikler
├── 🎨 assets/                   # STATİK KAYNAKLAR
│   ├── brand/                  # Marka dosyaları
│   │   ├── logos/              # Logo dosyaları
│   │   └── documents/          # Marka dokümanları
│   ├── css/                    # Stil dosyaları
│   │   ├── style.css           # Ana stil dosyası
│   │   ├── responsive-header.css # Responsive header
│   │   ├── artein-brand.css    # Marka stilleri
│   │   └── scss/               # SCSS kaynak dosyaları
│   ├── images/                 # Resim dosyaları
│   ├── js/                     # JavaScript dosyaları
│   ├── uploads/                # YÜKLENEN DOSYALAR
│   │   ├── projects/           # Proje resimleri
│   │   ├── sliders/            # Slider resimleri
│   │   ├── media/              # Medya dosyaları
│   │   └── about/              # Hakkımızda resimleri
│   └── webfonts/               # Web fontları
├── 📦 storage/                  # DEPOLAMA
│   ├── cache/                  # Önbellek dosyaları
│   ├── logs/                   # Log dosyaları
│   └── sessions/               # Session dosyaları
├── 📄 README.md                # Bu dosya
├── 📋 INSTALLATION_STEPS.md    # Detaylı kurulum rehberi
├── 📋 DEPLOYMENT_CHECKLIST.md  # Deployment kontrol listesi
├── 🔒 .htaccess                # Ana güvenlik ve routing
├── 🏠 index.php                # Ana giriş dosyası
└── 📜 LICENSE                  # Lisans dosyası
```

## 🎯 Özellikler

### Admin Panel Özellikleri
- 📊 İstatistiklerle dashboard
- 🖼️ Dinamik slider yönetimi
- 🏗️ Tam proje yönetim sistemi
- 📷 Medya galerisi ve yükleme
- 💌 İletişim formu yönetimi
- ⚙️ Site ayarları paneli
- 🔐 Güvenli kimlik doğrulama
- 📝 İçerik yönetimi sistemi
- 🌐 Site yönetimi araçları

### Frontend Özellikleri
- 📱 Tamamen responsive tasarım
- 🎨 Modern UI/UX
- 🚀 Hızlı yükleme
- 📧 Çalışan iletişim formu
- 🔍 SEO optimize edilmiş
- 📊 Google Analytics hazır
- 🎭 Smooth animasyonlar
- 📱 Mobil uyumlu

## 🔧 Özelleştirme

### Yeni İçerik Ekleme
1. Admin paneline giriş yapın
2. Sezgisel arayüzü kullanarak:
   - Slider'ları ekleyin/düzenleyin
   - Projeleri resimlerle yönetin
   - Site ayarlarını güncelleyin
   - Medya dosyalarını yükleyin

### Tasarım Değiştirme
- CSS'i düzenleyin: `assets/css/style.css`
- Admin şablonlarını değiştirin: `app/admin/includes/`
- Frontend layout'unu güncelleyin: `app/frontend/includes/`

## 🛡️ Güvenlik

- ✅ CSRF koruması aktif
- ✅ SQL injection önleme (PDO prepared statements)
- ✅ Session güvenliği (httponly, secure, samesite)
- ✅ Dosya yükleme doğrulaması (MIME type kontrolü)
- ✅ Admin erişim kontrolü
- ✅ XSS koruması (htmlspecialchars)
- ✅ Güvenli şifreleme (password_hash)
- ✅ .htaccess ile PHP dosya koruması
- ✅ Config klasörü erişim engelleme
- ✅ Upload klasörü güvenliği
- ✅ HTTPS zorunlu (production)
- ✅ Güvenlik header'ları (CSP, X-Frame-Options)

## 🚀 Production Deployment

### FTP Yüklemesi
1. **DEPLOYMENT_CHECKLIST.md** dosyasını takip edin
2. **Production config** dosyasını güncelleyin (`app/admin/config/production.php`)
3. **Domain ayarlarını** yapın (`https://artein.tr`)
4. **SMTP bilgilerini** güncelleyin
5. **.htaccess** dosyalarını yükleyin

### Önemli Dosyalar
- 📋 `DEPLOYMENT_CHECKLIST.md` - Deployment rehberi
- 🔒 `.htaccess` - Ana güvenlik ve routing
- ⚙️ `app/admin/config/production.php` - Production ayarları
- 🛡️ `assets/uploads/.htaccess` - Upload güvenliği
- 🛡️ `app/admin/config/.htaccess` - Config koruması

## 📞 Destek

Sorunlar veya sorular için:
1. Bu README'yi kontrol edin
2. `DEPLOYMENT_CHECKLIST.md` dosyasını inceleyin
3. Konfigürasyon dosyalarını gözden geçirin
4. Veritabanı bağlantısının çalıştığından emin olun
5. Dosya izinlerini doğrulayın

## 🎉 Başarı!

Kurulum tamamlandığında, şunları yapabileceksiniz:
- ✅ Dinamik içerik yönetimi
- ✅ Medya yükleme ve organizasyon
- ✅ İletişim sorgularını yönetme
- ✅ Site istatistiklerini izleme
- ✅ Admin paneli üzerinden her şeyi özelleştirme
- ✅ Production ortamında güvenli çalışma
- ✅ HTTPS ile güvenli iletişim
- ✅ E-posta gönderimi

**ArteIn CMS ile mutlu geliştirmeler!** 🏗️

---

## 🔗 Önemli Linkler

### Frontend Sayfaları
- **Ana Sayfa**: `https://artein.tr`
- **Projeler**: `https://artein.tr/projeler`
- **Hakkımızda**: `https://artein.tr/biz-kimiz`
- **Manifesto**: `https://artein.tr/manifesto`
- **Medya Galerisi**: `https://artein.tr/medya-galerisi`
- **404 Sayfası**: `https://artein.tr/404`

### Admin Paneli
- **Giriş**: `https://artein.tr/app/admin/login.php`
- **Dashboard**: `https://artein.tr/app/admin/dashboard.php`
- **Proje Yönetimi**: `https://artein.tr/app/admin/projects.php`
- **Slider Yönetimi**: `https://artein.tr/app/admin/sliders.php`
- **Medya Yönetimi**: `https://artein.tr/app/admin/media.php`
- **Site Ayarları**: `https://artein.tr/app/admin/settings.php`
- **İçerik Yönetimi**: `https://artein.tr/app/admin/content.php`
- **Site Yönetimi**: `https://artein.tr/app/admin/site-management.php`
- **Mesajlar**: `https://artein.tr/app/admin/messages.php`
- **Hizmetler**: `https://artein.tr/app/admin/services.php`
