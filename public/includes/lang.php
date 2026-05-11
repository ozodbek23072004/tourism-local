<?php
/**
 * Centralized Language System
 * Usage: __('nav_places') => "Joylar" (or Russian/English equivalent)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Language switch via GET parameter
if (isset($_GET['lang']) && in_array($_GET['lang'], ['uz', 'ru', 'en'])) {
    $_SESSION['lang'] = $_GET['lang'];
    // Remove lang param and redirect to clean URL
    $url = strtok($_SERVER['REQUEST_URI'], '?');
    $params = $_GET;
    unset($params['lang']);
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    header("Location: $url");
    exit;
}

// Determine current language
$currentLang = $_SESSION['lang'] ?? 'uz';
$GLOBALS['current_lang'] = $currentLang;

// Load translations
$langFile = __DIR__ . '/lang/' . $currentLang . '.php';
$GLOBALS['translations'] = file_exists($langFile) ? require $langFile : require __DIR__ . '/lang/uz.php';

/**
 * Translate a key
 */
function __(?string $key, string $fallback = ''): string {
    return $GLOBALS['translations'][$key] ?? $fallback ?: $key;
}

/**
 * Get current language code
 */
function currentLang(): string {
    return $GLOBALS['current_lang'] ?? 'uz';
}

/**
 * Get localized field name suffix
 * E.g., for 'uz' returns '_uz', used to build field names like 'name_uz', 'description_uz'
 */
function langSuffix(): string {
    return '_' . currentLang();
}

/**
 * Get localized value from a row
 * Tries current language, falls back to uz, then any non-empty
 */
function localizedField(array $row, string $baseField): string {
    $lang = currentLang();
    $fieldLang = $baseField . '_' . $lang;
    $fieldUz = $baseField . '_uz';
    
    if (!empty($row[$fieldLang])) return $row[$fieldLang];
    if (!empty($row[$fieldUz])) return $row[$fieldUz];
    // fallback: try the base field without suffix (e.g., restaurants.name)
    if (!empty($row[$baseField])) return $row[$baseField];
    return '';
}

/**
 * Build language switcher URL
 */
function langSwitchUrl(string $targetLang): string {
    $params = $_GET;
    $params['lang'] = $targetLang;
    $url = strtok($_SERVER['REQUEST_URI'], '?');
    return $url . '?' . http_build_query($params);
}
