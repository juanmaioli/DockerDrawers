<?php
session_start();
include_once("../config.php");

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

// Validar sesión del usuario
if (empty($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'No autenticado']);
    exit();
}

$mla_id = $_GET['id'] ?? ($_POST['id'] ?? '');
$mla_id = preg_replace('/[^A-Z0-9]/', '', strtoupper($mla_id));

if (empty($mla_id)) {
    echo json_encode(['status' => 'error', 'message' => 'ID de artículo inválido']);
    exit();
}

$conn = get_db_connection();
$permalink = "https://articulo.mercadolibre.com.ar/MLA-" . str_replace('MLA', '', $mla_id);

$title = '';
$img   = '';
$price = null;
$is_full = 'no';
$is_international = 'no';

// 1. Scraping HTML mediante cURL con User-Agent de bot para evitar páginas de tráfico sospechoso
$ch = curl_init($permalink);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT        => 15,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_USERAGENT      => 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)',
]);
$html = curl_exec($ch);
curl_close($ch);

// Fallback: si MercadoLibre bloquea con la pantalla de tráfico sospechoso, usar proxy de Google Translate
if (empty($html) || stripos($html, 'suspicious-traffic') !== false || stripos($html, 'account-verification') !== false || stripos($html, 'og:title') === false) {
    $proxy_url = "https://translate.google.com/translate?sl=auto&tl=es&u=" . urlencode($permalink);
    $ch_p = curl_init($proxy_url);
    curl_setopt_array($ch_p, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
    ]);
    $html_proxy = curl_exec($ch_p);
    curl_close($ch_p);
    if (!empty($html_proxy) && stripos($html_proxy, 'suspicious-traffic') === false) {
        $html = $html_proxy;
    }
}

if (!empty($html)) {
    // A. Extracción de Título y Precio desde og:title (Ej: "Google TV Streamer... - $ 224.900")
    if (preg_match('/<meta\s+(?:property|name)=["\']og:title["\']\s+content=["\'](.*?)["\']/i', $html, $m)) {
        $raw_title = html_entity_decode($m[1], ENT_QUOTES, 'UTF-8');
        if (preg_match('/^(.*?)\s*-\s*\$\s*([\d\.\,]+)/u', $raw_title, $pm)) {
            $title = trim($pm[1]);
            $price_str = str_replace(['.', ','], ['', '.'], $pm[2]);
            $price = floatval($price_str);
        } else {
            $title = $raw_title;
        }
    } else if (preg_match('/<title>(.*?)<\/title>/i', $html, $m)) {
        $title = html_entity_decode(explode('|', $m[1])[0], ENT_QUOTES, 'UTF-8');
    } else if (preg_match('/class=["\'][^"\']*ui-pdp-title[^"\']*["\'][^>]*>(.*?)<\/h1>/is', $html, $m)) {
        $title = trim(strip_tags($m[1]));
    } else if (preg_match('/<base[^>]+href=["\'](https?:\/\/[^"\']+)["\']/i', $html, $m)) {
        if (preg_match('/\/([a-z0-9\-]+)\/p\/MLA/i', $m[1], $sm)) {
            $title = ucwords(str_replace('-', ' ', $sm[1]));
        }
    }
    $title = trim(str_replace([' | MercadoLibre', ' | Mercado Libre'], '', $title));

    // Si el título parece una URL o contiene translate.goog o sigue igual a la id, limpiar extraendo el slug legible
    if (empty($title) || $title === $mla_id || preg_match('/https?:\/\/[^\s]+/i', $title) || stripos($title, 'translate.goog') !== false) {
        if (preg_match('/\/([a-z0-9\-]+)\/p\/MLA/i', $html, $sm)) {
            $title = ucwords(str_replace('-', ' ', $sm[1]));
        } else if (preg_match('/MLA-\d+-([a-z0-9\-]+)/i', $html, $sm)) {
            $title = ucwords(str_replace('-', ' ', $sm[1]));
        }
    }

    // B. Extracción de Imagen
    if (preg_match('/<meta\s+(?:property|name)=["\'](?:og:image|twitter:image)["\']\s+content=["\'](.*?)["\']/i', $html, $m)) {
        $img = str_replace('http://', 'https://', $m[1]);
    } else if (preg_match('/<img[^>]+class=["\'][^"\']*ui-pdp-image[^"\']*["\'][^>]+src=["\'](.*?)["\']/i', $html, $m)) {
        $img = str_replace('http://', 'https://', $m[1]);
    } else if (preg_match('/https?:\/\/http2\.mlstatic\.com\/D_[A-Z0-9_\-]+/i', $html, $im)) {
        $img = $im[0];
    }

    // C. Extracción de Precio Fallback (JSON offers, og:price:amount, andes-money-amount)
    if (empty($price)) {
        if (preg_match('/"offers"\s*:\s*\{[^}]*"price"\s*:\s*([\d\.]+)/i', $html, $m)) {
            $price = floatval($m[1]);
        } else if (preg_match('/<meta\s+property=["\']og:price:amount["\']\s+content=["\']([\d\.]+)["\']/i', $html, $m)) {
            $price = floatval($m[1]);
        } else if (preg_match('/"price"\s*:\s*"?([\d\.]+)"?/i', $html, $m)) {
            $price = floatval($m[1]);
        } else if (preg_match_all('/class=["\'][^"\']*andes-money-amount__fraction[^"\']*["\'][^>]*>([\d\.]+)</i', $html, $m)) {
            $cleanNum = str_replace('.', '', $m[1][0]);
            $price = floatval($cleanNum);
        }
    }

    // D. Detección de Envío Full
    if (
        stripos($html, 'enviado por full') !== false ||
        stripos($html, 'poly-shipping__promise-icon--full') !== false ||
        stripos($html, '#poly_full') !== false ||
        stripos($html, 'Almacenado y enviado por') !== false ||
        stripos($html, 'Llega mañana con Full') !== false ||
        stripos($html, 'Llega hoy con Full') !== false
    ) {
        $is_full = 'si';
    }

    // E. Detección de Compra Internacional
    if (
        stripos($html, 'envío internacional') !== false ||
        stripos($html, 'cbt_logo') !== false ||
        stripos($html, 'envío desde china') !== false ||
        stripos($html, 'compra internacional') !== false
    ) {
        $is_international = 'si';
    }

    // F. Extracción de Descripción
    $desc = '';
    if (preg_match('/<meta\s+property=["\']og:description["\']\s+content=["\'](.*?)["\']/i', $html, $m)) {
        $desc = html_entity_decode($m[1], ENT_QUOTES, 'UTF-8');
    }
}

// 2. Regla de Exclusión Mutua: Full e Internacional son independientes pero no ambos 'si'
if ($is_full === 'si') {
    $is_international = 'no';
}

if (empty($title)) {
    $title = $mla_id;
}

// 3. Persistir en MariaDB drawers_fav
$stmt_check = $conn->prepare("SELECT count(*) AS total FROM drawers_fav WHERE fav_mla = ?");
$stmt_check->bind_param("s", $mla_id);
$stmt_check->execute();
$row_check = $stmt_check->get_result()->fetch_assoc();
$stmt_check->close();

if (!empty($row_check['total'])) {
    $stmt = $conn->prepare("UPDATE drawers_fav SET fav_title = ?, fav_img = ?, fav_price = ?, fav_desc = ?, fav_full = ?, fav_internacional = ? WHERE fav_mla = ?");
    $stmt->bind_param("ssdssss", $title, $img, $price, $desc, $is_full, $is_international, $mla_id);
    $stmt->execute();
    $stmt->close();
} else {
    $stmt = $conn->prepare("INSERT INTO drawers_fav (fav_mla, fav_link, fav_date, fav_title, fav_img, fav_price, fav_desc, fav_full, fav_internacional, fav_delete) VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, ?, 0)");
    $stmt->bind_param("ssssdsss", $mla_id, $permalink, $title, $img, $price, $desc, $is_full, $is_international);
    $stmt->execute();
    $stmt->close();
}

$conn->close();

echo json_encode([
    'status' => 'ok',
    'data'   => [
        'id'            => $mla_id,
        'title'         => $title,
        'img'           => $img ?: 'images/ml.svg',
        'price'         => $price,
        'desc'          => $desc,
        'full'          => $is_full,
        'internacional' => $is_international,
        'link'          => $permalink,
    ]
]);

