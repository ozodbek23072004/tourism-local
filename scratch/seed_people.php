<?php
require_once 'includes/db.php';

$people = [
    [
        'name_uz' => 'Amir Temur',
        'name_ru' => 'Амир Тимур',
        'name_en' => 'Amir Timur',
        'bio_uz' => "Amir Temur (1336-1405) — buyuk davlat arbobi, mohir sarkarda va markazlashgan Temuriylar davlatining asoschisi. U Shahrisabz yaqinidagi Xo'ja Ilg'or qishlog'ida tug'ilgan. Temur o'zining harbiy yurishlari natijasida Hindistondan Turkiyagacha bo'lgan ulkan imperiyani barpo etgan. U nafaqat jangchi, balki madaniyat va ilm-fan homiysi ham bo'lgan. Samarqandni poytaxt etib belgilab, uni dunyoning eng go'zal shahriga aylantirgan. 'Kuch - adolatdadir' shiori uning davlat boshqaruvidagi asosiy tamoyili edi.",
        'bio_ru' => "Амир Тимур (1336-1405) — великий государственный деятель, полководец и основатель империи Тимуридов со столицей в Самарканде. Он родился в селении Ходжа-Ильгар недалеко от Шахрисабза. В результате своих походов он создал огромную державу, простиравшуюся от Индии до Средиземного моря. Тимур уделял большое внимание развитию науки, культуры и архитектуры. Самарканд при нем стал крупнейшим культурным и научным центром Востока. Его девиз 'Сила в справедливости' стал основой его правления.",
        'bio_en' => "Amir Timur (1336-1405), also known as Tamerlane, was a Turco-Mongol conqueror and the founder of the Timurid Empire in and around modern-day Central Asia. Born near Shahrisabz, he established a vast empire stretching from India to Turkey. Beyond his military conquests, he was a great patron of arts, literature, and architecture. He turned Samarkand into one of the most beautiful and influential cities in the world. His motto, 'Strength is in Justice,' reflected his philosophy of governance.",
        'born_year' => 1336,
        'died_year' => 1405,
        'region_id' => 1,
        'image' => 'amirtemur.png',
        'status' => 'active'
    ],
    [
        'name_uz' => 'Mirzo Ulug\'bek',
        'name_ru' => 'Мирзо Улугбек',
        'name_en' => 'Mirzo Ulugh Beg',
        'bio_uz' => "Mirzo Ulug'bek (1394-1449) — buyuk astronom va matematik, Amir Temurning nabirasi va Samarqand hukmdori. U Samarqandda o'sha davrdagi eng mashhur rasadxonani barpo etgan va 1018 ta yulduzning holatini aniq tasvirlab bergan 'Ziji jadidi Ko'ragoniy' asarini yozgan. Ulug'bek davrida Samarqand jahon ilm-fani markaziga aylandi. U ilm-fanga bo'lgan ishtiyoqi tufayli nafaqat hukmdor, balki buyuk olim sifatida ham tarixda qoldi.",
        'bio_ru' => "Мирзо Улугбек (1394-1449) — великий астроном и математик, внук Амира Тимура и правитель Самарканда. Он построил в Самарканде уникальную обсерваторию, где составил 'Зидж Улугбека' — каталог 1018 звезд, отличающийся поразительной точностью. При его правлении Самарканд стал мировой столицей науки. Улугбек вошел в историю как 'ученый на троне', для которого знания были превыше власти.",
        'bio_en' => "Mirzo Ulugh Beg (1394-1449) was a Timurid ruler as well as an astronomer and mathematician. He was the grandson of Amir Timur. He is most famous for building a great observatory in Samarkand and for his star catalogue, 'Zij-i Sultani,' which detailed the positions of 1,018 stars with incredible precision. Under his rule, Samarkand became a global center for scientific discovery and learning.",
        'born_year' => 1394,
        'died_year' => 1449,
        'region_id' => 1,
        'image' => 'ulughbek.png',
        'status' => 'active'
    ],
    [
        'name_uz' => 'Abu Ali ibn Sino',
        'name_ru' => 'Абу Али ибн Сина',
        'name_en' => 'Avicenna',
        'bio_uz' => "Abu Ali ibn Sino (980-1037) — dunyoga mashhur olim, faylasuf va tabib, G'arbda Avitsenna nomi bilan tanilgan. U Buxoro yaqinidagi Afshona qishlog'ida tug'ilgan. Uning 'Tib qonunlari' asari bir necha asrlar davomida Yevropa universitetlarida asosiy darslik bo'lib xizmat qilgan. Ibn Sino tibbiyotdan tashqari, falsafa, astronomiya va matematika sohalarida ham buyuk kashfiyotlar qilgan. U jami 450 dan ortiq asar yozgan bo'lib, ulardan 240 tasi bizgacha saqlanib qolgan.",
        'bio_ru' => "Абу Али ибн Сина (980-1037), известный на Западе как Авиценна — великий ученый-энциклопедист, философ и врач. Родился в селении Афшана близ Бухары. Его главный труд 'Канон врачебной науки' на протяжении веков был настольной книгой медиков во всем мире. Ибн Сина внес неоценимый вклад в развитие философии, астрономии и математики. Он является автором более 450 трудов, охватывающих почти все области знаний того времени.",
        'bio_en' => "Abu Ali ibn Sino (980-1037), widely known in the West as Avicenna, was a Persian polymath who is regarded as one of the most significant physicians, astronomers, and thinkers of the Islamic Golden Age. Born near Bukhara, his most famous work, 'The Canon of Medicine,' was the standard medical text in European and Islamic universities for centuries. He wrote nearly 450 treatises on a wide range of subjects, including philosophy, ethics, and science.",
        'born_year' => 980,
        'died_year' => 1037,
        'region_id' => 2,
        'image' => 'ibnsino.png',
        'status' => 'active'
    ]
];

foreach ($people as $person) {
    try {
        $stmt = $pdo->prepare("INSERT INTO people (name_uz, name_ru, name_en, bio_uz, bio_ru, bio_en, born_year, died_year, region_id, image, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $person['name_uz'], $person['name_ru'], $person['name_en'],
            $person['bio_uz'], $person['bio_ru'], $person['bio_en'],
            $person['born_year'], $person['died_year'], $person['region_id'],
            $person['image'], $person['status']
        ]);
        echo "Inserted: " . $person['name_uz'] . "\n";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

echo "Seeding complete.\n";
