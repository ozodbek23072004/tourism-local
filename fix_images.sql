-- ============================================================
-- Fix: Real rasmlar URL larini o'rnatish
-- Wikimedia Commons dan yuqori sifatli rasmlar
-- ============================================================

-- PLACES
UPDATE places SET image = 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/ac/Registan_square_Samarkand.jpg/1280px-Registan_square_Samarkand.jpg' WHERE id = 1;
UPDATE places SET image = 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/51/Itchan_Kala%2C_Khiva_%2821848826938%29.jpg/1280px-Itchan_Kala%2C_Khiva_%2821848826938%29.jpg' WHERE id = 2;
UPDATE places SET image = 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/05/Ark_fortress_in_Bukhara.jpg/1280px-Ark_fortress_in_Bukhara.jpg' WHERE id = 3;
UPDATE places SET image = 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/67/Shah-i-Zinda_panorama.jpg/1280px-Shah-i-Zinda_panorama.jpg' WHERE id = 4;
UPDATE places SET image = 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/42/Gur-e_Amir_Samarkand_%2836589640836%29.jpg/1280px-Gur-e_Amir_Samarkand_%2836589640836%29.jpg' WHERE id = 5;
UPDATE places SET image = 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/da/Bukhara%2C_Kalon_minaret_and_mosque.jpg/1280px-Bukhara%2C_Kalon_minaret_and_mosque.jpg' WHERE id = 6;
UPDATE places SET image = 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c3/Bibi-Khanym_Mosque.jpg/1280px-Bibi-Khanym_Mosque.jpg' WHERE id = 7;
UPDATE places SET image = 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/89/Chor_minor_new.JPG/800px-Chor_minor_new.JPG' WHERE id = 8;
UPDATE places SET image = 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2a/Savitsky_karakalpakstan_museum.jpg/1280px-Savitsky_karakalpakstan_museum.jpg' WHERE id = 9;
UPDATE places SET image = 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/37/Khast_Imam_Complex_in_Tashkent.jpg/1280px-Khast_Imam_Complex_in_Tashkent.jpg' WHERE id = 10;

-- PEOPLE
UPDATE people SET image = 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/57/Timur_reconstruction03.jpg/800px-Timur_reconstruction03.jpg' WHERE id = 1;
UPDATE people SET image = 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8a/Ulugh_Beg_Observatory_in_Samarkand.jpg/800px-Ulugh_Beg_Observatory_in_Samarkand.jpg' WHERE id = 2;
UPDATE people SET image = 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/48/Avicenna_TajikistanP17-20Somoni-1999_%28cropped%29.png/800px-Avicenna_TajikistanP17-20Somoni-1999_%28cropped%29.png' WHERE id = 3;

-- HOTELS (agar mavjud bo'lsa)
UPDATE hotels SET image = 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80' WHERE id = 1 AND image IS NOT NULL;
UPDATE hotels SET image = 'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=800&q=80' WHERE id = 2 AND image IS NOT NULL;
UPDATE hotels SET image = 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800&q=80' WHERE id = 3 AND image IS NOT NULL;
UPDATE hotels SET image = 'https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=800&q=80' WHERE id = 4 AND image IS NOT NULL;
UPDATE hotels SET image = 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800&q=80' WHERE id = 5 AND image IS NOT NULL;

-- RESTAURANTS (agar mavjud bo'lsa)
UPDATE restaurants SET image = 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=800&q=80' WHERE id = 1 AND image IS NOT NULL;
UPDATE restaurants SET image = 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=800&q=80' WHERE id = 2 AND image IS NOT NULL;
UPDATE restaurants SET image = 'https://images.unsplash.com/photo-1559339352-11d035aa65de?w=800&q=80' WHERE id = 3 AND image IS NOT NULL;
UPDATE restaurants SET image = 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800&q=80' WHERE id = 4 AND image IS NOT NULL;
UPDATE restaurants SET image = 'https://images.unsplash.com/photo-1552566626-52f8b828add9?w=800&q=80' WHERE id = 5 AND image IS NOT NULL;

-- Keshni tozalash uchun
DELETE FROM places WHERE 0;

SELECT 'Images fix completed!' AS status;
