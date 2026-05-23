<?php
// Diagnostic script for badminton scorer backend
// Upload this to public_html/ and visit: your-domain.com/diagnose.php
// Remove after debugging!

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$diagnostics = [
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => PHP_VERSION,
    'php_sapi' => PHP_SAPI,
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
    'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'unknown',
    'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
];

// Check mod_rewrite
if (function_exists('apache_get_modules')) {
    $diagnostics['mod_rewrite'] = in_array('mod_rewrite', apache_get_modules());
} else {
    $diagnostics['mod_rewrite'] = 'unavailable (not Apache?)';
}

// Check if required files exist
$required_files = [
    '.htaccess' => __DIR__ . '/.htaccess',
    'config/database.php' => __DIR__ . '/config/database.php',
    'models/MatchModel.php' => __DIR__ . '/models/MatchModel.php',
    'handlers/matches/create.php' => __DIR__ . '/handlers/matches/create.php',
    'handlers/health.php' => __DIR__ . '/handlers/health.php',
];

$diagnostics['files'] = [];
foreach ($required_files as $name => $path) {
    $diagnostics['files'][$name] = [
        'exists' => file_exists($path),
        'readable' => is_readable($path),
        'size' => file_exists($path) ? filesize($path) : 0,
    ];
}

// Test direct PHP file access with CORRECT field names
try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://' . $_SERVER['HTTP_HOST'] . '/handlers/matches/create.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    $testBody = json_encode([
        'mode' => 'singles',
        'player1' => ['Test'],
        'player2' => ['Debug']
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $testBody);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $direct_response = curl_exec($ch);
    $direct_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $diagnostics['direct_php_test'] = [
        'url' => '/handlers/matches/create.php',
        'http_code' => $direct_http_code,
        'response_preview' => substr($direct_response, 0, 300),
        'is_json' => json_decode($direct_response) !== null,
        'sent_body' => $testBody,
    ];
} catch (Throwable $t) {
    $diagnostics['direct_php_test'] = [
        'error' => $t->getMessage(),
        'note' => 'curl might not be available',
    ];
}

// Test via /api/matches/ with CORRECT field names
try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://' . $_SERVER['HTTP_HOST'] . '/api/matches/');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    $testBody = json_encode([
        'mode' => 'singles',
        'player1' => ['Test'],
        'player2' => ['Debug']
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $testBody);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $api_response = curl_exec($ch);
    $api_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $diagnostics['api_routing_test'] = [
        'url' => '/api/matches/',
        'http_code' => $api_http_code,
        'response_preview' => substr($api_response, 0, 300),
        'is_json' => json_decode($api_response) !== null,
        'sent_body' => $testBody,
    ];
} catch (Throwable $t) {
    $diagnostics['api_routing_test'] = [
        'error' => $t->getMessage(),
        'note' => 'curl might not be available',
    ];
}

// Check what php://input gives us
try {
    $inputData = file_get_contents('php://input');
    $diagnostics['php_input_test'] = [
        'raw_input_length' => strlen($inputData),
        'raw_input_preview' => substr($inputData, 0, 200),
        'json_decode_result' => json_decode($inputData, true),
        'json_last_error' => json_last_error_msg(),
    ];
} catch (Throwable $t) {
    $diagnostics['php_input_test'] = [
        'error' => $t->getMessage(),
    ];
}

echo json_encode($diagnostics, JSON_PRETTY_PRINT);
