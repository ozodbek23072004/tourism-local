<?php
$hotel = $hotel ?? [];
$errors = $errors ?? [];
$regions = $regions ?? [];
?>

<input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrf()) ?>">

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nomi (UZ) *</label>
        <input type="text" name="name_uz" value="<?= htmlspecialchars($hotel['name_uz'] ?? '') ?>" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 outline-none <?= isset($errors['name_uz']) ? 'border-red-500' : 'border-gray-300' ?>">
        <?php if (isset($errors['name_uz'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['name_uz']}</p>"; ?>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nomi (RU)</label>
        <input type="text" name="name_ru" value="<?= htmlspecialchars($hotel['name_ru'] ?? '') ?>" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 outline-none border-gray-300">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nomi (EN)</label>
        <input type="text" name="name_en" value="<?= htmlspecialchars($hotel['name_en'] ?? '') ?>" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 outline-none border-gray-300">
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tavsif (UZ)</label>
        <textarea name="description_uz" rows="4" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 outline-none border-gray-300"><?= htmlspecialchars($hotel['description_uz'] ?? '') ?></textarea>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tavsif (RU)</label>
        <textarea name="description_ru" rows="4" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 outline-none border-gray-300"><?= htmlspecialchars($hotel['description_ru'] ?? '') ?></textarea>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tavsif (EN)</label>
        <textarea name="description_en" rows="4" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 outline-none border-gray-300"><?= htmlspecialchars($hotel['description_en'] ?? '') ?></textarea>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Yulduzlar (1-5)</label>
        <select name="stars" class="w-full px-4 py-2 bg-white border rounded-lg focus:ring-blue-500 outline-none border-gray-300">
            <?php for($i=1; $i<=5; $i++): ?>
                <option value="<?= $i ?>" <?= ($hotel['stars'] ?? '1') == $i ? 'selected' : '' ?>><?= str_repeat('★', $i) ?></option>
            <?php endfor; ?>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Boshlang'ich narx</label>
        <input type="text" name="price_from" value="<?= htmlspecialchars($hotel['price_from'] ?? '') ?>" placeholder="Misol: 50.00" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 outline-none <?= isset($errors['price_from']) ? 'border-red-500' : 'border-gray-300' ?>">
        <?php if (isset($errors['price_from'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['price_from']}</p>"; ?>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($hotel['phone'] ?? '') ?>" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 outline-none border-gray-300">
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Viloyat</label>
        <select name="region_id" class="w-full px-4 py-2 bg-white border rounded-lg focus:ring-blue-500 outline-none border-gray-300">
            <option value="">-- Tanlang --</option>
            <?php foreach ($regions as $reg): ?>
                <option value="<?= $reg['id'] ?>" <?= ($hotel['region_id'] ?? '') == $reg['id'] ? 'selected' : '' ?>><?= htmlspecialchars($reg['name_uz']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Manzil</label>
        <input type="text" name="address" value="<?= htmlspecialchars($hotel['address'] ?? '') ?>" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 outline-none border-gray-300">
    </div>
</div>

<div class="mb-6">
    <label for="map_link" class="block text-sm font-medium text-gray-700 mb-1">Google Maps Havolasi yoki Koordinatalar</label>
    <?php 
    $mapVal = '';
    if (isset($_POST['map_link'])) {
        $mapVal = $_POST['map_link'];
    } elseif (!empty($hotel['latitude']) && !empty($hotel['longitude'])) {
        $mapVal = $hotel['latitude'] . ', ' . $hotel['longitude'];
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
        <label class="block text-sm font-medium text-gray-700 mb-1">Rasm <?= !empty($hotel['image']) ? '(Yangi yuklash eskisini almashtiradi)' : '*' ?></label>
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 outline-none <?= isset($errors['image']) ? 'border-red-500' : 'border-gray-300' ?> file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:bg-blue-50 file:text-blue-700">
        <p class="text-xs text-gray-500 mt-1">JPG, PNG, WEBP, GIF. Maks: 5MB.</p>
        <?php if (isset($errors['image'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['image']}</p>"; ?>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Holati</label>
        <div class="flex items-center space-x-6 mt-2">
            <label class="inline-flex items-center"><input type="radio" name="status" value="active" <?= ($hotel['status'] ?? 'draft') === 'active' ? 'checked' : '' ?> class="w-4 h-4 text-blue-600"> <span class="ml-2">Faol</span></label>
            <label class="inline-flex items-center"><input type="radio" name="status" value="draft" <?= ($hotel['status'] ?? 'draft') === 'draft' ? 'checked' : '' ?> class="w-4 h-4 text-blue-600"> <span class="ml-2">Qoralama</span></label>
        </div>
    </div>
</div>
