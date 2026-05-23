<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/MatchModel.php';

class MatchReadTest extends TestCase
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

    public function testGetMatchByIdReturnsCorrectData(): void
    {
        $data = [
            'mode' => 'singles',
            'player1' => ['Juan'],
            'player2' => ['Pedro'],
            'sets_to_win' => 3,
            'points_per_set' => 21
        ];

        $created = $this->matchModel->create($data);
        $match = $this->matchModel->getById($created['id']);

        $this->assertNotNull($match);
        $this->assertEquals($created['id'], $match['id']);
        $this->assertEquals('singles', $match['mode']);
        $this->assertEquals(['Juan'], $match['player1']);
        $this->assertEquals(['Pedro'], $match['player2']);
        $this->assertEquals(2, $match['sets_to_win']);
        $this->assertEquals(21, $match['points_per_set']);
        $this->assertEquals('active', $match['status']);
        $this->assertEquals(1, $match['current_set']);
        $this->assertEquals(1, $match['server']);
        $this->assertEquals('right', $match['service_side']);
        $this->assertEquals(['p1' => 0, 'p2' => 0], $match['current_score']);
        $this->assertEmpty($match['sets']);
        $this->assertNull($match['winner']);
    }

    public function testGetMatchByIdReturnsNullForNonExistent(): void
    {
        $match = $this->matchModel->getById('NONEXIST');
        $this->assertNull($match);
    }
}
