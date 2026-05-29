<?php
// portal/statstaff/views/test_connection.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Debug: StatStaff View Connection</h1>";

$configPath = '../../../config/database.php';

if (file_exists($configPath)) {
    echo "Config file found at: $configPath<br>";
    try {
        require_once $configPath;
        echo "Database connection success.<br>";
        
        // Test query
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll();
        echo "Tables accessible: " . count($tables) . "<br>";
    } catch (Exception $e) {
        echo "<b style='color:red;'>Connection Error: " . $e->getMessage() . "</b><br>";
    }
} else {
    echo "<b style='color:red;'>Config file NOT found at: $configPath</b><br>";
}
?>
