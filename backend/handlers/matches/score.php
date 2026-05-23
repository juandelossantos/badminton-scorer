<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/MatchModel.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;
$input = json_decode(file_get_contents('php://input'), true);

try {
    $db = getDB();
    $matchModel = new MatchModel($db);

    switch ($method) {
        case 'PUT':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'Match ID is required']);
                exit();
            }
            
            if (!isset($input['player']) || !in_array($input['player'], [1, 2])) {
                http_response_code(400);
                echo json_encode(['error' => 'Player must be 1 or 2']);
                exit();
            }
            
            $undo = $input['undo'] ?? false;
            $match = $matchModel->updateScore($id, $input['player'], $undo);
            
            echo json_encode($match);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
