CREATE DATABASE IF NOT EXISTS badminton_scorer CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE badminton_scorer;

CREATE TABLE IF NOT EXISTS matches (
    id VARCHAR(8) PRIMARY KEY,
    mode ENUM('singles', 'doubles') NOT NULL DEFAULT 'doubles',
    player1_names JSON NOT NULL,
    player2_names JSON NOT NULL,
    sets_to_win TINYINT NOT NULL DEFAULT 3,
    points_per_set TINYINT NOT NULL DEFAULT 21,
    status ENUM('active', 'completed', 'abandoned') NOT NULL DEFAULT 'active',
    current_set TINYINT NOT NULL DEFAULT 1,
    server TINYINT NOT NULL DEFAULT 1 COMMENT '1 or 2',
    service_side ENUM('left', 'right') NOT NULL DEFAULT 'right',
    current_p1 TINYINT NOT NULL DEFAULT 0,
    current_p2 TINYINT NOT NULL DEFAULT 0,
    sets_data JSON NOT NULL COMMENT 'Array of sets with scores and winner',
    winner TINYINT NULL COMMENT '1 or 2, NULL if not ended',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ended_at DATETIME NULL,
    
    INDEX idx_status_updated (status, updated_at),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
