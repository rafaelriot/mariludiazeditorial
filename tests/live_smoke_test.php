<?php

declare(strict_types=1);

$baseUrl = 'http://192.168.1.118:8080';

$endpoints = [
    '/' => [
        'status' => 200,
        'must_contain' => ['Marilú Díaz', 'Historias Destacadas', 'newsletter'],
    ],
    '/?q=liderazgo' => [
        'status' => 200,
        'must_contain' => ['Resultados de búsqueda'],
    ],
    '/categoria/liderazgo' => [
        'status' => 200,
        'must_contain' => ['Liderazgo'],
    ],
    '/sitemap.xml' => [
        'status' => 200,
        'content_type' => 'application/xml',
        'must_contain' => ['<?xml', '<urlset', 'http'],
    ],
    '/feed.xml' => [
        'status' => 200,
        'content_type' => 'application/rss+xml',
        'must_contain' => ['<?xml', '<rss', '<channel>'],
    ],
    '/robots.txt' => [
        'status' => 200,
        'must_contain' => ['User-agent', 'Sitemap'],
    ],
    '/sobre-mi' => [
        'status' => 200,
        'must_contain' => ['Sobre Mí', 'Marilú Díaz'],
    ],
    '/login' => [
        'status' => 200,
        'must_contain' => ['Iniciar Sesión'],
    ],
    '/auth/google/mock' => [
        'status' => 200,
        'must_contain' => ['Continuar con Google'],
    ],
];

$ch = curl_init();
$failed = 0;

echo "========================================================\n";
echo "   SMOKE TEST INTEGRAL DE PLATAFORMA EDITORIAL EN VIVO\n";
echo "========================================================\n\n";

foreach ($endpoints as $path => $spec) {
    curl_setopt($ch, CURLOPT_URL, $baseUrl . $path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);

    if ($httpCode !== $spec['status']) {
        echo "[FAIL] {$path} -> Esperado HTTP {$spec['status']}, obtenido {$httpCode}\n";
        $failed++;
        continue;
    }

    if (isset($spec['content_type']) && !str_contains($headers, $spec['content_type'])) {
        echo "[FAIL] {$path} -> Falta Content-Type '{$spec['content_type']}'\n";
        $failed++;
        continue;
    }

    if (isset($spec['must_contain'])) {
        foreach ($spec['must_contain'] as $needle) {
            if (!str_contains($body, $needle)) {
                echo "[FAIL] {$path} -> Falta texto esperado '{$needle}' en el cuerpo de la respuesta\n";
                $failed++;
                continue 2;
            }
        }
    }

    echo "[PASS] {$path} (HTTP {$httpCode} OK)\n";
}

// 1. Validación de Sintaxis XML de Sitemap
echo "\n--- 1. Validación Estricta de Sintaxis XML (Sitemap) ---\n";
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/sitemap.xml');
curl_setopt($ch, CURLOPT_HEADER, false);
$sitemapXml = curl_exec($ch);
libxml_use_internal_errors(true);
$xmlDoc = simplexml_load_string($sitemapXml);
if ($xmlDoc === false) {
    echo "[FAIL] Sitemap XML tiene errores de parsing:\n";
    foreach (libxml_get_errors() as $error) {
        echo " - Línea {$error->line}: {$error->message}\n";
    }
    $failed++;
} else {
    $count = count($xmlDoc->url);
    echo "[PASS] Sitemap XML es 100% válido y contiene {$count} URLs indexadas.\n";
}

// 2. Validación de Sintaxis XML de RSS
echo "\n--- 2. Validación Estricta de Sintaxis RSS Feed ---\n";
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/feed.xml');
curl_setopt($ch, CURLOPT_HEADER, false);
$rssXml = curl_exec($ch);
$rssDoc = simplexml_load_string($rssXml);
if ($rssDoc === false) {
    echo "[FAIL] RSS XML tiene errores de parsing.\n";
    $failed++;
} else {
    $itemsCount = count($rssDoc->channel->item);
    echo "[PASS] RSS Feed XML es 100% válido y contiene {$itemsCount} artículos.\n";
}

curl_close($ch);

echo "\n========================================================\n";
if ($failed === 0) {
    echo "  ¡TODAS LAS PRUEBAS EN VIVO FINALIZARON CON ÉXITO!\n";
    echo "========================================================\n";
    exit(0);
} else {
    echo "  FALLARON {$failed} PRUEBAS EN VIVO\n";
    echo "========================================================\n";
    exit(1);
}
