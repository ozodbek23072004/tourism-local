<?php
require_once '../../includes/config.php';
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireAuth();

$errors = [];
$person = [
    'name_uz' => '', 'name_ru' => '', 'name_en' => '',
    'bio_uz' => '', 'bio_ru' => '',
    'born_year' => '', 'died_year' => '', 'region_id' => '',
    'status' => 'active'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $person['name_uz'] = sanitize($_POST['name_uz'] ?? '');
    $person['name_ru'] = sanitize($_POST['name_ru'] ?? '');
    $person['name_en'] = sanitize($_POST['name_en'] ?? '');
    $person['bio_uz'] = sanitize($_POST['bio_uz'] ?? '');
    $person['bio_ru'] = sanitize($_POST['bio_ru'] ?? '');
    $person['born_year'] = sanitize($_POST['born_year'] ?? '');
    $person['died_year'] = sanitize($_POST['died_year'] ?? '');
    $person['region_id'] = sanitize($_POST['region_id'] ?? '');
    $person['status'] = in_array($_POST['status'] ?? '', ['active', 'draft']) ? $_POST['status'] : 'draft';

    verifyCsrf();
    $rules = [
        'name_uz' => 'required|max:255',
        'name_ru' => 'required|max:255',
        'name_en' => 'required|max:255',
        'born_year' => 'required|numeric|min:0'
    ];
    $errors = validate($rules, $person);

    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload = uploadImage('image', 'people');
        if ($upload) $imagePath = $upload;
        else $errors['image'] = 'Xatolik (Format yoki hajm)';
    } else {
        $errors['image'] = 'Majburiy maydon';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO people (name_uz, name_ru, name_en, bio_uz, bio_ru, born_year, died_year, region_id, video_url, image, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $person['name_uz'], $person['name_ru'], $person['name_en'],
            $person['bio_uz'], $person['bio_ru'], $person['born_year'],
            $person['died_year'] !== '' ? $person['died_year'] : null,
            $person['region_id'] !== '' ? $person['region_id'] : null,
            sanitize($_POST['video_url'] ?? ''),
            $imagePath, $person['status']
        ]);
        flashMessage('success', "Qo'shildi!");
        redirect('index.php');
    }
}
$stmtReg = $pdo->prepare("SELECT id, name_uz FROM regions ORDER BY name_uz"); $stmtReg->execute(); $regions = $stmtReg->fetchAll();
$pageTitle = "Shaxs qo'shish";
require_once '../../includes/layout_header.php';
?>
<div class="bg-white rounded-xl shadow p-6"><form action="" method="POST" enctype="multipart/form-data">
    <?php require_once '../../includes/people_form.php'; ?>
    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded">Saqlash</button>
</form></div>
<?php require_once '../../includes/layout_footer.php'; ?>
