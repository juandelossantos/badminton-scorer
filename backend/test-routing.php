<?php
// Test if API routing works correctly
header('Content-Type: application/json');

echo json_encode([
    'message' => 'If you see this, the .htaccess rewrite is NOT working for POST requests',
    'tip' => 'POST to /api/matches/ should go to handlers/matches/create.php, not to index.html',
    'mod_rewrite' => in_array('mod_rewrite', apache_get_modules()) ? 'enabled' : 'unknown/unavailable',
    'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
    'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'unknown',
    'actual_file' => __FILE__,
]);
