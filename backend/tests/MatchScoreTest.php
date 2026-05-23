<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/MatchModel.php';

class MatchScoreTest extends TestCase
{
    private $db;
    private $matchModel;

    protected function setUp(): void
    {
        $this->db = getDB();
        $this->matchModel = new MatchModel($this->db);
        
        // Clean up test data
        $this->db->exec("DELETE FROM matches WHERE id LIKE 'TEST%'");
    }

    public function testAddPointToPlayer1(): void
    {
        $match = $this->matchModel->create([
            'mode' => 'singles',
            'player1' => ['Juan'],
            'player2' => ['Pedro']
        ]);

        $updated = $this->matchModel->updateScore($match['id'], 1);
        
        $this->assertEquals(['p1' => 1, 'p2' => 0], $updated['current_score']);
        $this->assertEquals(1, $updated['server']); // Server stays with winner
    }

    public function testAddPointToPlayer2ChangesServer(): void
    {
        $match = $this->matchModel->create([
            'mode' => 'singles',
            'player1' => ['Juan'],
            'player2' => ['Pedro']
        ]);

        $updated = $this->matchModel->updateScore($match['id'], 2);
        
        $this->assertEquals(['p1' => 0, 'p2' => 1], $updated['current_score']);
        $this->assertEquals(2, $updated['server']); // Server changes to winner
    }

    public function testWinSetAt21Points(): void
    {
        $match = $this->matchModel->create([
            'mode' => 'singles',
            'player1' => ['Juan'],
            'player2' => ['Pedro']
        ]);

        // Create a match with P1 at 20 points
        $this->db->prepare("UPDATE matches SET current_p1 = 20 WHERE id = :id")
            ->execute([':id' => $match['id']]);

        $updated = $this->matchModel->updateScore($match['id'], 1);
        
        $this->assertEquals(['p1' => 0, 'p2' => 0], $updated['current_score']);
        $this->assertEquals(2, $updated['current_set']);
        $this->assertCount(1, $updated['sets']);
        $this->assertEquals(['p1' => 21, 'p2' => 0], $updated['sets'][0]);
    }

    public function testWinSetRequiresTwoPointDifference(): void
    {
        $match = $this->matchModel->create([
            'mode' => 'singles',
            'player1' => ['Juan'],
            'player2' => ['Pedro']
        ]);

        // Set scores to 20-20
        $this->db->prepare("UPDATE matches SET current_p1 = 20, current_p2 = 20 WHERE id = :id")
            ->execute([':id' => $match['id']]);

        // P1 scores - should be 21-20, set not won yet
        $updated = $this->matchModel->updateScore($match['id'], 1);
        $this->assertEquals(['p1' => 21, 'p2' => 20], $updated['current_score']);
        $this->assertEquals(1, $updated['current_set']);
    }

    public function testDeuceUpTo30(): void
    {
        $match = $this->matchModel->create([
            'mode' => 'singles',
            'player1' => ['Juan'],
            'player2' => ['Pedro']
        ]);

        // Set scores to 29-29
        $this->db->prepare("UPDATE matches SET current_p1 = 29, current_p2 = 29 WHERE id = :id")
            ->execute([':id' => $match['id']]);

        // P1 scores - should win set at 30-29
        $updated = $this->matchModel->updateScore($match['id'], 1);
        
        $this->assertEquals(['p1' => 0, 'p2' => 0], $updated['current_score']);
        $this->assertEquals(2, $updated['current_set']);
        $this->assertEquals(['p1' => 30, 'p2' => 29], $updated['sets'][0]);
    }

    public function testUndoPoint(): void
    {
        $match = $this->matchModel->create([
            'mode' => 'singles',
            'player1' => ['Juan'],
            'player2' => ['Pedro']
        ]);

        // Add 2 points to P1
        $this->matchModel->updateScore($match['id'], 1);
        $this->matchModel->updateScore($match['id'], 1);
        
        // Undo 1 point
        $updated = $this->matchModel->updateScore($match['id'], 1, true);
        
        $this->assertEquals(['p1' => 1, 'p2' => 0], $updated['current_score']);
    }

    public function testUndoAtZeroDoesNothing(): void
    {
        $match = $this->matchModel->create([
            'mode' => 'singles',
            'player1' => ['Juan'],
            'player2' => ['Pedro']
        ]);

        // Undo when score is 0-0
        $updated = $this->matchModel->updateScore($match['id'], 1, true);
        
        $this->assertEquals(['p1' => 0, 'p2' => 0], $updated['current_score']);
    }

    public function testWinMatch(): void
    {
        $match = $this->matchModel->create([
            'mode' => 'singles',
            'player1' => ['Juan'],
            'player2' => ['Pedro'],
            'sets_to_win' => 1,
            'points_per_set' => 5
        ]);

        // Win the only set needed
        for ($i = 0; $i < 5; $i++) {
            $this->matchModel->updateScore($match['id'], 1);
        }
        
        $updated = $this->matchModel->getById($match['id']);
        
        $this->assertEquals('completed', $updated['status']);
        $this->assertEquals(1, $updated['winner']);
        $this->assertEquals(5, $updated['sets'][0]['p1']);
        $this->assertEquals(0, $updated['sets'][0]['p2']);
    }

    public function testUpdateScoreOnCompletedMatchThrowsException(): void
    {
        $this->expectException(Exception::class);
        
        $match = $this->matchModel->create([
            'mode' => 'singles',
            'player1' => ['Juan'],
            'player2' => ['Pedro'],
            'sets_to_win' => 1,
            'points_per_set' => 5
        ]);

        // Win the match
        for ($i = 0; $i < 5; $i++) {
            $this->matchModel->updateScore($match['id'], 1);
        }
        
        // Try to update score on completed match
        $this->matchModel->updateScore($match['id'], 1);
    }
}
