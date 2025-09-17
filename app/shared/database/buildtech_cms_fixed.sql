-- BuildTech CMS Database Structure (FIXED VERSION)
-- Bu dosyayı phpMyAdmin'de çalıştırarak veritabanını oluşturun

CREATE DATABASE IF NOT EXISTS buildtech_cms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE buildtech_cms;

-- Admin kullanıcıları tablosu
CREATE TABLE admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'editor') DEFAULT 'editor',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Site ayarları tablosu
CREATE TABLE site_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('text', 'textarea', 'image', 'email', 'phone') DEFAULT 'text',
    description VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Slider içerikleri tablosu
CREATE TABLE sliders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    subtitle TEXT,
    button_text VARCHAR(100),
    button_link VARCHAR(255),
    background_image VARCHAR(255),
    order_index INT DEFAULT 0,
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Hakkımızda içeriği tablosu
CREATE TABLE about_content (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    subtitle TEXT,
    content TEXT NOT NULL,
    image VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Hizmetler tablosu
CREATE TABLE services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    icon VARCHAR(100),
    order_index INT DEFAULT 0,
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Projeler tablosu
CREATE TABLE projects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    content TEXT,
    featured_image VARCHAR(255),
    category VARCHAR(100),
    client VARCHAR(100),
    completion_date DATE,
    location VARCHAR(255),
    budget DECIMAL(15,2),
    order_index INT DEFAULT 0,
    is_featured BOOLEAN DEFAULT 0,
    is_active BOOLEAN DEFAULT 1,
    slug VARCHAR(255) UNIQUE,
    meta_title VARCHAR(255),
    meta_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Proje görselleri tablosu
CREATE TABLE project_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    alt_text VARCHAR(255),
    order_index INT DEFAULT 0,
    is_featured BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);

-- Medya dosyaları tablosu
CREATE TABLE media_files (
    id INT PRIMARY KEY AUTO_INCREMENT,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    file_size INT NOT NULL,
    alt_text VARCHAR(255),
    title VARCHAR(255),
    uploaded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_uploaded_by (uploaded_by)
);

-- İletişim mesajları tablosu
CREATE TABLE contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(255),
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT 0,
    replied_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Site istatistikleri tablosu
CREATE TABLE site_stats (
    id INT PRIMARY KEY AUTO_INCREMENT,
    stat_date DATE NOT NULL,
    page_views INT DEFAULT 0,
    unique_visitors INT DEFAULT 0,
    contact_forms INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_date (stat_date)
);

-- Varsayılan admin kullanıcısı ekle (kullanıcı: admin, şifre: admin123)
INSERT INTO admin_users (username, email, password, full_name, role) VALUES 
('admin', 'admin@buildtech.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Site Yöneticisi', 'admin');

-- Varsayılan site ayarları
INSERT INTO site_settings (setting_key, setting_value, setting_type, description) VALUES 
('site_title', 'BuildTech Engineering', 'text', 'Site başlığı'),
('site_description', 'Modern İnşaat Çözümleri', 'textarea', 'Site açıklaması'),
('company_name', 'Arte In', 'text', 'Şirket adı'),
('company_email', 'info@artein.com', 'email', 'Şirket e-posta'),
('company_phone', '+90 555 888 99 88', 'phone', 'Şirket telefon'),
('company_address', 'İstanbul, Türkiye', 'textarea', 'Şirket adresi'),
('logo', 'assets/images/ArteIn_logos-01.png', 'image', 'Site logosu'),
-- SEO & Analytics
('meta_keywords', 'inşaat, mühendislik, proje, yapı, arte in, buildtech', 'text', 'Meta keywords'),
('google_analytics', '', 'text', 'Google Analytics Measurement ID (GA4)'),
('google_site_verification', '', 'text', 'Google Search Console doğrulama kodu'),
-- Sosyal Medya
('social_whatsapp', 'https://wa.me/905558889988', 'text', 'WhatsApp bağlantısı'),
('social_facebook', 'https://facebook.com/artein', 'text', 'Facebook bağlantısı'),
('social_instagram', 'https://instagram.com/artein', 'text', 'Instagram bağlantısı');

-- Varsayılan slider içerikleri
INSERT INTO sliders (title, subtitle, button_text, button_link, background_image, order_index) VALUES 
('Geleceğin Yapılarını İnşa Ediyoruz', 'Modern mühendislik çözümleri ile hayallerinizi gerçeğe dönüştürüyoruz', 'Bizimle İletişime Geçin', '#contact', 'assets/images/slider1.jpg', 1),
('Yenilikçi Tasarımlar', 'Sürdürülebilir ve modern yapılar için öncü çözümler', 'Projelerimizi İnceleyin', '#projects', 'assets/images/slider2.jpg', 2),
('Profesyonel Uzmanlık', '20 yıllık deneyim ile sektörün öncü firması', 'Hakkımızda', '#about', 'assets/images/slider3.jpg', 3);

-- Varsayılan hakkımızda içeriği
INSERT INTO about_content (title, subtitle, content, image) VALUES 
('Hakkımızda', '"Arte in" adında mühendislik, inşaat ve taahhüt işleri yapan bir şirketiz.', 'Adından aldığı ilham ile sanatı ve mühendislik arasındaki ilişkiyi gösteren bir misyonu bulunmaktadır. Kaliteyi, sürdürülebilirliği, yeniliği, akılcılığı ve güveni hedeflemektedir.', 'assets/images/about.jpg');

-- Varsayılan hizmetler
INSERT INTO services (title, description, icon, order_index) VALUES 
('İnşaat Mühendisliği', 'Modern yapı teknolojileri ile güvenli ve dayanıklı yapılar inşa ediyoruz.', 'fas fa-building', 1),
('Proje Yönetimi', 'A\'dan Z\'ye profesyonel proje yönetimi hizmetleri sunuyoruz.', 'fas fa-tasks', 2),
('Mimari Tasarım', 'Estetik ve fonksiyonelliği bir araya getiren yaratıcı tasarım çözümleri.', 'fas fa-drafting-compass', 3),
('Danışmanlık', 'Uzman kadromuz ile teknik danışmanlık hizmetleri veriyoruz.', 'fas fa-handshake', 4);

-- Varsayılan projeler
INSERT INTO projects (title, description, content, featured_image, category, client, completion_date, location, slug, is_featured) VALUES 
('Modern Ofis Kompleksi', 'Şehrin merkezinde modern bir ofis kompleksi projesi', 'Bu proje, modern mimari anlayışla tasarlanmış, çevre dostu teknolojiler kullanılarak inşa edilmiş bir ofis kompleksidir. Toplam 15.000 m² kapalı alana sahip olan kompleks, 500 kişilik çalışma kapasitesine sahiptir.', 'assets/images/project1.jpg', 'Ticari', 'ABC Holding', '2023-12-01', 'İstanbul', 'modern-ofis-kompleksi', 1),
('Lüks Konut Projesi', 'Deniz manzaralı lüks konut projesi', 'Boğaz manzaralı bu lüks konut projesi, 24 daireden oluşmaktadır. Her daire özel tasarım detayları ve yüksek kalite malzemeler kullanılarak tamamlanmıştır.', 'assets/images/project2.jpg', 'Konut', 'XYZ İnşaat', '2023-08-15', 'İstanbul', 'luks-konut-projesi', 1),
('Endüstriyel Tesis', 'Büyük ölçekli endüstriyel üretim tesisi', 'Modern üretim teknolojilerine uygun olarak tasarlanan bu endüstriyel tesis, 25.000 m² kapalı alana sahiptir. Çelik konstrüksiyon ve prefabrik panel sistemler kullanılmıştır.', 'assets/images/project3.jpg', 'Endüstriyel', 'DEF Sanayi', '2023-06-30', 'Kocaeli', 'endustriyel-tesis', 1);

-- Proje görselleri
INSERT INTO project_images (project_id, image_path, alt_text, order_index, is_featured) VALUES 
(1, 'assets/images/project1.jpg', 'Modern Ofis Kompleksi Ana Görsel', 1, 1),
(2, 'assets/images/project2.jpg', 'Lüks Konut Projesi Ana Görsel', 1, 1),
(3, 'assets/images/project3.jpg', 'Endüstriyel Tesis Ana Görsel', 1, 1);

-- Varsayılan istatistikler
INSERT INTO site_stats (stat_date, page_views, unique_visitors, contact_forms) VALUES 
(CURDATE(), 0, 0, 0);
