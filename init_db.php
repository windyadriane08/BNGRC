<?php
echo "Starting DB init...\n";
require_once __DIR__ . '/app/bootstrap.php';

echo "Bootstrap loaded.\n";
$pdo = Flight::db();

echo "PDO created.\n";
$sql = file_get_contents(__DIR__ . '/database/schema.sql');
echo "SQL loaded: " . substr($sql, 0, 50) . "...\n";
$pdo->exec($sql);

echo "Database initialized successfully.\n";