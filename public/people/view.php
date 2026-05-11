<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../includes/functions.php';

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: index.php"); exit; }

$stmt = $pdo->prepare("SELECT p.*, r.name_uz as region_name FROM people p LEFT JOIN regions r ON p.region_id = r.id WHERE p.id = :id AND p.status = 'active'");
$stmt->execute(['id' => $id]);
$person = $stmt->fetch();

if (!$person) { http_response_code(404); require_once '../404.php'; exit; }

try { $pdo->prepare("UPDATE people SET views = views + 1 WHERE id = :id")->execute(['id' => $id]); } catch (PDOException $e) {}

// Related people (Same era: +/- 100 years from born_year)
$born_year = (int)$person['born_year'];
$stmtRelated = $pdo->prepare("
    SELECT id, name_uz, name_ru, name_en, image, born_year, died_year 
    FROM people 
    WHERE id != :id AND status = 'active' AND born_year BETWEEN :y_start AND :y_end
    ORDER BY born_year ASC LIMIT 6
");
$stmtRelated->execute(['id' => $person['id'], 'y_start' => $born_year - 100, 'y_end' => $born_year + 100]);
$relatedPeople = $stmtRelated->fetchAll();

$lang = currentLang();
$personName = localizedField($person, 'name');
$personBio = localizedField($person, 'bio');

// Extract a quote from bio if exists (looking for text inside quotes)
$quote = '';
if (preg_match('/[«"]([^»"]{20,150})[»"]/', $personBio, $matches)) {
    $quote = $matches[1];
}

$pageTitle = htmlspecialchars($personName);
require_once '../includes/seo.php';
renderMeta(['title' => $pageTitle . ' | ' . __('nav_people')]);
require_once '../includes/layout_header.php';
$currentUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
?>

<style>
/* Arabesque pattern */
.bg-arabesque {
    background-image: url('data:image/svg+xml;utf8,<svg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg"><path d="M20 0l20 20-20 20L0 20z" fill="rgba(201,146,42,0.05)" fill-rule="evenodd"/></svg>');
}
.gold-frame {
    position: relative;
}
.gold-frame::before {
    content: '';
    position: absolute;
    inset: -10px;
    border: 1px solid rgba(201,146,42,0.4);
    z-index: 0;
    pointer-events: none;
}
.gold-frame::after {
    content: '';
    position: absolute;
    inset: -20px;
    border: 1px solid rgba(201,146,42,0.15);
    z-index: 0;
    pointer-events: none;
}

/* Timeline */
.timeline-node {
    position: relative;
    padding-left: 2.5rem;
    padding-bottom: 2.5rem;
}
.timeline-node::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 2px;
    background: rgba(201,146,42,0.4);
}
.timeline-node:last-child::before {
    bottom: auto;
    height: 100%;
}
.timeline-node::after {
    content: '';
    position: absolute;
    left: -4px;
    top: 0;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #C9922A;
    box-shadow: 0 0 0 4px rgba(201,146,42,0.2);
}

.horizontal-scroll {
    display: flex;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    gap: 1.5rem;
    padding-bottom: 2rem;
}
.horizontal-scroll::-webkit-scrollbar {
    height: 6px;
}
.horizontal-scroll::-webkit-scrollbar-track {
    background: #f1f1f1; 
    border-radius: 10px;
}
.horizontal-scroll::-webkit-scrollbar-thumb {
    background: #C9922A; 
    border-radius: 10px;
}
.scroll-card {
    scroll-snap-align: start;
    min-width: 260px;
}
.person-card.no-image .card-image-area {
  background: linear-gradient(135deg, #2C1A0E 0%, #1A0E05 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}
.person-card.no-image .card-image-area::after {
  content: attr(data-initial);
  font-family: 'Playfair Display', serif;
  font-size: 80px;
  color: rgba(201, 146, 42, 0.6);
}
</style>

<!-- HERO SPLIT LAYOUT -->
<section class="min-h-screen pt-24 bg-white flex flex-col lg:flex-row">
    <!-- LEFT: Portrait Image -->
    <div class="w-full lg:w-5/12 bg-[#1A0E05] relative flex items-center justify-center p-12 lg:p-20 overflow-hidden min-h-[60vh] lg:min-h-screen">
        <div class="absolute inset-0 bg-arabesque opacity-30"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-[#1A0E05] via-transparent to-transparent z-10"></div>
        
        <div class="gold-frame relative z-20 w-full max-w-sm" data-aos="fade-in" data-aos-duration="1500">
            <img src="<?= publicImage($person['image'], $personName) ?>" 
                 alt="<?= htmlspecialchars($personName) ?>" 
                 class="w-full aspect-[3/4] object-cover shadow-2xl relative z-10"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
            <!-- Fallback -->
            <div class="hidden absolute inset-0 bg-[#2C1A0E] flex-col items-center justify-center z-10 text-[#C9922A]">
                <span class="font-display text-8xl"><?= mb_strtoupper(mb_substr($personName, 0, 1)) ?></span>
            </div>
        </div>
    </div>

    <!-- RIGHT: Info & Timeline -->
    <div class="w-full lg:w-7/12 p-8 lg:p-20 bg-silk-50 relative">
        <div class="max-w-2xl mx-auto h-full flex flex-col justify-center">
            
            <div class="flex items-center gap-2 text-amber-600/70 text-xs mb-8 uppercase tracking-widest font-bold" data-aos="fade-up">
                <a href="<?= BASE_URL ?>public/index.php" class="hover:text-amber-600 transition-colors"><?= __('home') ?></a>
                <span>/</span>
                <a href="index.php" class="hover:text-amber-600 transition-colors"><?= __('nav_people') ?></a>
            </div>

            <div data-aos="fade-up" data-aos-delay="100">
                <h1 class="font-display text-5xl md:text-6xl font-bold text-[#1A0E05] mb-4 leading-tight">
                    <?= htmlspecialchars($personName) ?>
                </h1>
                <p class="text-xl text-[#C9922A] font-semibold tracking-wide mb-8">
                    <?= $person['born_year'] ?> — <?= $person['died_year'] ?: __('present') ?>
                </p>
            </div>

            <div class="prose prose-lg text-silk-700 leading-relaxed mb-16" data-aos="fade-up" data-aos-delay="200">
                <p><?= nl2br(htmlspecialchars(mb_substr(strip_tags($personBio), 0, 600))) ?>...</p>
            </div>

            <!-- Achievement Timeline -->
            <div class="mt-8" data-aos="fade-up" data-aos-delay="300">
                <h3 class="font-display text-2xl font-bold text-[#1A0E05] mb-8">Hayot yo'li va merosi</h3>
                
                <div class="timeline-container">
                    <div class="timeline-node">
                        <div class="text-sm font-bold text-[#C9922A] mb-1"><?= $person['born_year'] ?></div>
                        <h4 class="font-bold text-[#1A0E05] text-lg">Tavallud</h4>
                        <p class="text-sm text-silk-600 mt-2">
                            <?= htmlspecialchars($person['region_name'] ?? 'Movarounnahr') ?> hududida dunyoga kelgan.
                        </p>
                    </div>

                    <div class="timeline-node">
                        <div class="text-sm font-bold text-[#C9922A] mb-1">Faoliyat davri</div>
                        <h4 class="font-bold text-[#1A0E05] text-lg">Asosiy yutuqlari</h4>
                        <p class="text-sm text-silk-600 mt-2">
                            Mintaqa va jahon sivilizatsiyasi rivojiga ulkan hissa qo'shgan. Buyuk asarlar yozgan va tarixda chuqur iz qoldirgan.
                        </p>
                    </div>

                    <?php if($person['died_year']): ?>
                    <div class="timeline-node">
                        <div class="text-sm font-bold text-[#C9922A] mb-1"><?= $person['died_year'] ?></div>
                        <h4 class="font-bold text-[#1A0E05] text-lg">Vafoti</h4>
                        <p class="text-sm text-silk-600 mt-2">
                            O'zidan o'chmas meros qoldirib, vafot etgan. Uning asarlari hozirgi kungacha o'rganilib kelinmoqda.
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- QUOTE BLOCK -->
<?php if ($quote): ?>
<section class="py-24 bg-[#1A0E05] relative overflow-hidden flex items-center justify-center text-center">
    <div class="absolute inset-0 bg-arabesque opacity-10"></div>
    <div class="relative z-10 max-w-4xl mx-auto px-6">
        <span class="font-display text-[#C9922A] opacity-20 text-[8rem] leading-none absolute -top-12 left-1/2 -translate-x-1/2 pointer-events-none">"</span>
        <blockquote class="font-display text-2xl md:text-4xl italic text-white leading-snug relative z-10">
            <?= htmlspecialchars($quote) ?>
        </blockquote>
        <div class="h-[1px] w-24 bg-[#C9922A] mx-auto mt-8"></div>
        <p class="text-[#C9922A] font-semibold mt-4 tracking-widest uppercase text-sm">— <?= htmlspecialchars($personName) ?></p>
    </div>
</section>
<?php endif; ?>

<!-- FULL BIO DETAILS (Optional if bio is long) -->
<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-6 lg:px-8">
        <div x-data="{ tab: '<?= $lang ?>' }">
            <div class="mb-8 border-b border-silk-200 flex gap-6 justify-center">
                <button @click="tab='uz'" :class="tab==='uz' ? 'border-[#C9922A] text-[#1A0E05]' : 'border-transparent text-silk-400 hover:text-silk-600'" class="pb-3 border-b-2 font-bold uppercase tracking-wider text-sm transition-all"><?= __('lang_uz') ?></button>
                <?php if(!empty($person['bio_ru'])): ?>
                <button @click="tab='ru'" :class="tab==='ru' ? 'border-[#C9922A] text-[#1A0E05]' : 'border-transparent text-silk-400 hover:text-silk-600'" class="pb-3 border-b-2 font-bold uppercase tracking-wider text-sm transition-all"><?= __('lang_ru') ?></button>
                <?php endif; ?>
                <?php if(!empty($person['bio_en'])): ?>
                <button @click="tab='en'" :class="tab==='en' ? 'border-[#C9922A] text-[#1A0E05]' : 'border-transparent text-silk-400 hover:text-silk-600'" class="pb-3 border-b-2 font-bold uppercase tracking-wider text-sm transition-all"><?= __('lang_en') ?></button>
                <?php endif; ?>
            </div>
            <article class="prose prose-lg max-w-none text-silk-700 leading-loose mx-auto">
                <div x-show="tab==='uz'"><?= nl2br(htmlspecialchars($person['bio_uz'] ?? __('no_info'))) ?></div>
                <?php if(!empty($person['bio_ru'])): ?>
                <div x-show="tab==='ru'" style="display:none"><?= nl2br(htmlspecialchars($person['bio_ru'])) ?></div>
                <?php endif; ?>
                <?php if(!empty($person['bio_en'])): ?>
                <div x-show="tab==='en'" style="display:none"><?= nl2br(htmlspecialchars($person['bio_en'])) ?></div>
                <?php endif; ?>
            </article>
        </div>
    </div>
</section>


<!-- RELATED PEOPLE -->
<?php if (count($relatedPeople) > 0): ?>
<section class="py-20 bg-silk-50 border-t border-silk-200/60 overflow-hidden">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex items-center justify-between mb-10" data-aos="fade-up">
            <h2 class="font-display text-3xl font-bold text-[#1A0E05]">Shu davrda yashagan</h2>
        </div>
        
        <div class="horizontal-scroll pb-6" data-aos="fade-up" data-aos-delay="100">
            <?php foreach($relatedPeople as $rel): 
                $relName = localizedField($rel, 'name');
            ?>
            <a href="view.php?id=<?= $rel['id'] ?>" class="group scroll-card block person-card">
                <div class="relative w-[260px] h-[350px] rounded-xl overflow-hidden bg-[#1A0E05] border border-[#C9922A]/20 group-hover:border-[#C9922A]/60 transition-all duration-500">
                    <div class="card-image-area absolute inset-x-0 top-0 w-full h-full" data-initial="<?= mb_strtoupper(mb_substr($relName, 0, 1)) ?>">
                        <img loading="lazy" src="<?= publicImage($rel['image'], $relName) ?>" alt="<?= htmlspecialchars($relName) ?>" 
                             class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-700"
                             onerror="this.onerror=null; this.style.display='none'; this.closest('.person-card').classList.add('no-image')">
                    </div>

                    <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-[#1A0E05] via-[#1A0E05]/80 to-transparent"></div>
                    <div class="absolute inset-x-0 bottom-0 p-5">
                        <h3 class="font-display font-bold text-white text-lg mb-1"><?= htmlspecialchars($relName) ?></h3>
                        <p class="text-[#C9922A] text-xs font-semibold"><?= $rel['born_year'] ?> — <?= $rel['died_year'] ?: __('present') ?></p>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once '../includes/layout_footer.php'; ?>
