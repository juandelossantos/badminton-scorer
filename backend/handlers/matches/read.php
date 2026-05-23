<?php
header('Content-Type: application/json');
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
    $id = $_GET['id'] ?? null;

    if ($method !== 'GET') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        exit;
    }
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Match ID is required']);
        exit;
    }
    
    $db = getDB();
    $matchModel = new MatchModel($db);
    $match = $matchModel->getById($id);
    
    if (!$match) {
        http_response_code(404);
        echo json_encode(['error' => 'Match not found']);
        exit;
    }
    
    echo json_encode($match);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} catch (Throwable $t) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $t->getMessage()]);
}
