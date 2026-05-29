<?php
// health_check.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Site Health Check</h1>";

// 1. PHP Version
echo "PHP Version: " . phpversion() . "<br>";

// 2. Database Connection
echo "<h3>Database Connectivity</h3>";
try {
    require_once 'config/database.php';
    echo "<p style='color:green;'>Success: Database connection is stable.</p>";
} catch (Exception $e) {
    echo "<p style='color:red;'>FAILED: " . $e->getMessage() . "</p>";
}

// 3. Header/Include Check
echo "<h3>File Path Check</h3>";
if (file_exists('includes/public_header.php')) {
    echo "<p style='color:green;'>Success: Header file found.</p>";
} else {
    echo "<p style='color:red;'>FAILED: Header file not found at includes/public_header.php</p>";
}

// 4. Test DB Table (About)
echo "<h3>Database Schema Check</h3>";
try {
    $stmt = $pdo->query("SELECT * FROM About LIMIT 1");
    echo "<p style='color:green;'>Success: 'About' table is accessible.</p>";
} catch (Exception $e) {
    echo "<p style='color:red;'>FAILED: 'About' table query failed. Ensure '50_NewTables.sql' was imported.</p>";
}
?>
