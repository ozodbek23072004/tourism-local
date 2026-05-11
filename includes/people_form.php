<?php
$person = $person ?? [];
$errors = $errors ?? [];
$regions = $regions ?? [];
?>

<input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrf()) ?>">

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nomi (UZ) *</label>
        <input type="text" name="name_uz" value="<?= htmlspecialchars($person['name_uz'] ?? '') ?>" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 outline-none <?= isset($errors['name_uz']) ? 'border-red-500' : 'border-gray-300' ?>">
        <?php if (isset($errors['name_uz'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['name_uz']}</p>"; ?>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nomi (RU) *</label>
        <input type="text" name="name_ru" value="<?= htmlspecialchars($person['name_ru'] ?? '') ?>" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 outline-none <?= isset($errors['name_ru']) ? 'border-red-500' : 'border-gray-300' ?>">
        <?php if (isset($errors['name_ru'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['name_ru']}</p>"; ?>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nomi (EN) *</label>
        <input type="text" name="name_en" value="<?= htmlspecialchars($person['name_en'] ?? '') ?>" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 outline-none <?= isset($errors['name_en']) ? 'border-red-500' : 'border-gray-300' ?>">
        <?php if (isset($errors['name_en'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['name_en']}</p>"; ?>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Biografiya (UZ)</label>
        <textarea name="bio_uz" rows="4" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 outline-none border-gray-300"><?= htmlspecialchars($person['bio_uz'] ?? '') ?></textarea>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Biografiya (RU)</label>
        <textarea name="bio_ru" rows="4" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 outline-none border-gray-300"><?= htmlspecialchars($person['bio_ru'] ?? '') ?></textarea>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tug'ilgan yili *</label>
        <input type="number" name="born_year" value="<?= htmlspecialchars($person['born_year'] ?? '') ?>" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 outline-none <?= isset($errors['born_year']) ? 'border-red-500' : 'border-gray-300' ?>">
        <?php if (isset($errors['born_year'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['born_year']}</p>"; ?>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Vafot etgan yili</label>
        <input type="number" name="died_year" value="<?= htmlspecialchars($person['died_year'] ?? '') ?>" placeholder="Tirik bo'lsa bo'sh qoldiring" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 outline-none border-gray-300">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Viloyat</label>
        <select name="region_id" class="w-full px-4 py-2 bg-white border rounded-lg focus:ring-blue-500 outline-none border-gray-300">
            <option value="">-- Tanlang --</option>
            <?php foreach ($regions as $reg): ?>
                <option value="<?= $reg['id'] ?>" <?= ($person['region_id'] ?? '') == $reg['id'] ? 'selected' : '' ?>><?= htmlspecialchars($reg['name_uz']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>



<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Rasm <?= !empty($person['image']) ? '(Yangi yuklash eskisini almashtiradi)' : '*' ?></label>
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 outline-none <?= isset($errors['image']) ? 'border-red-500' : 'border-gray-300' ?> file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:bg-blue-50 file:text-blue-700">
        <p class="text-xs text-gray-500 mt-1">JPG, PNG, WEBP, GIF. Maks: 5MB.</p>
        <?php if (isset($errors['image'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['image']}</p>"; ?>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Holati</label>
        <div class="flex items-center space-x-6 mt-2">
            <label class="inline-flex items-center"><input type="radio" name="status" value="active" <?= ($person['status'] ?? 'draft') === 'active' ? 'checked' : '' ?> class="w-4 h-4 text-blue-600"> <span class="ml-2">Faol</span></label>
            <label class="inline-flex items-center"><input type="radio" name="status" value="draft" <?= ($person['status'] ?? 'draft') === 'draft' ? 'checked' : '' ?> class="w-4 h-4 text-blue-600"> <span class="ml-2">Qoralama</span></label>
        </div>
    </div>
</div>
