<?php
/**
 * Proxy para detalles de ítems de ML.
 * PolicyAgent bloquea las llamadas desde el browser.
 * Este proxy corre server-side con el access_token del usuario.
 * URL: api/ml_item_proxy.php?ids=MLA1,MLA2,...
 */
session_start();
include("../config.php");

header('Content-Type: application/json');
header('Cache-Control: no-store');

// Validar sesión
if (empty($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit();
}

$usuarioId = (int)($_SESSION['usuario_id'] ?? 0);
if (!$usuarioId) {
    http_response_code(401);
    echo json_encode(['error' => 'Sin usuario_id']);
    exit();
}

// Sanitizar IDs
$ids_raw = $_GET['ids'] ?? '';
$ids     = preg_replace('/[^A-Z0-9,]/', '', strtoupper($ids_raw));
if (empty($ids)) { echo json_encode([]); exit(); }

// Obtener access_token
$conn = get_db_connection();
$stmt = $conn->prepare("SELECT access_token, refresh_token, expires_at FROM drawers_ml_auth WHERE usr_id = ?");
$stmt->bind_param("i", $usuarioId);
$stmt->execute();
$res  = $stmt->get_result();
$auth = $res->num_rows > 0 ? $res->fetch_assoc() : null;
$stmt->close();

if (!$auth) {
    $conn->close();
    http_response_code(401);
    echo json_encode(['error' => 'Sin token ML']);
    exit();
}

$access_token = $auth['access_token'];

// Refrescar si expiró
if (time() >= strtotime($auth['expires_at'])) {
    $ch = curl_init("https://api.mercadolibre.com/oauth/token");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query([
            'grant_type'    => 'refresh_token',
            'client_id'     => $ml_client_id,
            'client_secret' => $ml_client_secret,
            'refresh_token' => $auth['refresh_token'],
        ]),
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_TIMEOUT    => 10,
    ]);
    $r = json_decode(curl_exec($ch), true);
    curl_close($ch);
    if (isset($r['access_token'])) {
        $access_token = $r['access_token'];
        $exp          = date("Y-m-d H:i:s", time() + ($r['expires_in'] ?? 21600));
        $stm = $conn->prepare("UPDATE drawers_ml_auth SET access_token=?, expires_at=? WHERE usr_id=?");
        $stm->bind_param("ssi", $access_token, $exp, $usuarioId);
        $stm->execute();
        $stm->close();
    }
}
$conn->close();

// Attempt to fetch details - status, title, etc.
$attrs = "id,title,thumbnail,price,currency_id,permalink,status";
$url   = "https://api.mercadolibre.com/items?ids={$ids}&attributes={$attrs}";

function proxy_curl(string $url, ?string $token = null): array {
    $headers = ['Accept: application/json'];
    if ($token) $headers[] = "Authorization: Bearer {$token}";
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; DrawersApp/1.0)',
    ]);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $data = json_decode($body, true);
    return ['code' => $code, 'data' => $data, 'raw' => substr($body, 0, 400)];
}

$r1 = proxy_curl($url, $access_token);

// Si falla con token, intentar sin token
$r2 = null;
if (!is_array($r1['data']) || isset($r1['data']['error']) || isset($r1['data']['blocked_by'])) {
    $r2 = proxy_curl($url, null);
}

$raw = ($r2 && is_array($r2['data']) && !isset($r2['data']['error'])) ? $r2 : $r1;
$data = $raw['data'];

// Si ambos fallaron, devolver debug
if (!is_array($data) || isset($data['error']) || isset($data['blocked_by'])) {
    echo json_encode([
        '_debug' => [
            'r1_code' => $r1['code'],
            'r1_raw'  => $r1['raw'],
            'r2_code' => $r2['code'] ?? null,
            'r2_raw'  => $r2['raw'] ?? null,
        ]
    ]);
    exit();
}

// Normalizar e identificar favoritos eliminados o no encontrados
$result = [];
if (is_array($data)) {
    foreach ($data as $entry) {
        if (!is_array($entry)) continue;

        $code = $entry['code'] ?? 200;
        $item = (isset($entry['body']) && is_array($entry['body'])) ? $entry['body'] : $entry;
        $itemId = $item['id'] ?? ($entry['body']['id'] ?? null);

        if (($code == 200 || $code == 206) && !empty($item['id'])) {
            $result[] = [
                'id'          => $item['id'],
                'title'       => !empty($item['title']) ? $item['title'] : $item['id'],
                'thumbnail'   => str_replace('http://', 'https://', $item['thumbnail'] ?? ''),
                'price'       => $item['price'] ?? null,
                'currency_id' => $item['currency_id'] ?? 'ARS',
                'permalink'   => $item['permalink'] ?? '',
                'deleted'     => false,
            ];
        } else if ($itemId) {
            $result[] = [
                'id'      => $itemId,
                'deleted' => true,
            ];
        }
    }
}

echo json_encode($result);
