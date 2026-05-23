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
            
            $match = $matchModel->getById($id);
            if (!$match) {
                http_response_code(404);
                echo json_encode(['error' => 'Match not found']);
                exit();
            }
            
            $status = $input['status'] ?? 'completed';
            $winner = $input['winner'] ?? null;
            
            $stmt = $db->prepare("
                UPDATE matches 
                SET status = :status, winner = :winner, updated_at = NOW()
                WHERE id = :id
            ");
            
            $stmt->execute([
                ':id' => $id,
                ':status' => $status,
                ':winner' => $winner
            ]);
            
            $updated = $matchModel->getById($id);
            echo json_encode($updated);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
