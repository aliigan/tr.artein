-- Site ayarlarına yeni iletişim ve sosyal medya alanları ekle

-- İletişim bilgileri (eğer yoksa ekle)
INSERT IGNORE INTO site_settings (setting_key, setting_value, setting_description) VALUES
('company_email', 'info@artein.com', 'Şirket e-posta adresi'),
('company_phone', '+90 555 888 99 88', 'Şirket telefon numarası'),
('company_address', 'İstanbul, Türkiye', 'Şirket adresi');

-- Sosyal medya linkleri (eğer yoksa ekle)
INSERT IGNORE INTO site_settings (setting_key, setting_value, setting_description) VALUES
('facebook_url', '', 'Facebook sayfa URL'),
('instagram_url', '', 'Instagram hesap URL'),
('linkedin_url', '', 'LinkedIn şirket sayfası URL'),
('youtube_url', '', 'YouTube kanal URL'),
('twitter_url', '', 'Twitter hesap URL'),
('whatsapp_number', '+90 555 888 99 88', 'WhatsApp telefon numarası');
