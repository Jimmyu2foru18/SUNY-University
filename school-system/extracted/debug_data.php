<?php
require_once 'config/database.php';

$tables = ['Student', 'Faculty', 'Major', 'Department', 'Semester'];
$results = [];

foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
        $results[$table] = $stmt->fetchColumn();
    } catch (Exception $e) {
        $results[$table] = "Error: " . $e->getMessage();
    }
}

// Get some sample majors
try {
    $stmt = $pdo->query("SELECT majorName FROM Major LIMIT 3");
    $results['SampleMajors'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $results['SampleMajors'] = "Error";
}

echo json_encode($results, JSON_PRETTY_PRINT);
?>
