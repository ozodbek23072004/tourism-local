<?php
$url = 'https://maps.app.goo.gl/YG5fUKfUisCTHNQPA';
$headers = @get_headers($url, 1);
$location = isset($headers['Location']) ? (is_array($headers['Location']) ? end($headers['Location']) : $headers['Location']) : $url;
echo 'Location: ' . $location . "\n";

$lat = null;
$lng = null;

// First try to find exact pin marker (!3d... !4d...)
if (preg_match('/!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/', $location, $matches)) {
    $lat = $matches[1];
    $lng = $matches[2];
    echo "Found via !3d !4d\n";
} 
// Fallback to viewport center (@lat,lng)
elseif (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $location, $matches)) {
    $lat = $matches[1];
    $lng = $matches[2];
    echo "Found via @lat,lng\n";
}

echo "Lat: $lat, Lng: $lng\n";
?>
