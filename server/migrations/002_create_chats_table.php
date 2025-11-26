<?php
require_once __DIR__ . '/../config/database.php';

$sql = "
CREATE TABLE IF NOT EXISTS chats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    is_group TINYINT(1) NOT NULL DEFAULT 0,
    group_name VARCHAR(255) DEFAULT NULL,
    rules TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;
";

$connection->query($sql);
echo "Chats table created.\n";
