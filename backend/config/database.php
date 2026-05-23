<?php
function getDB() {
    static $db = null;
    
    if ($db === null) {
        // Try getenv first (Docker/local), fallback to $_SERVER (Apache SetEnv)
        $host = getenv('DB_HOST') ?: ($_SERVER['DB_HOST'] ?? 'db');
        $dbname = getenv('DB_NAME') ?: ($_SERVER['DB_NAME'] ?? 'badminton_scorer');
        $username = getenv('DB_USER') ?: ($_SERVER['DB_USER'] ?? 'root');
        $password = getenv('DB_PASS') ?: ($_SERVER['DB_PASS'] ?? 'rootpass');
        
        try {
            $db = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            throw new Exception('Database connection failed: ' . $e->getMessage());
        }
    }
    
    return $db;
}
