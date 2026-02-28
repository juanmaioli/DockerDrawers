<?php
/**
 * img_proxy.php - Proxy to serve HTTP images over HTTPS
 * to avoid Mixed Content issues.
 */

// Add a random parameter to the source URL to prevent backend caching
$url = "http://pikapp.com.ar/rnd_img/index.php?id=anime&rand=" . uniqid();

// Initializing CURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
// Set a realistic User-Agent to avoid being blocked
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');

$data = curl_exec($ch);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200 && !empty($data)) {
    header("Content-Type: $contentType");
    // Disable browser caching to ensure a new random image on every load
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo $data;
} else {
    // Fallback if proxy fails
    http_response_code(404);
    echo "Image not found";
}
?>
