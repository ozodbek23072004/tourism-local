<?php
require_once 'includes/db.php';

$places = [
    [
        'name_uz' => 'Chor Minor madrasasi',
        'name_ru' => 'Медресе Чор-Минор',
        'name_en' => 'Chor Minor Madrasah',
        'description_uz' => "Chor Minor (forscha 'to'rtta minora') — Buxorodagi eng o'ziga xos me'moriy yodgorliklardan biri. U 1807-yilda boy turkman savdogari Xalifa Niyozqul tomonidan barpo etilgan. 

Bino aslida kattaroq madrasa majmuasining darvozaxonasi bo'lgan, ammo vaqt o'tishi bilan madrasaning o'zi vayron bo'lgan. Chor Minorning to'rtta minorasi bor va ularning har biri moviy gumbaz bilan tugallangan. Minoralar turlicha bezatilgan bo'lib, ba'zi tadqiqotchilar buni dunyodagi to'rtta asosiy dinning (islom, xristianlik, buddizm, induizm) ramziy tasviri deb hisoblashadi. Binoning ichida kichik masjid va kutubxona joylashgan. Bu obida o'zining ixchamligi va betakror dizayni bilan Buxoroning boshqa yirik inshootlaridan ajralib turadi.",
        'description_ru' => "Чор-Минор (в переводе — 'четыре минарета') — одно из самых необычных и запоминающихся зданий в Бухаре. Оно было построено в 1807 году на средства богатого туркменского купца Халифа Ниязкула.

Здание служило входным порталом в ныне разрушенное медресе. Главная особенность — четыре башни, увенчанные бирюзовыми куполами. Декор каждой башни уникален, что породило теории о том, что они символизируют четыре мировые религии. Внутри здания располагались мечеть и библиотека. Чор-Минор отличается от классических монументальных построек Бухары своей камерностью и изяществом. Объект является частью Всемирного наследия ЮНЕСКО.",
        'description_en' => "Chor Minor (Persian for 'four minarets') is one of the most charming and unique structures in Bukhara. Built in 1807 by Khalif Niyaz-kul, a wealthy merchant, it served as the gatehouse to a larger madrasah complex that has since disappeared.

The building is famous for its four towers topped with vibrant azure domes. Each tower features distinct decorative elements, leading some scholars to believe they symbolize the four major world religions. The interior contains a small prayer hall and a library. Its compact size and whimsical design make it a standout monument in the historic center of Bukhara.",
        'category_id' => 1,
        'region_id' => 2,
        'image' => 'chorminor.png',
        'status' => 'active'
    ],
    [
        'name_uz' => 'Savitskiy nomidagi san\'at muzeyi',
        'name_ru' => 'Музей искусств имени Савицкого',
        'name_en' => 'Savitsky State Museum of Arts',
        'description_uz' => "Nukus shahridagi Savitskiy muzeyi — dunyoga mashhur 'Sahrodagi Luvr' nomi bilan tanilgan. U 1966-yilda olim va rassom Igor Savitskiy tomonidan tashkil etilgan. 

Muzey o'zining rus avangard san'ati to'plami bilan mashhur bo'lib, bu borada dunyoda Sankt-Peterburgdagi Rus muzeyidan keyin ikkinchi o'rinda turadi. Savitskiy sho'ro davrida taqiqlangan va 'begona' deb topilgan asarlarni butun Ittifoq bo'ylab yig'ib, ularni chekka Nukus shahriga olib kelish orqali yo'q bo'lib ketishdan saqlab qolgan. Bundan tashqari, muzeyda Qoraqalpog'istonning qadimiy arxeologik topilmalari va amaliy san'at namunalarining eng boy kolleksiyasi mavjud. Bu muzey bugungi kunda O'zbekistonning eng muhim madaniy xazinalaridan biri hisoblanadi.",
        'description_ru' => "Музей Савицкого в Нукусе, известный во всем мире как 'Лувр в пустыне', является одним из самых значимых культурных центров Узбекистана. Он был основан в 1966 году художником и этнографом Игорем Савицким.

Музей обладает второй по значимости в мире коллекцией произведений русского авангарда. Савицкий годами собирал картины, которые советская власть считала 'идеологически вредными', и свозил их в Нукус, спасая от уничтожения. Помимо авангарда, в музее представлена богатейшая коллекция древних находок из Хорезма и уникальные изделия каракалпакского народного творчества. Этот музей стал местом паломничества для любителей искусства со всего мира.",
        'description_en' => "The Savitsky State Museum of Arts in Nukus is world-renowned as the 'Louvre of the Desert.' Founded in 1966 by the visionary artist and collector Igor Savitsky, it holds a treasure trove of cultural heritage in the remote region of Karakalpakstan.

The museum is most famous for housing the world's second-largest collection of Russian avant-garde art. Savitsky rescued these works from Soviet censorship by moving them to this isolated desert city. Additionally, the museum contains an extensive collection of ancient artifacts from the Khorezm civilization and traditional Karakalpak folk art. It is considered one of the most unexpected and significant art repositories in the world.",
        'category_id' => 3,
        'region_id' => 6, // Qoraqalpog'iston ID
        'image' => 'savitsky.png',
        'status' => 'active'
    ],
    [
        'name_uz' => 'Hazrati Imom majmuasi',
        'name_ru' => 'Комплекс Хазрет Имам',
        'name_en' => 'Hazrati Imam Complex',
        'description_uz' => "Hazrati Imom (Hast Imom) majmuasi — Toshkentning eng muhim diniy va ma'naviy markazidir. Majmua X asrda yashagan buyuk olim va imom Abu Bakr ash-Qaffol Shoshiy qabri atrofida shakllangan.

Majmuaning eng qimmatli boyligi — Muyi Muborak madrasasida saqlanayotgan dunyodagi eng qadimiy Qur'on qo'lyozmasi, ya'ni 'Usmon Qur'oni' (VII asr) hisoblanadi. Shuningdek, bu yerda Baroqxon madrasasi, Tilla Shayx masjidi va muhtasham Hazrati Imom jome masjidi joylashgan. 2007-yilda qayta ta'mirlangan ushbu majmua o'zining baland minoralari va sharqona uslubdagi me'morchiligi bilan Toshkentning ko'rkiga aylanib qolgan. Bu yerda O'zbekiston Musulmonlari idorasi ham joylashgan bo'lib, u butun mamlakatning islomiy markazi hisoblanadi.",
        'description_ru' => "Комплекс Хазрет Имам (Хаст Имам) — духовное сердце Ташкента. Он возник вокруг захоронения великого ученого и первого проповедника ислама в Ташкенте — Абу Бакра Мухаммада ибн Али аш-Каффаль Шаши, жившего в X веке.

Главным сокровищем комплекса является медресе Муйи Муборак, где хранится знаменитый Коран Османа — старейшая в мире рукопись священной книги, датируемая VII веком. Ансамбль также включает в себя медресе Барак-хана, мечеть Тилля-Шейха и грандиозную соборную мечеть Хазрет Имам, возведенную в 2007 году. Комплекс с его величественными 50-метровыми минаретами и резными деревянными колоннами является главной достопримечательностью старого города Ташкента.",
        'description_en' => "The Hazrati Imam (Hast Imam) Complex is the spiritual heart of Tashkent. It was built around the tomb of Abu Bakr Muhammad ibn Ali Ismoil ash-Qaffol Shoshiy, a revered 10th-century scholar and preacher.

The most precious artifact within the complex is the Uthman Quran, kept in the Muyi Muborak Madrasah. It is recognized as the world's oldest surviving Quran manuscript, dating back to the 7th century. The complex also features the Barak-Khan Madrasah, the Tilla Sheikh Mosque, and the grand Hazrati Imam Mosque built in 2007. With its towering minarets and intricate wood carvings, the complex serves as the primary center of Islamic culture and administration in Uzbekistan.",
        'category_id' => 1,
        'region_id' => 4,
        'image' => 'hazratiimam.png',
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
        echo "Error: " . $e->getMessage() . "\n";
    }
}

echo "Seeding complete.\n";
