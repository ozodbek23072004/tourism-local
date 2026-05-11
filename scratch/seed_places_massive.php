<?php
require_once 'includes/db.php';

$places = [
    [
        'id' => 1,
        'name_uz' => 'Registon maydoni',
        'name_ru' => 'Площадь Регистан',
        'name_en' => 'Registan Square',
        'description_uz' => "Registon maydoni — Samarqand shahrining qadimiy markazi bo'lib, o'zining uchta ulkan va mahobatli madrasasi bilan butun dunyoga mashhur. Bu maydon asrlar davomida shahar ijtimoiy, iqtisodiy va siyosiy hayotining markazi bo'lib xizmat qilgan. 

1. Ulug'bek madrasasi (1417–1420): Mirzo Ulug'bek tomonidan bunyod etilgan ushbu madrasa o'z davrining eng yirik ilmiy akademiyasi bo'lgan. Bu yerda matematika, astronomiya va din ilmlari o'qitilgan. Madrasa peshtog'idagi yulduzli naqshlar Ulug'bekning ilm-fanga bo'lgan ishtiyoqini anglatadi.
2. Sherdor madrasasi (1619–1636): Samarqand hokimi Yalangto'sh Bahodir buyrug'i bilan qurilgan. Uning peshtog'ida ikki sher (yo'lbars) ohu ortidan quvayotgani va ularning ortida odam qiyofasidagi quyosh tasvirlangan. Bu tasvir Samarqand ramziga aylangan.
3. Tillakori madrasasi (1646–1660): Majmuani yakunlovchi ushbu bino ham madrasa, ham juma masjidi vazifasini o'tagan. Binoning ichki qismi 'kundal' uslubida oltin suvi bilan nihoyatda boy bezatilgani uchun unga 'Tillakori' (oltin bilan bezatilgan) nomi berilgan.

Registon maydoni 2001-yilda UNESCO Butunjahon merosi ro'yxatiga kiritilgan va bugungi kunda O'zbekistonning tashrif qog'ozi hisoblanadi.",
        'description_ru' => "Площадь Регистан — это сердце древнего Самарканда, величественный архитектурный ансамбль, признанный во всем мире. На протяжении столетий Регистан был центром торговли, политики и религии.

1. Медресе Улугбека (1417–1420): Построенное великим астрономом и правителем Мирзо Улугбеком, это здание служило крупнейшим научным центром своего времени. Здесь преподавали точные науки, такие как математика и астрономия. Фасад украшен мозаикой с изображением звезд.
2. Медресе Шердор (1619–1636): Построено по приказу хокима Самарканда Ялангтуша Бахадура. Название 'Шердор' означает 'Обитель львов'. На его портале изображены львы (тигры), преследующие ланей на фоне восходящего солнца с человеческим лицом.
3. Медресе Тилля-Кари (1646–1660): Завершает ансамбль с северной стороны. Оно выполняло функции школы и главной мечети города. Интерьер мечети поражает роскошью: огромные поверхности покрыты сусальным золотом в технике 'кундаль', за что медресе и получило свое название.

Регистан включен в список Всемирного наследия ЮНЕСКО в 2001 году и является символом мастерства восточных зодчих.",
        'description_en' => "Registan Square is the crown jewel of Samarkand, a monumental public square framed by three massive, azure-domed Islamic schools (madrasahs). It was the public center of the city, where royal edicts were read and celebrations held.

1. Ulugh Beg Madrasah (1417–1420): Built by the scholar-king Ulugh Beg, Timur's grandson, it was one of the world's leading educational institutions. Stars on its portal reflect Ulugh Beg's passion for astronomy.
2. Sher-Dor Madrasah (1619–1636): Commissioned by Yalangtush Bahadur, the governor of Samarkand. Its name means 'Possessing Lions,' referring to the unique mosaics on its portal showing lions (or tigers) chasing deer under a human-faced sun.
3. Tilla-Kari Madrasah (1646–1660): This final addition served as both a madrasah and the city's grand Friday mosque. The interior is lavishly decorated with gold leaf (Tilla-Kari means 'gilded'), creating a breathtaking golden glow within the prayer hall.

In 2001, the entire complex was inscribed on the UNESCO World Heritage List, representing the pinnacle of Timurid and Shaybanid architecture.",
        'category_id' => 1,
        'region_id' => 1,
        'image' => 'registan.png',
        'status' => 'active'
    ],
    [
        'id' => 2,
        'name_uz' => 'Ichan Qal\'a',
        'name_ru' => 'Ичан-Кала',
        'name_en' => 'Itchan Kala',
        'description_uz' => "Ichan Qal'a — Xiva shahrining markazida joylashgan, baland devorlar bilan o'ralgan 'ichki shahar'. Bu shahar Ochiq osmon ostidagi muzey deb ataladi, chunki unda o'rta asrlarga xos atmosfera va me'moriy obidalar to'liq saqlanib qolgan.

Qal'aning mudofaa devorlari 2 kilometrga yaqin bo'lib, balandligi 8-10 metrni tashkil etadi. Shaharga to'rtta darvoza: Ota darvoza, Bog'cha darvoza, Polvon darvoza va Tosh darvoza orqali kiriladi. 

Asosiy yodgorliklar:
- Kalta Minor: Moviy koshinlar bilan to'liq qoplangan, ammo qurilishi yakunlanmay qolgan ulkan minora.
- Juma masjidi: 212 ta qadimiy o'ymakor yog'och ustunlari bilan mashhur bo'lgan betakror bino.
- Islom Xo'ja majmuasi: Xivaning eng baland minorasi va madrasasi.
- Toshhovli saroyi: Xiva xonlarining hashamatli qarorgohi.

Ichan Qal'a 1990-yilda O'zbekistondagi birinchi ob'ekt sifatida UNESCO Butunjahon merosi ro'yxatiga kiritilgan.",
        'description_ru' => "Ичан-Кала — это обнесенный мощными стенами внутренний город Хивы, настоящий 'музей под открытым небом'. Здесь сохранился дух древнего Востока и более 50 уникальных архитектурных памятников.

Стены крепости имеют протяженность около 2 км и высоту до 10 метров. В город ведут четыре монументальных ворот, ориентированных по сторонам света. 

Знаковые памятники:
- Минарет Кальта-Минар: Полностью покрыт глазурованной плиткой бирюзового цвета, остался незавершенным, но стал символом города.
- Мечеть Джума: Знаменита своим залом с 212 резными деревянными колоннами, некоторые из которых датируются X веком.
- Дворец Таш-Хаули: Резиденция хивинских ханов с великолепными внутренними двориками и майоликовой отделкой.
- Минарет Ислам-Ходжа: Самый высокий минарет в Хиве, служивший маяком для караванов.

Ичан-Кала стала первым объектом в Узбекистане, включенным в список Всемирного наследия ЮНЕСКО (1990).",
        'description_en' => "Itchan Kala is the walled inner city of Khiva, acting as a living 'open-air museum.' It was the first site in Central Asia to be inscribed on the UNESCO World Heritage List.

The city is protected by crenelated mud-brick walls nearly 2 km long and up to 10 meters high. Access is granted through four massive gates: Ata Darvaza, Bagcha Darvaza, Palvan Darvaza, and Tash Darvaza.

Notable Monuments:
- Kalta Minor: A short but stout minaret entirely covered in exquisite turquoise tiles, left unfinished but iconic.
- Juma Mosque: A unique hypostyle mosque featuring a forest of 212 carved wooden columns, some dating back to the 10th century.
- Tash Khauli Palace: The luxurious residence of the Khiva Khans, renowned for its intricate tilework and courtyards.
- Islam Khoja Minaret: The tallest minaret in Khiva, dominating the city skyline.

Its labyrinthine streets and well-preserved architecture offer a glimpse into the glory of the Khiva Khanate.",
        'category_id' => 1,
        'region_id' => 3,
        'image' => 'itchan_kala.png',
        'status' => 'active'
    ],
    [
        'id' => 3,
        'name_uz' => 'Ark qal\'asi',
        'name_ru' => 'Крепость Арк',
        'name_en' => 'Ark of Bukhara',
        'description_uz' => "Ark — Buxoro shahrining eng qadimgi yodgorligi bo'lib, u asrlar davomida hukmdorlar qarorgohi bo'lib xizmat qilgan ulkan qal'adir. Qal'a miloddan avvalgi birinchi ming yillikka borib taqaladi va u sun'iy barpo etilgan tepalik ustida joylashgan.

Qal'aning devorlari 789 metr uzunlikka ega bo'lib, o'rta asrlarda u haqiqiy 'shahar ichidagi shahar' bo'lgan. Uning ichida amirlarning saroyi, masjidlar, zarbxona, xazina, ma'muriy binolar, otxonalar va hatto zindon ham bo'lgan.

Afsonaga ko'ra, Arkni qadimgi fors shahzodasi Siyovush barpo etgan. Qal'aga kirishda ikkita ulkan minorali darvoza va baland yo'lak (pando's) orqali o'tiladi. 1920-yildagi bombardimon natijasida qal'aning katta qismi vayron bo'lgan, ammo uning saqlanib qolgan qismlarida hozirgi kunda Buxoro davlat muzey-qo'riqxonasi ko'rgazmalari joylashgan.",
        'description_ru' => "Арк — древняя цитадель в Бухаре, на протяжении веков служившая резиденцией бухарских эмиров. Это мощное фортификационное сооружение, расположенное на искусственном холме, чья история уходит корнями в первое тысячелетие до нашей эры.

Стены крепости имеют длину 789 метров. В средние века Арк был настоящим 'городом в городе'. За его стенами находились дворцы правителей, мечети, монетный двор, казначейство, арсенал и тюрьмы.

По легенде, крепость была заложена мифическим принцем Сиявушем. Вход в Арк оформлен массивными воротами с двумя башнями, за которыми идет крутой подъем. Несмотря на то, что большая часть построек была разрушена в 1920 году, сохранившиеся здания сегодня занимают экспозиции Бухарского государственного музея-заповедника.",
        'description_en' => "The Ark of Bukhara is a massive fortress that served as the residence of the Emirs of Bukhara for centuries. It is the oldest structure in the city, with foundations dating back to the 1st millennium BC, built atop an artificial hill.

The perimeter of the fortress walls is 789 meters. In the Middle Ages, the Ark was a 'city within a city,' containing the emir's palace, mosques, a mint, government offices, a treasury, and even a prison.

Legend attributes its construction to the mythical Persian prince Siyavush. The grand entrance features twin towers and a ramp leading up to the main citadel. Although much of it was damaged during the 1920 siege, the remaining structures now house the collections of the Bukhara State Museum-Reserve.",
        'category_id' => 1,
        'region_id' => 2,
        'image' => 'ark.png',
        'status' => 'active'
    ],
    [
        'name_uz' => 'Shohi Zinda majmuasi',
        'name_ru' => 'Ансамбль Шахи-Зинда',
        'name_en' => 'Shah-i-Zinda Ensemble',
        'description_uz' => "Shohi Zinda — Samarqanddagi eng sirli va go'zal me'moriy yodgorliklardan biri. Bu majmua XI-XV asrlarda shakllangan va bir qator maqbaralardan iborat 'ko'cha' ko'rinishiga ega.

Nomi 'Shohi Zinda' — 'Tirik shoh' ma'nosini anglatadi. Bu nom Muhammad payg'ambarning amakivachchalari Qusam ibn Abbos bilan bog'liq. Afsonaga ko'ra, u dushmanlar hujumidan yer ostiga yashiringan va hanuzgacha o'sha yerda yashaydi.

Majmua o'zining ko'k, moviy va firuza rangli koshinlari bilan mashhur. Har bir maqbara o'ziga xos geometrik naqshlar, xattotlik namunalari va ranglar jilosiga ega. Shohi Zindada Amir Temurning qarindoshlari, sarkardalari va yaqinlari dafn etilgan. Bu yerda O'rta Osiyo me'morchilik san'atining barcha nozik qirralarini ko'rish mumkin.",
        'description_ru' => "Шахи-Зинда — один из самых загадочных и живописных памятников Самарканда. Этот ансамбль представляет собой улицу из великолепных мавзолеев, строившихся на протяжении XI-XV веков.

Название 'Шахи-Зинда' переводится как 'Живой царь'. Оно связано с легендой о двоюродном брате пророка Мухаммеда — Кусаме ибн Аббасе, который, согласно преданию, скрылся под землей от преследователей и живет там по сей день.

Ансамбль знаменит своей потрясающей отделкой: здесь можно увидеть лучшие образцы глазурованной керамики синих и бирюзовых оттенков. Каждый мавзолей уникален своими орнаментами и каллиграфией. Здесь похоронены родственники и приближенные Амира Тимура, что делает это место важным историческим некрополем.",
        'description_en' => "Shah-i-Zinda is a stunning necropolis in Samarkand, often described as an 'avenue of mausoleums.' Formed between the 11th and 15th centuries, it is one of the most spiritually significant and visually breathtaking sites in Uzbekistan.

The name means 'The Living King,' referring to Kusam ibn Abbas, a cousin of the Prophet Muhammad. Legend says he fled underground to escape enemies and remains alive to this day.

The complex is world-famous for its incredible blue and turquoise tilework. Each mausoleum is a masterpiece of majolica and terracotta decoration, featuring intricate geometric patterns and sacred calligraphy. It serves as the final resting place for members of Timur's family and his generals, showcasing the evolution of Timurid decorative arts.",
        'category_id' => 1,
        'region_id' => 1,
        'image' => 'shahizinda.png',
        'status' => 'active'
    ],
    [
        'name_uz' => 'Go\'ri Amir maqbarasi',
        'name_ru' => 'Мавзолей Гур-Эмир',
        'name_en' => 'Gur-e-Amir Mausoleum',
        'description_uz' => "Go'ri Amir (forscha 'Podshoh qabri') — buyuk sarkarda Amir Temur va temuriylar sulolasi vakillari dafn etilgan maqbara. Bu bino O'rta asr Sharq me'morchiligining eng buyuk asarlaridan biri hisoblanadi.

Dastlab maqbara Amir Temurning to'satdan vafot etgan nabirasi Muhammad Sulton uchun 1403-yilda qurila boshlagan. Ammo 1405-yilda Amir Temurning o'zi vafot etgach, qishki qor tufayli uning jasadi Shahrisabzga emas, aynan shu yerga dafn etilgan.

Maqbara o'zining qovurg'asimon moviy gumbazi bilan mashhur. Ichki qismi oltin suvi, loklangan papier-mashe va marmar o'ymakorligi bilan nihoyatda hashamatli bezatilgan. Amir Temurning qabr toshi dunyodagi eng yirik nefrit toshidan yasalgan. Go'ri Amir Hindistondagi mashhur Toj Mahal uchun me'moriy namuna bo'lib xizmat qilgan.",
        'description_ru' => "Гур-Эмир (в переводе — 'Гробница царя') — фамильная усыпальница великого полководца Амира Тимура и его потомков. Этот мавзолей является шедевром средневекового исламского зодчества.

Строительство началось в 1403 году по приказу Тимура для его любимого внука Мухаммада Султана. Однако после внезапной смерти самого Тимура в 1405 году, его тело было упокоено именно здесь. Позже здесь были похоронены его сыновья и внуки, включая великого ученого Улугбека.

Мавзолей знаменит своим огромным ребристым куполом ярко-синего цвета. Интерьер поражает богатством отделки: золоченая роспись, мраморные панели и резьба по ганчу. Надгробие Тимура выполнено из цельного куска темно-зеленого нефрита. Гур-Эмир послужил прообразом для таких великих памятников, как Тадж-Махал в Индии.",
        'description_en' => "Gur-e-Amir (Persian for 'Tomb of the King') is the final resting place of the conqueror Amir Timur (Tamerlane) and his heirs. It is a masterpiece of Central Asian Islamic architecture.

The project began in 1403 following the sudden death of Timur's favorite grandson, Muhammad Sultan. When Timur himself passed away in 1405, he was interred here due to heavy snow blocking the passes to his hometown. It later became the dynastic crypt for other Timurid rulers, including the astronomer Ulugh Beg.

The mausoleum is iconic for its large, fluted azure dome. The interior is sumptuously decorated with gold leaf, painted papier-mâché, and carved marble. Timur's cenotaph is carved from a single massive block of dark green jade. Gur-e-Amir is recognized as a primary architectural inspiration for the Taj Mahal in Agra.",
        'category_id' => 1,
        'region_id' => 1,
        'image' => 'gureamir.png',
        'status' => 'active'
    ],
    [
        'name_uz' => 'Poi Kalon majmuasi',
        'name_ru' => 'Ансамбль Пои-Калян',
        'name_en' => 'Po-i-Kalyan Complex',
        'description_uz' => "Poi Kalon (forscha 'Ulug' poyidagi joy') — Buxoroning markaziy diniy majmuasi. U Minorai Kalon, Masjidi Kalon va Mir Arab madrasasidan iborat.

- Minorai Kalon (1127): Buxoroning ramzi bo'lgan ushbu minora 47 metr balandlikka ega. U g'isht terish san'atining betakror namunasi bo'lib, 12 ta turli xil naqshli belbog' bilan bezatilgan. Qizig'i shundaki, Chingizxon Buxoroni vayron qilganda, minora tepasiga qarab uning salobatidan lol qolgan va uni buzmaslikka buyruq bergan.
- Masjidi Kalon: 12 ming kishini sig'dira oladigan ulkan juma masjidi.
- Mir Arab madrasasi: XVI asrda qurilgan va hanuzgacha faoliyat ko'rsatayotgan nufuzli islom ta'lim muassasasi.

Majmua asrlar davomida Buxoroning ma'naviy va ta'lim markazi bo'lib kelgan va o'zining mustahkam brick-style arxitekturasi bilan ajralib turadi.",
        'description_ru' => "Пои-Калян (в переводе — 'Подножие Великого') — центральный архитектурный ансамбль Бухары. Он включает в себя минарет Калян, одноименную мечеть и медресе Мири-Араб.

- Минарет Калян (1127): Визитная карточка Бухары. Его высота составляет 47 метров. Минарет украшен 12 полосами уникальной кирпичной кладки, которые никогда не повторяются. Легенда гласит, что даже Чингисхан был настолько поражен его величием, что приказал сохранить его во время разрушения города.
- Мечеть Калян: Одна из древнейших и крупнейших мечетей Центральной Азии, вмещающая до 12 тысяч верующих.
- Медресе Мири-Араб: Построено в XVI веке и по сей день является действующим духовным учебным заведением.

Ансамбль Пои-Калян олицетворяет величие 'Благородной Бухары' и ее многовековые традиции кирпичного зодчества.",
        'description_en' => "Po-i-Kalyan (Persian for 'At the Foot of the Great One') is the main religious complex of Bukhara. It consists of the Kalyan Minaret, the Kalyan Mosque, and the Mir-i-Arab Madrasah.

- Kalyan Minaret (1127): A masterpiece of brickwork standing 47 meters tall. It features 12 bands of unique patterns. Legend has it that Genghis Khan was so awestruck by its grandeur that he spared it from destruction while the rest of the city was razed.
- Kalyan Mosque: One of the largest Friday mosques in Central Asia, capable of holding 12,000 worshippers.
- Mir-i-Arab Madrasah: Built in the 16th century, it remains one of the most prestigious Islamic educational institutions in the region to this day.

The complex stands as a testament to the architectural and spiritual legacy of 'Bukhara the Noble.'",
        'category_id' => 1,
        'region_id' => 2,
        'image' => 'kalyan.png',
        'status' => 'active'
    ],
    [
        'name_uz' => 'Bibixonim masjidi',
        'name_ru' => 'Мечеть Биби-Ханым',
        'name_en' => 'Bibi-Khanym Mosque',
        'description_uz' => "Bibixonim masjidi — Amir Temurning buyrug'i bilan uning Hindiston yurishidan so'ng 1399-1404 yillarda qurilgan. Bu o'sha davrdagi islom dunyosining eng yirik va mahobatli juma masjidlaridan biri bo'lgan.

Afsonaga ko'ra, masjid Amir Temurning sevimli rafiqasi Bibixonim sharafiga qurilgan. Masjidning kirish peshtog'i 35 metr balandlikka ega bo'lib, u o'z davrining muhandislik cho'qqisi bo'lgan. Masjid hovlisining o'rtasida marmardan yasalgan ulkan Qur'on kursi joylashgan bo'lib, u hozirgacha saqlanib qolgan.

Garchi zilzilalar va vaqt ta'sirida bino qisman vayron bo'lgan bo'lsa-da, uning qayta tiklangan gumbazlari va ulkan peshtog'i Samarqandning qudratini namoyish etib turibdi. Moviy gumbazlarning kattaligi va koshinlarining jilvasi bu yerga kelgan har qanday sayyohni hayratda qoldiradi.",
        'description_ru' => "Мечеть Биби-Ханым была построена по приказу Амира Тимура после его победоносного похода в Индию в 1399-1404 годах. В то время она была одной из крупнейших и величественных соборных мечетей мусульманского мира.

Согласно легенде, мечеть была названа в честь любимой жены Тимура. Высота входного портала составляла 35 метров, что было пределом инженерных возможностей той эпохи. В центре двора до сих пор стоит гигантская мраморная подставка для Корана, изготовленная при Улугбеке.

Хотя землетрясения сильно повредили структуру в прошлые века, масштаб и красота восстановленных куполов и портала по-прежнему поражают воображение. Синие купола Биби-Ханым видны из многих точек Самарканда как символ его былого могущества.",
        'description_en' => "The Bibi-Khanym Mosque was commissioned by Amir Timur following his successful campaign in India between 1399 and 1404. It was designed to be one of the largest and most magnificent congregational mosques in the Islamic world at the time.

Named after Timur's favorite wife according to legend, the mosque featured an entrance portal 35 meters high, pushing the limits of medieval engineering. In the center of the courtyard stands a massive marble lectern for the Quran, placed there by Ulugh Beg.

Though partially destroyed by earthquakes over the centuries, the restored blue domes and monumental portals still showcase the grand scale of Timurid ambition. Its tiles and sheer size remain some of the most impressive sights in Samarkand.",
        'category_id' => 1,
        'region_id' => 1,
        'image' => 'bibikhanym.png',
        'status' => 'active'
    ]
];

foreach ($places as $place) {
    try {
        if (isset($place['id'])) {
            // Update existing
            $stmt = $pdo->prepare("UPDATE places SET name_uz=?, name_ru=?, name_en=?, description_uz=?, description_ru=?, description_en=?, category_id=?, region_id=?, image=?, status=? WHERE id=?");
            $stmt->execute([
                $place['name_uz'], $place['name_ru'], $place['name_en'],
                $place['description_uz'], $place['description_ru'], $place['description_en'],
                $place['category_id'], $place['region_id'], $place['image'], $place['status'], $place['id']
            ]);
            echo "Updated: " . $place['name_uz'] . "\n";
        } else {
            // Insert new
            $stmt = $pdo->prepare("INSERT INTO places (name_uz, name_ru, name_en, description_uz, description_ru, description_en, category_id, region_id, image, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $place['name_uz'], $place['name_ru'], $place['name_en'],
                $place['description_uz'], $place['description_ru'], $place['description_en'],
                $place['category_id'], $place['region_id'], $place['image'], $place['status']
            ]);
            echo "Inserted: " . $place['name_uz'] . "\n";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

echo "Seeding complete.\n";
