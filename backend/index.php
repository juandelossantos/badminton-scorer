<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Simple router
$path = $_GET['path'] ?? '';

if ($path === 'health') {
    echo json_encode(['status' => 'ok']);
    exit();
}

// 404 for unknown paths
http_response_code(404);
echo json_encode(['error' => 'Not found']);
