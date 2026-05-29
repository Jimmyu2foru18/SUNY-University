<?php
// health_check_debug.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Site Health Check - Debugging</h1>";

// 1. Database Connection
require_once 'config/database.php';
echo "<p style='color:green;'>Success: Database connection is stable.</p>";

// 2. Schema Check: List all tables to see what exists
echo "<h3>Database Table List</h3>";
try {
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables currently in database: " . implode(", ", $tables) . "<br>";
    
    if (in_array('About', $tables) || in_array('about', $tables)) {
        echo "<p style='color:green;'>Success: 'About' table exists.</p>";
        
        // Test query again with the exact name found
        $tableName = in_array('About', $tables) ? 'About' : 'about';
        $stmt = $pdo->query("SELECT * FROM $tableName LIMIT 1");
        echo "<p style='color:green;'>Success: Query on '$tableName' successful.</p>";
    } else {
        echo "<p style='color:red;'>FAILED: 'About' table not found in the list above.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red;'>FAILED: Error during schema check: " . $e->getMessage() . "</p>";
}
?>
