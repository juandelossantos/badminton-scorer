<?php

class MatchModel
{
    private $db;
    
    public function __construct($db)
    {
        $this->db = $db;
    }
    
    public function generateId()
    {
        $maxAttempts = 10;
        $attempts = 0;
        
        do {
            $id = strtoupper(substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'), 0, 8));
            $stmt = $this->db->prepare("SELECT 1 FROM matches WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $exists = $stmt->fetch();
            $attempts++;
        } while ($exists && $attempts < $maxAttempts);
        
        if ($exists) {
            throw new Exception('Failed to generate unique match ID after ' . $maxAttempts . ' attempts');
        }
        
        return $id;
    }
    
    public function create($data)
    {
        // Validation
        if (!isset($data['mode']) || !in_array($data['mode'], ['singles', 'doubles'])) {
            throw new Exception('Invalid mode. Must be singles or doubles.');
        }
        
        if (!isset($data['player1']) || empty($data['player1'])) {
            throw new Exception('Player1 names are required.');
        }
        
        if (!isset($data['player2']) || empty($data['player2'])) {
            throw new Exception('Player2 names are required.');
        }
        
        $id = $this->generateId();
        // Badminton rules: always best of 3 sets, 21 points per set
        $setsToWin = 2;
        $pointsPerSet = 21;
        
        $stmt = $this->db->prepare("
            INSERT INTO matches (
                id, mode, player1_names, player2_names,
                sets_to_win, points_per_set, status,
                current_set, server, service_side,
                current_p1, current_p2, sets_data,
                created_at, updated_at
            ) VALUES (
                :id, :mode, :player1, :player2,
                :sets_to_win, :points_per_set, 'active',
                1, 1, 'right',
                0, 0, :sets_data,
                NOW(), NOW()
            )
        ");
        
        $stmt->execute([
            ':id' => $id,
            ':mode' => $data['mode'],
            ':player1' => json_encode($data['player1']),
            ':player2' => json_encode($data['player2']),
            ':sets_to_win' => $setsToWin,
            ':points_per_set' => $pointsPerSet,
            ':sets_data' => json_encode([])
        ]);
        
        return $this->getById($id);
    }
    
    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM matches WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        
        if (!$row) return null;
        
        return $this->formatMatch($row);
    }
    
    public function updateScore($id, $player, $undo = false)
    {
        $match = $this->getById($id);
        if (!$match) {
            throw new Exception('Match not found');
        }
        
        if ($match['status'] !== 'active') {
            throw new Exception('Match is not active');
        }
        
        $score = $match['current_score'];
        
        if ($undo) {
            // Undo: remove 1 point from player
            if ($player === 1 && $score['p1'] > 0) {
                $score['p1']--;
            } elseif ($player === 2 && $score['p2'] > 0) {
                $score['p2']--;
            }
            
            $this->saveScore($id, $score, $match['server'], $match['service_side']);
            return $this->getById($id);
        }
        
        // Add point
        if ($player === 1) {
            $score['p1']++;
        } else {
            $score['p2']++;
        }
        
        // Server goes to winner of the point
        $server = $player;
        
        // Service side: if server score is even -> right, odd -> left
        $serverScore = ($player === 1) ? $score['p1'] : $score['p2'];
        $serviceSide = ($serverScore % 2 === 0) ? 'right' : 'left';
        
        // Check if set is won
        $p1Score = $score['p1'];
        $p2Score = $score['p2'];
        $pointsPerSet = $match['points_per_set'];
        $maxPoints = 30;
        
        $setWon = false;
        $setWinner = null;
        
        // Win by reaching points_per_set with 2-point difference
        if ($p1Score >= $pointsPerSet && $p1Score - $p2Score >= 2) {
            $setWon = true;
            $setWinner = 1;
        } elseif ($p2Score >= $pointsPerSet && $p2Score - $p1Score >= 2) {
            $setWon = true;
            $setWinner = 2;
        } elseif ($p1Score === $maxPoints || $p2Score === $maxPoints) {
            // Win by reaching 30 (cap)
            $setWon = true;
            $setWinner = ($p1Score === $maxPoints) ? 1 : 2;
        }
        
        if ($setWon) {
            $sets = $match['sets'];
            $sets[] = ['p1' => $p1Score, 'p2' => $p2Score];
            
            $p1SetsWon = 0;
            $p2SetsWon = 0;
            foreach ($sets as $set) {
                if ($set['p1'] > $set['p2']) $p1SetsWon++;
                else $p2SetsWon++;
            }
            
            $setsToWin = $match['sets_to_win'];
            $matchWon = ($p1SetsWon >= $setsToWin || $p2SetsWon >= $setsToWin);
            
            if ($matchWon) {
                $winner = ($p1SetsWon >= $setsToWin) ? 1 : 2;
                $this->saveSet($id, $sets, 'completed', $winner);
                return $this->getById($id);
            } else {
                // Start new set
                $currentSet = $match['current_set'] + 1;
                $this->saveSetAndReset($id, $sets, $currentSet, $server);
                return $this->getById($id);
            }
        }
        
        $this->saveScore($id, $score, $server, $serviceSide);
        return $this->getById($id);
    }
    
    private function saveScore($id, $score, $server, $serviceSide)
    {
        $stmt = $this->db->prepare("
            UPDATE matches 
            SET current_p1 = :p1, current_p2 = :p2, 
                server = :server, service_side = :service_side,
                updated_at = NOW()
            WHERE id = :id
        ");
        
        $stmt->execute([
            ':id' => $id,
            ':p1' => $score['p1'],
            ':p2' => $score['p2'],
            ':server' => $server,
            ':service_side' => $serviceSide
        ]);
    }
    
    private function saveSet($id, $sets, $status, $winner)
    {
        $stmt = $this->db->prepare("
            UPDATE matches 
            SET sets_data = :sets, status = :status, winner = :winner,
                current_p1 = 0, current_p2 = 0, updated_at = NOW()
            WHERE id = :id
        ");
        
        $stmt->execute([
            ':id' => $id,
            ':sets' => json_encode($sets),
            ':status' => $status,
            ':winner' => $winner
        ]);
    }
    
    private function saveSetAndReset($id, $sets, $currentSet, $server)
    {
        $stmt = $this->db->prepare("
            UPDATE matches 
            SET sets_data = :sets, current_set = :current_set,
                current_p1 = 0, current_p2 = 0, server = :server,
                service_side = 'right', updated_at = NOW()
            WHERE id = :id
        ");
        
        $stmt->execute([
            ':id' => $id,
            ':sets' => json_encode($sets),
            ':current_set' => $currentSet,
            ':server' => $server
        ]);
    }
    
    private function formatMatch($row)
    {
        return [
            'id' => $row['id'],
            'mode' => $row['mode'],
            'player1' => json_decode($row['player1_names'], true),
            'player2' => json_decode($row['player2_names'], true),
            'sets_to_win' => (int)$row['sets_to_win'],
            'points_per_set' => (int)$row['points_per_set'],
            'status' => $row['status'],
            'current_set' => (int)$row['current_set'],
            'server' => (int)$row['server'],
            'service_side' => $row['service_side'],
            'current_score' => [
                'p1' => (int)$row['current_p1'],
                'p2' => (int)$row['current_p2']
            ],
            'sets' => json_decode($row['sets_data'], true) ?: [],
            'winner' => $row['winner'] ? (int)$row['winner'] : null,
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at']
        ];
    }
}
