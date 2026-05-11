<?php
$restaurant = $restaurant ?? [];
$errors = $errors ?? [];
$regions = $regions ?? [];
?>

<input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrf()) ?>">

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nomi (UZ) *</label>
        <input type="text" name="name_uz" value="<?= htmlspecialchars($restaurant['name_uz'] ?? '') ?>" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 outline-none <?= isset($errors['name_uz']) ? 'border-red-500' : 'border-gray-300' ?>">
        <?php if (isset($errors['name_uz'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['name_uz']}</p>"; ?>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nomi (RU)</label>
        <input type="text" name="name_ru" value="<?= htmlspecialchars($restaurant['name_ru'] ?? '') ?>" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 outline-none border-gray-300">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nomi (EN)</label>
        <input type="text" name="name_en" value="<?= htmlspecialchars($restaurant['name_en'] ?? '') ?>" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 outline-none border-gray-300">
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tavsif (UZ)</label>
        <textarea name="description_uz" rows="4" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 outline-none border-gray-300"><?= htmlspecialchars($restaurant['description_uz'] ?? '') ?></textarea>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tavsif (RU)</label>
        <textarea name="description_ru" rows="4" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 outline-none border-gray-300"><?= htmlspecialchars($restaurant['description_ru'] ?? '') ?></textarea>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tavsif (EN)</label>
        <textarea name="description_en" rows="4" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 outline-none border-gray-300"><?= htmlspecialchars($restaurant['description_en'] ?? '') ?></textarea>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Oshxona turi</label>
        <input type="text" name="cuisine_type" value="<?= htmlspecialchars($restaurant['cuisine_type'] ?? '') ?>" placeholder="Masalan: O'zbek milliy" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 outline-none border-gray-300">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Narx darajasi</label>
        <select name="price_range" class="w-full px-4 py-2 bg-white border rounded-lg focus:ring-blue-500 outline-none border-gray-300">
            <option value="low" <?= ($restaurant['price_range'] ?? '') === 'low' ? 'selected' : '' ?>>Arzon ($)</option>
            <option value="mid" <?= ($restaurant['price_range'] ?? '') === 'mid' ? 'selected' : '' ?>>O'rtacha ($$)</option>
            <option value="high" <?= ($restaurant['price_range'] ?? '') === 'high' ? 'selected' : '' ?>>Qimmat ($$$)</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($restaurant['phone'] ?? '') ?>" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 outline-none border-gray-300">
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Ish vaqti</label>
        <input type="text" name="working_hours" value="<?= htmlspecialchars($restaurant['working_hours'] ?? '') ?>" placeholder="08:00 - 23:00" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 outline-none border-gray-300">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Viloyat</label>
        <select name="region_id" class="w-full px-4 py-2 bg-white border rounded-lg focus:ring-blue-500 outline-none border-gray-300">
            <option value="">-- Tanlang --</option>
            <?php foreach ($regions as $reg): ?>
                <option value="<?= $reg['id'] ?>" <?= ($restaurant['region_id'] ?? '') == $reg['id'] ? 'selected' : '' ?>><?= htmlspecialchars($reg['name_uz']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<div class="mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-1">Manzil</label>
    <input type="text" name="address" value="<?= htmlspecialchars($restaurant['address'] ?? '') ?>" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 outline-none border-gray-300">
</div>

<div class="mb-6">
    <label for="map_link" class="block text-sm font-medium text-gray-700 mb-1">Google Maps Havolasi yoki Koordinatalar</label>
    <?php 
    $mapVal = '';
    if (isset($_POST['map_link'])) {
        $mapVal = $_POST['map_link'];
    } elseif (!empty($restaurant['latitude']) && !empty($restaurant['longitude'])) {
        $mapVal = $restaurant['latitude'] . ', ' . $restaurant['longitude'];
    }
    ?>
    <input type="text" id="map_link" name="map_link" value="<?= htmlspecialchars($mapVal) ?>" placeholder="Misol: https://maps.app.goo.gl/... yoki 41.2995, 69.2401"
           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors <?= isset($errors['map_link']) ? 'border-red-500' : 'border-gray-300' ?>">
    <p class="text-xs text-gray-500 mt-1">Google xaritadan "Share" -> "Copy link" qilib tashlang yoki "lat, lng" ni kiriting.</p>
    <?php if (isset($errors['map_link'])): ?>
        <p class="text-red-500 text-xs mt-1"><?= $errors['map_link'] ?></p>
    <?php endif; ?>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Rasm <?= !empty($restaurant['image']) ? '(Yangi yuklash eskisini almashtiradi)' : '*' ?></label>
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 outline-none <?= isset($errors['image']) ? 'border-red-500' : 'border-gray-300' ?> file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:bg-blue-50 file:text-blue-700">
        <p class="text-xs text-gray-500 mt-1">JPG, PNG, WEBP, GIF. Maks: 5MB.</p>
        <?php if (isset($errors['image'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['image']}</p>"; ?>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Holati</label>
        <div class="flex items-center space-x-6 mt-2">
            <label class="inline-flex items-center"><input type="radio" name="status" value="active" <?= ($restaurant['status'] ?? 'draft') === 'active' ? 'checked' : '' ?> class="w-4 h-4 text-blue-600"> <span class="ml-2">Faol</span></label>
            <label class="inline-flex items-center"><input type="radio" name="status" value="draft" <?= ($restaurant['status'] ?? 'draft') === 'draft' ? 'checked' : '' ?> class="w-4 h-4 text-blue-600"> <span class="ml-2">Qoralama</span></label>
        </div>
    </div>
</div>
