<?php
// admin/login.php

require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Redirect to dashboard if already authenticated
if (isset($_SESSION['admin_id'])) {
    header("Location: " . BASE_URL . "admin/index.php");
    exit;
}

$error = '';

// Brute Force Protection Logic
$maxAttempts = 5;
$lockoutTime = 15 * 60; // 15 minutes in seconds

if (isset($_SESSION['lockout_until']) && $_SESSION['lockout_until'] > time()) {
    $remaining = ceil(($_SESSION['lockout_until'] - time()) / 60);
    $error = "Siz juda ko'p marotaba xato parolni kiritdingiz. Iltimos, {$remaining} daqiqadan so'ng qayta urinib ko'ring.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    verifyCsrf();
    
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Iltimos, login va parolni kiriting.';
    } else {
        if (login($username, $password, $pdo)) {
            // Reset attempts on successful login
            unset($_SESSION['login_attempts']);
            unset($_SESSION['lockout_until']);
            header("Location: " . BASE_URL . "admin/index.php");
            exit;
        } else {
            // Increment attempts
            $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
            
            if ($_SESSION['login_attempts'] >= $maxAttempts) {
                $_SESSION['lockout_until'] = time() + $lockoutTime;
                $error = "Siz juda ko'p marotaba xato parolni kiritdingiz. Tizim 15 daqiqaga qulflanadi.";
            } else {
                $left = $maxAttempts - $_SESSION['login_attempts'];
                $error = "Login yoki parol noto'g'ri. Qolgan imkoniyatlar: {$left}";
            }
        }
    }
}

$csrfToken = generateCsrf();
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Tourism Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: {
                            900: '#0f172a',
                            800: '#1e293b',
                            700: '#334155'
                        }
                    }
                }
            }
            }
        }
    </script>
</head>
<body class="bg-dark-900 text-gray-100 min-h-screen flex items-center justify-center font-sans antialiased p-4">
    <div class="w-full max-w-md p-8 bg-dark-800 rounded-2xl shadow-2xl border border-dark-700 relative overflow-hidden">
        <!-- Decorative gradient background effect -->
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500"></div>
        
        <div class="text-center mb-8 mt-2">
            <h1 class="text-3xl font-bold tracking-tight text-white">Tizimga Kirish</h1>
            <p class="text-gray-400 mt-2 text-sm">Boshqaruv paneliga xush kelibsiz</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-lg mb-6 text-sm flex items-center" role="alert">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-300 mb-2">Login</label>
                <input type="text" id="username" name="username" required autocomplete="username"
                       class="w-full px-4 py-3 bg-dark-900 border border-dark-700 rounded-lg focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-colors text-white placeholder-gray-500 outline-none"
                       placeholder="Loginni kiriting">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-300 mb-2">Parol</label>
                <input type="password" id="password" name="password" required autocomplete="current-password"
                       class="w-full px-4 py-3 bg-dark-900 border border-dark-700 rounded-lg focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-colors text-white placeholder-gray-500 outline-none"
                       placeholder="••••••••">
            </div>

            <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-500 text-white font-semibold py-3 px-4 rounded-lg transition-colors focus:ring-4 focus:ring-blue-500/50 outline-none mt-4 shadow-lg shadow-blue-500/25">
                Kirish
            </button>
        </form>
    </div>
</body>
</html>
