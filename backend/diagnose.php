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

// Read .htaccess content (first 20 lines)
$htaccess_path = __DIR__ . '/.htaccess';
if (file_exists($htaccess_path)) {
    $htaccess_lines = file($htaccess_path);
    $diagnostics['htaccess_first_20_lines'] = array_slice($htaccess_lines, 0, 20);
} else {
    $diagnostics['htaccess'] = 'NOT FOUND - This is the problem!';
}

// Check database connection
try {
    require_once __DIR__ . '/config/database.php';
    $db = getDB();
    $db->query('SELECT 1');
    $diagnostics['database'] = [
        'connected' => true,
        'host' => getenv('DB_HOST') ?: $_SERVER['DB_HOST'] ?? 'not set',
        'name' => getenv('DB_NAME') ?: $_SERVER['DB_NAME'] ?? 'not set',
    ];
} catch (Exception $e) {
    $diagnostics['database'] = [
        'connected' => false,
        'error' => $e->getMessage(),
    ];
} catch (Throwable $t) {
    $diagnostics['database'] = [
        'connected' => false,
        'error' => 'Fatal: ' . $t->getMessage(),
    ];
}

// Check environment variables
$diagnostics['environment'] = [
    'DB_HOST_set' => !empty(getenv('DB_HOST') ?: $_SERVER['DB_HOST'] ?? ''),
    'DB_NAME_set' => !empty(getenv('DB_NAME') ?: $_SERVER['DB_NAME'] ?? ''),
    'DB_USER_set' => !empty(getenv('DB_USER') ?: $_SERVER['DB_USER'] ?? ''),
    'DB_PASS_set' => !empty(getenv('DB_PASS') ?: $_SERVER['DB_PASS'] ?? ''),
];

// Try to load MatchModel
try {
    require_once __DIR__ . '/models/MatchModel.php';
    $diagnostics['match_model'] = [
        'loaded' => true,
        'methods' => get_class_methods('MatchModel'),
    ];
} catch (Throwable $t) {
    $diagnostics['match_model'] = [
        'loaded' => false,
        'error' => $t->getMessage(),
    ];
}

// Test direct call to create.php
try {
    // Simulate what the .htaccess should do
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://' . $_SERVER['HTTP_HOST'] . '/handlers/matches/create.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'player1' => 'Test',
        'player2' => 'Debug',
        'type' => 'singles'
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $direct_response = curl_exec($ch);
    $direct_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $diagnostics['direct_php_test'] = [
        'url' => '/handlers/matches/create.php',
        'http_code' => $direct_http_code,
        'response_preview' => substr($direct_response, 0, 200),
        'is_json' => json_decode($direct_response) !== null,
    ];
} catch (Throwable $t) {
    $diagnostics['direct_php_test'] = [
        'error' => $t->getMessage(),
        'note' => 'curl might not be available',
    ];
}

// Test via /api/matches/ (what the frontend uses)
try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://' . $_SERVER['HTTP_HOST'] . '/api/matches/');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'player1' => 'Test',
        'player2' => 'Debug',
        'type' => 'singles'
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $api_response = curl_exec($ch);
    $api_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $diagnostics['api_routing_test'] = [
        'url' => '/api/matches/',
        'http_code' => $api_http_code,
        'response_preview' => substr($api_response, 0, 200),
        'is_json' => json_decode($api_response) !== null,
        'note' => json_decode($api_response) === null ? 'HTML returned instead of JSON - .htaccess rewrite not working!' : 'OK',
    ];
} catch (Throwable $t) {
    $diagnostics['api_routing_test'] = [
        'error' => $t->getMessage(),
        'note' => 'curl might not be available',
    ];
}

echo json_encode($diagnostics, JSON_PRETTY_PRINT);
