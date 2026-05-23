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

// Check if required files exist
$required_files = [
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
        'host' => getenv('DB_HOST') ?: $_SERVER['DB_HOST'] ?? 'not set',
        'name' => getenv('DB_NAME') ?: $_SERVER['DB_NAME'] ?? 'not set',
    ];
} catch (Throwable $t) {
    $diagnostics['database'] = [
        'connected' => false,
        'error' => 'Fatal: ' . $t->getMessage(),
        'trace' => $t->getTraceAsString(),
    ];
}

// Check environment variables
$diagnostics['environment'] = [
    'DB_HOST_set' => !empty(getenv('DB_HOST') ?: $_SERVER['DB_HOST'] ?? ''),
    'DB_NAME_set' => !empty(getenv('DB_NAME') ?: $_SERVER['DB_NAME'] ?? ''),
    'DB_USER_set' => !empty(getenv('DB_USER') ?: $_SERVER['DB_USER'] ?? ''),
    'DB_PASS_set' => !empty(getenv('DB_PASS') ?: $_SERVER['DB_PASS'] ?? ''),
];

// Try to create a test match (optional)
try {
    require_once __DIR__ . '/models/MatchModel.php';
    $db = getDB();
    $model = new MatchModel($db);
    $diagnostics['match_model'] = [
        'loaded' => true,
        'methods' => get_class_methods($model),
    ];
} catch (Throwable $t) {
    $diagnostics['match_model'] = [
        'loaded' => false,
        'error' => $t->getMessage(),
    ];
}

echo json_encode($diagnostics, JSON_PRETTY_PRINT);
