<?php
// config/database.php

$host = 'sql110.infinityfree.com';
$db   = 'if0_42004206_school';
$user = 'if0_42004206';
$pass = '65VG2nofiAmPct';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     // Fix for MAX_JOIN_SIZE error on shared hosting
     $pdo->exec("SET SQL_BIG_SELECTS=1");
} catch (\PDOException $e) {
     // Explicitly show error for debugging
     die("Database connection failed: " . $e->getMessage());
}
?>
