<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Disable session ini sets for CLI
if (php_sapi_name() === 'cli') {
    // Prevent config.php from failing on session_start or ini_set if needed
}

require_once 'includes/db.php';

$places = [
    [
        'name_uz' => 'Registon maydoni',
        'name_ru' => 'Площадь Регистан',
        'name_en' => 'Registan Square',
        'description_uz' => 'Samarqanddagi Registon maydoni – Markaziy Osiyodagi eng mahobatli va tarixiy ahamiyatga ega bo‘lgan me’moriy yodgorliklardan biri hamda Samarqand shahrining ramzi hisoblanadi. Majmua uchta ulug‘vor madrasadan iborat: Ulug‘bek madrasasi (1417–1420), Sherdor madrasasi (1619–1636) va Tillakori madrasasi (1646–1660). 2001-yilda UNESCO Butunjahon merosi ro‘yxatiga kiritilgan.',
        'description_ru' => 'Регистан — знаменитая площадь в центре Самарканда, являющаяся одной из самых известных достопримечательностей Центральной Азии. Ансамбль состоит из трёх медресе: Улугбека, Шердор и Тилля-Кари. Регистан был центром жизни города, где зачитывались указы и проводились праздники. В 2001 году включён в Список всемирного наследия ЮНЕСКО.',
        'description_en' => 'Registan Square is the heart of the ancient city of Samarkand and one of the most magnificent architectural ensembles in Central Asia. It is framed by three monumental madrasahs: Ulugbek Madrasah, Sher-Dor Madrasah, and Tilla-Kari Madrasah. It served as a public square and a center of learning. In 2001, it was added to the UNESCO World Heritage List.',
        'category_id' => 1,
        'region_id' => 1,
        'image' => 'registan.png',
        'status' => 'active'
    ],
    [
        'name_uz' => 'Ichan Qal\'a',
        'name_ru' => 'Ичан-Кала',
        'name_en' => 'Itchan Kala',
        'description_uz' => 'Ichan Qal\'a — Xiva shahridagi devor bilan o‘ralgan qadimiy ichki shahar. Bu yerda 50 dan ortiq tarixiy obidalar va 250 ga yaqin eski uylar saqlanib qolgan. Mashhur yodgorliklari: Kalta Minor, Ko‘hna Ark, Juma masjidi va Islom Xo‘ja minorasi. Ichan Qal\'a O‘zbekistondagi birinchi bo‘lib UNESCO ro‘yxatiga kiritilgan obyektdir.',
        'description_ru' => 'Ичан-Кала — «внутренний город» Хивы, окружённый мощными крепостными стенами. Это уникальный музей под открытым небом, где сохранились десятки медресе, мечетей и минаретов. Главные достопримечательности: минарет Кальта-Минар, крепость Куня-Арк и Джума-мечеть. Первый объект Всемирного наследия ЮНЕСКО в Узбекистане.',
        'description_en' => 'Itchan Kala is the walled inner city of Khiva, acting as an open-air museum. It contains more than 50 historic monuments and 250 traditional houses. Key highlights include the Kalta Minor Minaret, the Kunya-Ark Fortress, and the Juma Mosque. It was the first site in Uzbekistan to be inscribed on the UNESCO World Heritage List.',
        'category_id' => 1,
        'region_id' => 3,
        'image' => 'itchan_kala.png',
        'status' => 'active'
    ],
    [
        'name_uz' => 'Ark qal\'asi',
        'name_ru' => 'Крепость Арк',
        'name_en' => 'Ark of Bukhara',
        'description_uz' => 'Ark qal\'asi — Buxorodagi eng qadimgi me’moriy va arxeologik yodgorlik. U asrlar davomida Buxoro amirlarining qarorgohi bo‘lgan. Qal\'a maydoni 4 gektar bo‘lib, balandligi 20 metrgacha yetadigan sun\'iy tepalik ustida joylashgan. Ichida saroylar, masjidlar, zarbxona va zindon kabi binolar mavjud bo\'lgan.',
        'description_ru' => 'Крепость Арк — древняя цитадель в Бухаре, служившая резиденцией бухарских эмиров на протяжении столетий. Крепость расположена на искусственном холме высотой около 20 метров. Внутри неё находились дворцы, мечети, государственные учреждения и тюрьма. Является символом древней Бухары.',
        'description_en' => 'The Ark of Bukhara is a massive fortress that served as the residence of the Emirs of Bukhara for centuries. Standing on an artificial hill 20 meters high, it covers 4 hectares. Historically, it was a "city within a city," containing palaces, mosques, government offices, and a prison.',
        'category_id' => 1,
        'region_id' => 2,
        'image' => 'ark.png',
        'status' => 'active'
    ]
];

foreach ($places as $place) {
    try {
        $stmt = $pdo->prepare("INSERT INTO places (name_uz, name_ru, name_en, description_uz, description_ru, description_en, category_id, region_id, image, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $place['name_uz'], $place['name_ru'], $place['name_en'],
            $place['description_uz'], $place['description_ru'], $place['description_en'],
            $place['category_id'], $place['region_id'], $place['image'], $place['status']
        ]);
        echo "Inserted: " . $place['name_uz'] . "\n";
    } catch (Exception $e) {
        echo "Error inserting " . $place['name_uz'] . ": " . $e->getMessage() . "\n";
    }
}

echo "Done.\n";
