<?php
require_once '../includes/config.php';
require_once '../includes/db.php';

header("Content-Type: text/xml;charset=utf-8");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

function addUrl($url, $lastmod, $changefreq = 'weekly', $priority = '0.8') {
    echo '<url>';
    echo '<loc>' . htmlspecialchars($url) . '</loc>';
    if ($lastmod) {
        echo '<lastmod>' . date('Y-m-d', strtotime($lastmod)) . '</lastmod>';
    }
    echo '<changefreq>' . $changefreq . '</changefreq>';
    echo '<priority>' . $priority . '</priority>';
    echo '</url>';
}

$baseUrl = BASE_URL . 'public/';

// Home page
addUrl($baseUrl . 'index.php', date('Y-m-d'), 'daily', '1.0');
addUrl($baseUrl . 'search.php', date('Y-m-d'), 'daily', '0.8');

// List pages
addUrl($baseUrl . 'places/index.php', date('Y-m-d'), 'daily', '0.9');
addUrl($baseUrl . 'people/index.php', date('Y-m-d'), 'weekly', '0.8');
addUrl($baseUrl . 'restaurants/index.php', date('Y-m-d'), 'weekly', '0.8');
addUrl($baseUrl . 'hotels/index.php', date('Y-m-d'), 'weekly', '0.8');

// Places
$stmt = $pdo->prepare("SELECT id, created_at FROM places WHERE status = 'active' ORDER BY id DESC");
$stmt->execute();
while ($row = $stmt->fetch()) {
    addUrl($baseUrl . 'places/view.php?id=' . $row['id'], $row['created_at'], 'monthly', '0.7');
}

// People
$stmt = $pdo->prepare("SELECT id, created_at FROM people WHERE status = 'active' ORDER BY id DESC");
$stmt->execute();
while ($row = $stmt->fetch()) {
    addUrl($baseUrl . 'people/view.php?id=' . $row['id'], $row['created_at'], 'monthly', '0.6');
}

// Restaurants
$stmt = $pdo->prepare("SELECT id, created_at FROM restaurants WHERE status = 'active' ORDER BY id DESC");
$stmt->execute();
while ($row = $stmt->fetch()) {
    addUrl($baseUrl . 'restaurants/view.php?id=' . $row['id'], $row['created_at'], 'monthly', '0.6');
}

// Hotels
$stmt = $pdo->prepare("SELECT id, created_at FROM hotels WHERE status = 'active' ORDER BY id DESC");
$stmt->execute();
while ($row = $stmt->fetch()) {
    addUrl($baseUrl . 'hotels/view.php?id=' . $row['id'], $row['created_at'], 'monthly', '0.6');
}

echo '</urlset>';
