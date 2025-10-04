# ArteIn CMS - FTP Deployment Kontrol Listesi

## 🚀 FTP Yüklemesi Öncesi Kontroller

### ✅ Dosya Hazırlığı
- [ ] Production config dosyası güncellendi (`app/admin/config/production.php`)
- [ ] Domain adı doğru ayarlandı (`https://artein.tr`)
- [ ] E-posta ayarları güncellendi
- [ ] .htaccess dosyaları oluşturuldu
- [ ] Upload klasörü güvenlik ayarları yapıldı

### ✅ Dosya Yapısı Kontrolü
- [ ] `app/` klasörü ve alt klasörleri
- [ ] `assets/` klasörü ve alt klasörleri
- [ ] `storage/` klasörü (cache, logs, sessions)
- [ ] `index.php` ana dosyası
- [ ] `.htaccess` dosyası

### ✅ Güvenlik Kontrolleri
- [ ] Config klasörü korumalı (.htaccess)
- [ ] Upload klasörü korumalı (.htaccess)
- [ ] PHP dosyaları gizli
- [ ] HTTPS yönlendirmesi aktif

## 🌐 Sunucu Tarafı Hazırlık

### ✅ Sunucu Gereksinimleri
- [ ] PHP 7.4+ yüklü
- [ ] MySQL 5.7+ yüklü
- [ ] Apache/Nginx çalışıyor
- [ ] mod_rewrite aktif
- [ ] SSL sertifikası aktif

### ✅ Veritabanı Hazırlığı
- [ ] MySQL veritabanı oluşturuldu
- [ ] Veritabanı kullanıcısı oluşturuldu
- [ ] SQL dosyası import edildi (`app/shared/database/buildtech_cms.sql`)
- [ ] Veritabanı bağlantı bilgileri güncellendi

### ✅ Dosya İzinleri
- [ ] Upload klasörleri yazılabilir (755)
- [ ] Storage klasörleri yazılabilir (755)
- [ ] PHP dosyaları okunabilir (644)

## 📤 FTP Yükleme Adımları

### 1. Dosya Yükleme
1. Tüm proje dosyalarını FTP ile yükleyin
2. Dosya yapısını koruyun
3. Hidden dosyaları (.htaccess) yüklemeyi unutmayın

### 2. Veritabanı Bağlantısı
1. `app/admin/config/database.php` dosyasını sunucu bilgileriyle güncelleyin
2. Veritabanı bağlantısını test edin

### 3. Site URL Ayarları
1. `app/admin/config/production.php` dosyasında domain'i kontrol edin
2. Tüm URL'lerin doğru olduğundan emin olun

## 🔍 Yükleme Sonrası Testler

### ✅ Frontend Testleri
- [ ] Ana sayfa açılıyor (`https://artein.tr`)
- [ ] Projeler sayfası çalışıyor
- [ ] Hakkımızda sayfası çalışıyor
- [ ] İletişim formu çalışıyor
- [ ] Responsive tasarım çalışıyor

### ✅ Admin Panel Testleri
- [ ] Admin girişi çalışıyor (`https://artein.tr/app/admin/login.php`)
- [ ] Dashboard açılıyor
- [ ] Proje ekleme/düzenleme çalışıyor
- [ ] Medya yükleme çalışıyor
- [ ] Site ayarları çalışıyor

### ✅ Güvenlik Testleri
- [ ] HTTPS zorunlu çalışıyor
- [ ] Config dosyalarına erişim engellenmiş
- [ ] Upload klasörü güvenli
- [ ] Admin paneli korumalı

## 🚨 Kritik Notlar

### ⚠️ Önemli
- **SMTP şifresini** production config'de güncelleyin
- **Veritabanı bilgilerini** sunucuya göre ayarlayın
- **SSL sertifikasının** aktif olduğundan emin olun
- **Backup** almayı unutmayın

### 🔧 Sorun Giderme
- 500 hatası: .htaccess dosyasını kontrol edin
- 404 hatası: mod_rewrite'ın aktif olduğunu kontrol edin
- Veritabanı hatası: Bağlantı bilgilerini kontrol edin
- Resimler görünmüyor: Dosya yollarını kontrol edin

## 📞 Destek
Sorun yaşarsanız:
1. Error log'ları kontrol edin
2. Veritabanı bağlantısını test edin
3. Dosya izinlerini kontrol edin
4. .htaccess ayarlarını kontrol edin

---
**Başarılı deployment için tebrikler!** 🎉
