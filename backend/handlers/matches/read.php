<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/MatchModel.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

try {
    $db = getDB();
    $matchModel = new MatchModel($db);

    switch ($method) {
        case 'GET':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'Match ID is required']);
                exit();
            }
            
            $match = $matchModel->getById($id);
            if (!$match) {
                http_response_code(404);
                echo json_encode(['error' => 'Match not found']);
                exit();
            }
            
            echo json_encode($match);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
