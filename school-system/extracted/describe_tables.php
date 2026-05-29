<?php
require_once 'config/database.php';
try {
    echo "--- Department Table ---\n";
    $stmt = $pdo->query("DESCRIBE Department");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    
    echo "\n--- Major Table ---\n";
    $stmt = $pdo->query("DESCRIBE Major");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
