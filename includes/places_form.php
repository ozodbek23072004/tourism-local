<?php
// includes/places_form.php

$place = $place ?? [];
$errors = $errors ?? [];
$categories = $categories ?? [];
$regions = $regions ?? [];
?>

<input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrf()) ?>">

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- Nomi (UZ) -->
    <div>
        <label for="name_uz" class="block text-sm font-medium text-gray-700 mb-1">Nomi (UZ) *</label>
        <input type="text" id="name_uz" name="name_uz" value="<?= htmlspecialchars($place['name_uz'] ?? '') ?>" 
               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors <?= isset($errors['name_uz']) ? 'border-red-500' : 'border-gray-300' ?>">
        <?php if (isset($errors['name_uz'])): ?>
            <p class="text-red-500 text-xs mt-1"><?= $errors['name_uz'] ?></p>
        <?php endif; ?>
    </div>

    <!-- Nomi (RU) -->
    <div>
        <label for="name_ru" class="block text-sm font-medium text-gray-700 mb-1">Nomi (RU) *</label>
        <input type="text" id="name_ru" name="name_ru" value="<?= htmlspecialchars($place['name_ru'] ?? '') ?>" 
               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors <?= isset($errors['name_ru']) ? 'border-red-500' : 'border-gray-300' ?>">
        <?php if (isset($errors['name_ru'])): ?>
            <p class="text-red-500 text-xs mt-1"><?= $errors['name_ru'] ?></p>
        <?php endif; ?>
    </div>

    <!-- Nomi (EN) -->
    <div>
        <label for="name_en" class="block text-sm font-medium text-gray-700 mb-1">Nomi (EN) *</label>
        <input type="text" id="name_en" name="name_en" value="<?= htmlspecialchars($place['name_en'] ?? '') ?>" 
               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors <?= isset($errors['name_en']) ? 'border-red-500' : 'border-gray-300' ?>">
        <?php if (isset($errors['name_en'])): ?>
            <p class="text-red-500 text-xs mt-1"><?= $errors['name_en'] ?></p>
        <?php endif; ?>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <!-- Category -->
    <div>
        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Kategoriya</label>
        <select id="category_id" name="category_id" class="w-full px-4 py-2 bg-white border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors <?= isset($errors['category_id']) ? 'border-red-500' : 'border-gray-300' ?>">
            <option value="">-- Tanlang --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= ($place['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name_uz']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['category_id'])): ?>
            <p class="text-red-500 text-xs mt-1"><?= $errors['category_id'] ?></p>
        <?php endif; ?>
    </div>

    <!-- Region -->
    <div>
        <label for="region_id" class="block text-sm font-medium text-gray-700 mb-1">Viloyat</label>
        <select id="region_id" name="region_id" class="w-full px-4 py-2 bg-white border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors <?= isset($errors['region_id']) ? 'border-red-500' : 'border-gray-300' ?>">
            <option value="">-- Tanlang --</option>
            <?php foreach ($regions as $reg): ?>
                <option value="<?= $reg['id'] ?>" <?= ($place['region_id'] ?? '') == $reg['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($reg['name_uz']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['region_id'])): ?>
            <p class="text-red-500 text-xs mt-1"><?= $errors['region_id'] ?></p>
        <?php endif; ?>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- Description (UZ) -->
    <div>
        <label for="description_uz" class="block text-sm font-medium text-gray-700 mb-1">Tavsif (UZ)</label>
        <textarea id="description_uz" name="description_uz" rows="4" 
                  class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors <?= isset($errors['description_uz']) ? 'border-red-500' : 'border-gray-300' ?>"><?= htmlspecialchars($place['description_uz'] ?? '') ?></textarea>
        <?php if (isset($errors['description_uz'])): ?>
            <p class="text-red-500 text-xs mt-1"><?= $errors['description_uz'] ?></p>
        <?php endif; ?>
    </div>

    <!-- Description (RU) -->
    <div>
        <label for="description_ru" class="block text-sm font-medium text-gray-700 mb-1">Tavsif (RU)</label>
        <textarea id="description_ru" name="description_ru" rows="4" 
                  class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors <?= isset($errors['description_ru']) ? 'border-red-500' : 'border-gray-300' ?>"><?= htmlspecialchars($place['description_ru'] ?? '') ?></textarea>
        <?php if (isset($errors['description_ru'])): ?>
            <p class="text-red-500 text-xs mt-1"><?= $errors['description_ru'] ?></p>
        <?php endif; ?>
    </div>

    <!-- Description (EN) -->
    <div>
        <label for="description_en" class="block text-sm font-medium text-gray-700 mb-1">Tavsif (EN)</label>
        <textarea id="description_en" name="description_en" rows="4" 
                  class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors <?= isset($errors['description_en']) ? 'border-red-500' : 'border-gray-300' ?>"><?= htmlspecialchars($place['description_en'] ?? '') ?></textarea>
        <?php if (isset($errors['description_en'])): ?>
            <p class="text-red-500 text-xs mt-1"><?= $errors['description_en'] ?></p>
        <?php endif; ?>
    </div>
</div>

<div class="mb-6">
    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Manzil</label>
    <input type="text" id="address" name="address" value="<?= htmlspecialchars($place['address'] ?? '') ?>" 
           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors <?= isset($errors['address']) ? 'border-red-500' : 'border-gray-300' ?>">
    <?php if (isset($errors['address'])): ?>
        <p class="text-red-500 text-xs mt-1"><?= $errors['address'] ?></p>
    <?php endif; ?>
</div>

<div class="mb-6">
    <label for="map_link" class="block text-sm font-medium text-gray-700 mb-1">Google Maps Havolasi yoki Koordinatalar</label>
    <?php 
    $mapVal = '';
    if (isset($_POST['map_link'])) {
        $mapVal = $_POST['map_link'];
    } elseif (!empty($place['latitude']) && !empty($place['longitude'])) {
        $mapVal = $place['latitude'] . ', ' . $place['longitude'];
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
    <!-- Image -->
    <div>
        <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Rasm <?= !empty($place['image']) ? '(Yangi yuklash eskisini almashtiradi)' : '*' ?></label>
        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp,image/gif" 
               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors bg-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 <?= isset($errors['image']) ? 'border-red-500' : 'border-gray-300' ?>">
        <p class="text-xs text-gray-500 mt-1">Formatlar: JPG, PNG, WEBP, GIF. Maks: 5MB.</p>
        <?php if (isset($errors['image'])): ?>
            <p class="text-red-500 text-xs mt-1"><?= $errors['image'] ?></p>
        <?php endif; ?>
    </div>

    <!-- Status -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Holati</label>
        <div class="flex items-center space-x-6 mt-2">
            <label class="inline-flex items-center">
                <input type="radio" name="status" value="active" <?= ($place['status'] ?? 'draft') === 'active' ? 'checked' : '' ?> 
                       class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                <span class="ml-2 text-gray-700">Faol (Active)</span>
            </label>
            <label class="inline-flex items-center">
                <input type="radio" name="status" value="draft" <?= ($place['status'] ?? 'draft') === 'draft' ? 'checked' : '' ?> 
                       class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                <span class="ml-2 text-gray-700">Qoralama (Draft)</span>
            </label>
        </div>
        <?php if (isset($errors['status'])): ?>
            <p class="text-red-500 text-xs mt-1"><?= $errors['status'] ?></p>
        <?php endif; ?>
    </div>
</div>
