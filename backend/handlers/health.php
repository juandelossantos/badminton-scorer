<?php
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    require_once __DIR__ . '/../config/database.php';
    
    $db = getDB();
    $db->query('SELECT 1');
    
    http_response_code(200);
    echo json_encode(['status' => 'ok', 'database' => 'connected']);
    
} catch (Exception $e) {
    http_response_code(503);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} catch (Throwable $t) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $t->getMessage()]);
}
