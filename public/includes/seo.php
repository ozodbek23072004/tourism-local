<?php
function renderMeta(array $meta = []): void {
    global $seoMetaHtml;
    
    $title = $meta['title'] ?? "Tourism UZ — O'zbekiston sayohat portali";
    $description = $meta['description'] ?? "O'zbekistondagi diqqatga sazovor joylar, tarixiy shaxslar, restoranlar va mehmonxonalar haqida ma'lumot";
    
    // Ensure BASE_URL is available
    $baseUrl = defined('BASE_URL') ? BASE_URL : '/';
    $image = $meta['image'] ?? ($baseUrl . 'assets/og-default.jpg');
    
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $url = $meta['url'] ?? ($protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

    $html = '<title>' . htmlspecialchars($title) . '</title>' . "\n";
    $html .= '    <meta name="description" content="' . htmlspecialchars($description) . '">' . "\n";
    $html .= '    <meta property="og:title" content="' . htmlspecialchars($title) . '">' . "\n";
    $html .= '    <meta property="og:description" content="' . htmlspecialchars($description) . '">' . "\n";
    $html .= '    <meta property="og:image" content="' . htmlspecialchars($image) . '">' . "\n";
    $html .= '    <meta property="og:url" content="' . htmlspecialchars($url) . '">' . "\n";
    $html .= '    <meta property="og:type" content="website">' . "\n";
    $html .= '    <link rel="canonical" href="' . htmlspecialchars($url) . '">';

    $seoMetaHtml = $html;
}
