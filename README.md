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
define('SITE_URL', 'http://localhost/buildtech-main');
```

#### 4. Dosya İzinleri
Upload klasörlerini oluşturun:
- `uploads/`
- `uploads/sliders/`
- `uploads/projects/`
- `uploads/media/`

#### 5. Siteye Erişim
- **🌐 Frontend (Ana Sayfa)**: http://localhost/buildtech-main/app/frontend/index.php
- **🔧 Admin Paneli**: http://localhost/buildtech-main/app/admin/login.php

### Varsayılan Giriş Bilgileri
- **Kullanıcı Adı**: `admin`
- **Şifre**: `admin123`

## 📁 Proje Yapısı (Context7 Best Practices)

```
buildtech-main/
├── 🎯 app/                      # UYGULAMA KATMANI
│   ├── 🔧 admin/               # ADMIN PANELİ
│   │   ├── ajax/               # AJAX endpoint'leri
│   │   ├── config/             # Admin konfigürasyonu
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
│   │   │   └── footer.php      # Ortak footer
│   │   ├── index.php           # Ana sayfa
│   │   ├── projeler.php        # Projeler sayfası
│   │   ├── proje-detay.php     # Proje detay sayfası
│   │   ├── biz-kimiz.php       # Hakkımızda sayfası
│   │   ├── manifesto.php       # Manifesto sayfası
│   │   └── 404.php             # 404 hata sayfası
│   └── 🤝 shared/              # ORTAK KAYNAKLAR
│       ├── config/             # Ortak konfigürasyon
│       │   └── frontend_config.php
│       └── database/           # SQL dosyaları
│           ├── buildtech_cms.sql
│           └── buildtech_cms_fixed.sql
├── 🎨 assets/                   # STATİK KAYNAKLAR
│   ├── brand/                  # Marka dosyaları
│   │   ├── logos/              # Logo dosyaları
│   │   └── documents/          # Marka dokümanları
│   ├── css/                    # Stil dosyaları
│   │   ├── style.css           # Ana stil dosyası
│   │   ├── responsive-header.css # Responsive header
│   │   └── artein-brand.css    # Marka stilleri
│   ├── images/                 # Resim dosyaları
│   ├── js/                     # JavaScript dosyaları
│   └── webfonts/               # Web fontları
├── 📤 uploads/                 # YÜKLENEN DOSYALAR
│   ├── projects/               # Proje resimleri
│   └── sliders/                # Slider resimleri
├── 📄 README.md                # Bu dosya
├── 📋 INSTALLATION_STEPS.md    # Detaylı kurulum rehberi
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
- ✅ SQL injection önleme
- ✅ Session güvenliği
- ✅ Dosya yükleme doğrulaması
- ✅ Admin erişim kontrolü
- ✅ XSS koruması
- ✅ Güvenli şifreleme

## 📞 Destek

Sorunlar veya sorular için:
1. Bu README'yi kontrol edin
2. Konfigürasyon dosyalarını gözden geçirin
3. Veritabanı bağlantısının çalıştığından emin olun
4. Dosya izinlerini doğrulayın

## 🎉 Başarı!

Kurulum tamamlandığında, şunları yapabileceksiniz:
- ✅ Dinamik içerik yönetimi
- ✅ Medya yükleme ve organizasyon
- ✅ İletişim sorgularını yönetme
- ✅ Site istatistiklerini izleme
- ✅ Admin paneli üzerinden her şeyi özelleştirme

**BuildTech CMS ile mutlu geliştirmeler!** 🏗️

---

## 🔗 Önemli Linkler

### Frontend Sayfaları
- **Ana Sayfa**: `/app/frontend/index.php`
- **Projeler**: `/app/frontend/projeler.php`
- **Hakkımızda**: `/app/frontend/biz-kimiz.php`
- **Manifesto**: `/app/frontend/manifesto.php`
- **404 Sayfası**: `/app/frontend/404.php`

### Admin Paneli
- **Giriş**: `/app/admin/login.php`
- **Dashboard**: `/app/admin/dashboard.php`
- **Proje Yönetimi**: `/app/admin/projects.php`
- **Slider Yönetimi**: `/app/admin/sliders.php`
- **Medya Yönetimi**: `/app/admin/media.php`
- **Site Ayarları**: `/app/admin/settings.php`
- **İçerik Yönetimi**: `/app/admin/content.php`
- **Site Yönetimi**: `/app/admin/site-management.php`
- **Mesajlar**: `/app/admin/messages.php`
- **Hizmetler**: `/app/admin/services.php`
