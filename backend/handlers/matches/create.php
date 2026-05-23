<?php
// Always return JSON, even on errors
header('Content-Type: application/json');

// Suppress PHP errors from being output as HTML
error_reporting(0);
ini_set('display_errors', 0);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../../models/MatchModel.php';

    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($method !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        exit;
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON: ' . json_last_error_msg()]);
        exit;
    }
    
    $db = getDB();
    $matchModel = new MatchModel($db);
    $match = $matchModel->create($input);
    
    http_response_code(201);
    echo json_encode($match);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
} catch (Throwable $t) {
    // Catch any PHP fatal errors
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $t->getMessage()]);
}
