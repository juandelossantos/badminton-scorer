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
    
    if ($method !== 'PUT') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        exit;
    }
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Match ID is required']);
        exit;
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON: ' . json_last_error_msg()]);
        exit;
    }
    
    if (!isset($input['player']) || !in_array($input['player'], [1, 2])) {
        http_response_code(400);
        echo json_encode(['error' => 'Player must be 1 or 2']);
        exit;
    }
    
    $token = $input['token'] ?? '';
    if (empty($token)) {
        http_response_code(401);
        echo json_encode(['error' => 'Control token is required']);
        exit;
    }
    
    $db = getDB();
    $matchModel = new MatchModel($db);
    
    $undo = $input['undo'] ?? false;
    $match = $matchModel->updateScore($id, $input['player'], $token, $undo);
    
    echo json_encode($match);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
} catch (Throwable $t) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $t->getMessage()]);
}
