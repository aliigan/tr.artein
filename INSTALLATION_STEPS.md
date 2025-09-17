# BuildTech CMS - Detaylı Kurulum Rehberi

## 📋 İçindekiler
1. [Sistem Gereksinimleri](#sistem-gereksinimleri)
2. [Kurulum Adımları](#kurulum-adımları)
3. [Veritabanı Konfigürasyonu](#veritabanı-konfigürasyonu)
4. [Dosya İzinleri](#dosya-izinleri)
5. [Güvenlik Ayarları](#güvenlik-ayarları)
6. [Test ve Doğrulama](#test-ve-doğrulama)
7. [Sorun Giderme](#sorun-giderme)

## 🖥️ Sistem Gereksinimleri

### Minimum Gereksinimler
- **PHP**: 7.4.0 veya üzeri
- **MySQL**: 5.7.0 veya üzeri
- **Web Server**: Apache 2.4 veya Nginx 1.18
- **RAM**: 512MB (önerilen: 1GB)
- **Disk**: 100MB boş alan

### Önerilen Gereksinimler
- **PHP**: 8.0 veya üzeri
- **MySQL**: 8.0 veya üzeri
- **Web Server**: Apache 2.4 veya Nginx 1.20
- **RAM**: 2GB veya üzeri
- **Disk**: 500MB boş alan

### PHP Uzantıları
Aşağıdaki PHP uzantılarının yüklü olması gerekmektedir:
- `mysqli` veya `pdo_mysql`
- `gd` (resim işleme için)
- `fileinfo` (dosya türü tespiti için)
- `openssl` (güvenlik için)
- `mbstring` (çok baytlı string desteği)
- `json` (JSON işleme)

## 🚀 Kurulum Adımları

### Adım 1: Geliştirme Ortamı Kurulumu

#### XAMPP ile Kurulum (Önerilen)
1. **XAMPP'ı İndirin**
   - https://www.apachefriends.org/ adresinden indirin
   - Windows için en son sürümü seçin

2. **XAMPP'ı Yükleyin**
   - İndirilen dosyayı çalıştırın
   - Kurulum sırasında Apache ve MySQL'i seçin
   - Kurulum tamamlandıktan sonra XAMPP Control Panel'i açın

3. **Servisleri Başlatın**
   - Apache servisini başlatın
   - MySQL servisini başlatın
   - Her ikisinin de "Running" durumunda olduğundan emin olun

#### WAMP ile Kurulum
1. **WAMP'ı İndirin**
   - https://www.wampserver.com/ adresinden indirin

2. **WAMP'ı Yükleyin ve Başlatın**
   - Kurulum tamamlandıktan sonra WAMP'ı başlatın
   - Sistem tepsisinde yeşil ikon görünene kadar bekleyin

### Adım 2: Proje Dosyalarını Yerleştirme

1. **Proje Klasörünü Kopyalayın**
   ```bash
   # XAMPP için
   C:\xampp\htdocs\buildtech-main\
   
   # WAMP için
   C:\wamp64\www\buildtech-main\
   ```

2. **Klasör Yapısını Kontrol Edin**
   ```
   buildtech-main/
   ├── app/
   │   ├── admin/
   │   ├── frontend/
   │   └── shared/
   ├── assets/
   ├── uploads/
   └── README.md
   ```

### Adım 3: Veritabanı Oluşturma

1. **phpMyAdmin'i Açın**
   - Tarayıcıda `http://localhost/phpmyadmin` adresine gidin

2. **Yeni Veritabanı Oluşturun**
   - "New" butonuna tıklayın
   - Veritabanı adı: `buildtech_cms`
   - Collation: `utf8mb4_unicode_ci`
   - "Create" butonuna tıklayın

3. **SQL Dosyasını İmport Edin**
   - Oluşturulan veritabanını seçin
   - "Import" sekmesine gidin
   - "Choose File" butonuna tıklayın
   - `app/shared/database/buildtech_cms.sql` dosyasını seçin
   - "Go" butonuna tıklayın

## ⚙️ Veritabanı Konfigürasyonu

### Adım 1: Veritabanı Ayarları

1. **Database Konfigürasyon Dosyasını Düzenleyin**
   - `app/admin/config/database.php` dosyasını açın
   - Aşağıdaki değerleri güncelleyin:

   ```php
   <?php
   // Veritabanı ayarları
   define('DB_HOST', 'localhost');        // Veritabanı sunucusu
   define('DB_NAME', 'buildtech_cms');    // Veritabanı adı
   define('DB_USER', 'root');             // Kullanıcı adı
   define('DB_PASS', '');                 // Şifre (XAMPP için boş)
   define('DB_CHARSET', 'utf8mb4');       // Karakter seti
   ?>
   ```

2. **Site URL Ayarları**
   - `app/admin/config/config.php` dosyasını açın
   - Site URL'ini güncelleyin:

   ```php
   <?php
   // Site ayarları
   define('SITE_URL', 'http://localhost/buildtech-main');
   define('SITE_NAME', 'BuildTech CMS');
   define('ADMIN_EMAIL', 'admin@example.com');
   ?>
   ```

### Adım 2: Frontend Konfigürasyonu

1. **Frontend Config Dosyasını Kontrol Edin**
   - `app/shared/config/frontend_config.php` dosyası otomatik olarak doğru yolları kullanır
   - Herhangi bir değişiklik yapmanıza gerek yoktur

## 📁 Dosya İzinleri

### Windows için
Windows'ta genellikle dosya izinleri sorunu yaşanmaz, ancak upload klasörlerinin yazılabilir olduğundan emin olun.

### Linux/macOS için
```bash
# Upload klasörlerini oluşturun
mkdir -p uploads/sliders
mkdir -p uploads/projects
mkdir -p uploads/media

# İzinleri ayarlayın
chmod 755 uploads/
chmod 755 uploads/sliders/
chmod 755 uploads/projects/
chmod 755 uploads/media/

# Web sunucusu kullanıcısına sahiplik verin
chown -R www-data:www-data uploads/
```

## 🔒 Güvenlik Ayarları

### Adım 1: Admin Şifresini Değiştirin

1. **Admin Paneline Giriş Yapın**
   - `http://localhost/buildtech-main/app/admin/login.php` adresine gidin
   - Varsayılan giriş bilgileri:
     - Kullanıcı adı: `admin`
     - Şifre: `admin123`

2. **Şifreyi Değiştirin**
   - Dashboard'a gidin
   - "Site Ayarları" bölümüne gidin
   - Admin şifresini güçlü bir şifre ile değiştirin

### Adım 2: Güvenlik Kontrolleri

1. **Upload Klasörü Güvenliği**
   - `uploads/` klasörüne `.htaccess` dosyası ekleyin:
   ```apache
   # PHP dosyalarının çalıştırılmasını engelle
   <Files "*.php">
       Order Deny,Allow
       Deny from all
   </Files>
   ```

2. **Config Klasörü Güvenliği**
   - `app/admin/config/` klasörüne `.htaccess` dosyası ekleyin:
   ```apache
   # Config dosyalarına doğrudan erişimi engelle
   Order Deny,Allow
   Deny from all
   ```

## ✅ Test ve Doğrulama

### Adım 1: Frontend Testi

1. **Ana Sayfayı Test Edin**
   - `http://localhost/buildtech-main/app/frontend/index.php` adresine gidin
   - Sayfanın düzgün yüklendiğini kontrol edin
   - Responsive tasarımı test edin

2. **Diğer Sayfaları Test Edin**
   - Projeler: `http://localhost/buildtech-main/app/frontend/projeler.php`
   - Hakkımızda: `http://localhost/buildtech-main/app/frontend/biz-kimiz.php`
   - Manifesto: `http://localhost/buildtech-main/app/frontend/manifesto.php`

### Adım 2: Admin Panel Testi

1. **Admin Girişi Test Edin**
   - `http://localhost/buildtech-main/app/admin/login.php` adresine gidin
   - Giriş yapabildiğinizi kontrol edin

2. **Admin Fonksiyonlarını Test Edin**
   - Dashboard'u kontrol edin
   - Proje ekleme/düzenleme test edin
   - Slider yönetimi test edin
   - Medya yükleme test edin

### Adım 3: Veritabanı Testi

1. **Veri Ekleme Testi**
   - Admin panelinden yeni proje ekleyin
   - Veritabanında kaydedildiğini kontrol edin

2. **Frontend Görüntüleme Testi**
   - Eklenen projenin frontend'de göründüğünü kontrol edin

## 🔧 Sorun Giderme

### Yaygın Sorunlar ve Çözümleri

#### 1. "Database connection failed" Hatası
**Sebep**: Veritabanı bağlantı ayarları yanlış
**Çözüm**:
- `app/admin/config/database.php` dosyasını kontrol edin
- MySQL servisinin çalıştığından emin olun
- Veritabanı adının doğru olduğunu kontrol edin

#### 2. "404 Not Found" Hatası
**Sebep**: URL yolları yanlış
**Çözüm**:
- `app/admin/config/config.php` dosyasındaki `SITE_URL` ayarını kontrol edin
- Apache mod_rewrite modülünün aktif olduğundan emin olun

#### 3. "Permission denied" Hatası
**Sebep**: Dosya izinleri yanlış
**Çözüm**:
- Upload klasörlerinin yazılabilir olduğundan emin olun
- Web sunucusu kullanıcısının gerekli izinlere sahip olduğunu kontrol edin

#### 4. "Fatal error: Class not found" Hatası
**Sebep**: PHP dosya yolları yanlış
**Çözüm**:
- `require_once` yollarını kontrol edin
- Dosyaların doğru konumda olduğundan emin olun

#### 5. Resimler Görünmüyor
**Sebep**: Asset yolları yanlış
**Çözüm**:
- `assets/` klasörünün doğru konumda olduğunu kontrol edin
- CSS ve JS dosyalarının yüklendiğini kontrol edin

### Debug Modu

Geliştirme sırasında hataları görmek için:

1. **PHP Error Reporting'i Açın**
   ```php
   // app/shared/config/frontend_config.php dosyasında
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   ```

2. **MySQL Error Log'larını Kontrol Edin**
   - XAMPP: `C:\xampp\mysql\data\*.err`
   - WAMP: `C:\wamp64\logs\mysql_error.log`

3. **Apache Error Log'larını Kontrol Edin**
   - XAMPP: `C:\xampp\apache\logs\error.log`
   - WAMP: `C:\wamp64\logs\apache_error.log`

## 🎉 Kurulum Tamamlandı!

Artık BuildTech CMS'iniz kullanıma hazır! 

### Sonraki Adımlar:
1. ✅ Admin panelinden site ayarlarını yapılandırın
2. ✅ İlk projelerinizi ekleyin
3. ✅ Slider'larınızı oluşturun
4. ✅ İletişim bilgilerinizi güncelleyin
5. ✅ Site tasarımını özelleştirin

**Başarılı kurulum için tebrikler!** 🎊