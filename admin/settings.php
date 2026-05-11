<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAuth();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $groq_api_key = $_POST['groq_api_key'] ?? '';
    $groq_model = $_POST['groq_model'] ?? 'llama3-70b-8192';

    try {
        $stmt = $pdo->prepare("REPLACE INTO settings (setting_key, setting_value) VALUES ('groq_api_key', ?)");
        $stmt->execute([$groq_api_key]);

        $stmt = $pdo->prepare("REPLACE INTO settings (setting_key, setting_value) VALUES ('groq_model', ?)");
        $stmt->execute([$groq_model]);

        $success = "Sozlamalar saqlandi.";
    } catch (PDOException $e) {
        $error = "Xatolik: " . $e->getMessage();
    }
}

// Get current settings
$stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
$settingsDB = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$currentApiKey = $settingsDB['groq_api_key'] ?? '';
$currentModel = $settingsDB['groq_model'] ?? 'llama-3.3-70b-versatile';

$pageTitle = 'Sun\'iy intellekt (AI) Sozlamalari';
require_once '../includes/layout_header.php';
?>

<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
            <span class="material-symbols-outlined text-purple-600 text-4xl">auto_awesome</span>
            AI Sozlamalari (Groq)
        </h1>
        <a href="index.php" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">arrow_back</span> Ortga
        </a>
    </div>

    <?php if ($success): ?>
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <form action="" method="POST" class="space-y-6">
            
            <div>
                <label for="groq_api_key" class="block text-sm font-semibold text-gray-700 mb-2">Groq API Key</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">key</span>
                    <input type="password" id="groq_api_key" name="groq_api_key" value="<?= htmlspecialchars($currentApiKey) ?>" 
                           class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-colors"
                           placeholder="gsk_...">
                </div>
                <p class="text-xs text-gray-500 mt-2">Ushbu kalit orqali sayt sun'iy intellekt xizmatlariga ulanadi. Kalitni <a href="https://console.groq.com/keys" target="_blank" class="text-purple-600 hover:underline">console.groq.com</a> saytidan olishingiz mumkin.</p>
            </div>

            <div>
                <label for="groq_model" class="block text-sm font-semibold text-gray-700 mb-2">AI Model</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">memory</span>
                    <select id="groq_model" name="groq_model" class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 transition-colors appearance-none">
                        <option value="llama-3.3-70b-versatile" <?= $currentModel === 'llama-3.3-70b-versatile' ? 'selected' : '' ?>>Llama 3.3 70B (Eng yangi, kuchli)</option>
                        <option value="llama-3.1-8b-instant" <?= $currentModel === 'llama-3.1-8b-instant' ? 'selected' : '' ?>>Llama 3.1 8B (Tezkor)</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-end">
                <button type="submit" class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold py-3 px-8 rounded-xl hover:shadow-lg hover:shadow-purple-500/30 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined">save</span>
                    Sozlamalarni saqlash
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/layout_footer.php'; ?>
