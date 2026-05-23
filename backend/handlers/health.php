<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config/database.php';

try {
    $db = getDB();
    $db->query('SELECT 1');
    http_response_code(200);
    echo json_encode(['status' => 'ok', 'database' => 'connected']);
} catch (Exception $e) {
    http_response_code(503);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
