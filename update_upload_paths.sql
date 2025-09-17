-- Uploads klasör yollarını güncelle
-- Eski uploads/ yollarını assets/uploads/ olarak değiştir

-- Sliders tablosundaki background_image yollarını güncelle
UPDATE sliders 
SET background_image = REPLACE(background_image, 'uploads/', 'assets/uploads/')
WHERE background_image LIKE 'uploads/%';

-- Projects tablosundaki featured_image yollarını güncelle
UPDATE projects 
SET featured_image = REPLACE(featured_image, 'uploads/', 'assets/uploads/')
WHERE featured_image LIKE 'uploads/%';

-- Project_images tablosundaki image_path yollarını güncelle
UPDATE project_images 
SET image_path = REPLACE(image_path, 'uploads/', 'assets/uploads/')
WHERE image_path LIKE 'uploads/%';

-- About_content tablosundaki image yollarını güncelle
UPDATE about_content 
SET image = REPLACE(image, 'uploads/', 'assets/uploads/')
WHERE image LIKE 'uploads/%';

-- Site_settings tablosundaki logo yollarını güncelle
UPDATE site_settings 
SET setting_value = REPLACE(setting_value, 'uploads/', 'assets/uploads/')
WHERE setting_key = 'logo' AND setting_value LIKE 'uploads/%';
