<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/MatchModel.php';

class MatchCreateTest extends TestCase
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

    public function testCreateMatchGenerates8CharId(): void
    {
        $data = [
            'mode' => 'doubles',
            'player1' => ['Juan', 'Maria'],
            'player2' => ['Pedro', 'Ana'],
            'sets_to_win' => 3,
            'points_per_set' => 21
        ];

        $match = $this->matchModel->create($data);

        $this->assertNotNull($match['id']);
        $this->assertEquals(8, strlen($match['id']));
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]{8}$/', $match['id']);
    }

    public function testCreateMatchStoresCorrectData(): void
    {
        $data = [
            'mode' => 'singles',
            'player1' => ['Juan'],
            'player2' => ['Pedro'],
            'sets_to_win' => 3,
            'points_per_set' => 21
        ];

        $match = $this->matchModel->create($data);

        $this->assertEquals('singles', $match['mode']);
        $this->assertEquals(['Juan'], $match['player1']);
        $this->assertEquals(['Pedro'], $match['player2']);
        $this->assertEquals(3, $match['sets_to_win']);
        $this->assertEquals(21, $match['points_per_set']);
        $this->assertEquals('active', $match['status']);
        $this->assertEquals(1, $match['current_set']);
        $this->assertEquals(1, $match['server']);
        $this->assertEquals('right', $match['service_side']);
        $this->assertEquals(['p1' => 0, 'p2' => 0], $match['current_score']);
        $this->assertEmpty($match['sets']);
        $this->assertNull($match['winner']);
    }

    public function testCreateMatchWithDefaults(): void
    {
        $data = [
            'mode' => 'doubles',
            'player1' => ['Juan', 'Maria'],
            'player2' => ['Pedro', 'Ana']
        ];

        $match = $this->matchModel->create($data);

        $this->assertEquals(3, $match['sets_to_win']);
        $this->assertEquals(21, $match['points_per_set']);
    }

    public function testCreateMatchRejectsInvalidMode(): void
    {
        $this->expectException(Exception::class);
        
        $data = [
            'mode' => 'invalid',
            'player1' => ['Juan'],
            'player2' => ['Pedro']
        ];

        $this->matchModel->create($data);
    }

    public function testCreateMatchRejectsEmptyNames(): void
    {
        $this->expectException(Exception::class);
        
        $data = [
            'mode' => 'singles',
            'player1' => [],
            'player2' => ['Pedro']
        ];

        $this->matchModel->create($data);
    }
}
