# Tourism UZ — O'zbekiston sayohat portali

Ushbu loyiha O'zbekistonning turistik maskanlari, tarixiy shaxslari, restoranlari va mehmonxonalari haqida ma'lumot beruvchi professional veb-platformadir.

## 🚀 Texnologiyalar
- **Backend:** PHP 8.1 (Custom architecture)
- **Database:** MySQL 8.0 (PDO)
- **Frontend:** Tailwind CSS, Alpine.js
- **Optimization:** GD Library (Image WebP conversion), File Caching

## ✨ Imkoniyatlar
- **Bosh sahifa:** Dinamik kategoriyalar, viloyatlar va yangi qo'shilgan joylar.
- **Jonli Qidiruv (AJAX):** Sahifani yangilamasdan tezkor qidiruv natijalari.
- **Admin Panel:** Ma'lumotlarni to'liq boshqarish (CRUD).
- **Xavfsizlik:** 
  - CSRF himoyasi
  - Brute-force himoyasi (5 ta urinishdan keyin 15 daqiqa blokirovka)
  - XSS va SQL Injektsiyadan himoya
- **SEO:** 
  - Dinamik XML Sitemap (`/public/sitemap.php`)
  - Meta teglar (OG:Tags)
  - `robots.txt`
- **Performance:** 
  - Rasmlarni avtomatik WebP formatiga o'tkazish va o'lchamini kichraytirish.
  - Bosh sahifani 10 daqiqalik HTML kesh qilish.
  - Rasmlar uchun Lazy Loading.
  - Ko'rilishlar sonini hisoblash (Views counter).

## 🛠 O'rnatish (OpenServer)
1. Fayllarni `c:\OSPanel\domains\tourism.local\` papkasiga joylang.
2. MySQL orqali `tourism_db` bazasini yarating.
3. `database.sql` faylini bazaga import qiling.
4. `includes/config.php` faylida baza ulanish ma'lumotlarini tekshiring.
5. Brauzerda: `http://tourism.local/public/`

## 🔐 Admin Ma'lumotlari
- **URL:** `http://tourism.local/admin/`
- **Login:** `admin`
- **Parol:** `admin123`

---
*Loyihani professional darajaga Antigravity AI yordamida ko'tarildi.*
