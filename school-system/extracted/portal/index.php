<?php
// portal/index.php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    header("Location: ../public/login.php");
    exit();
}

$role = strtolower($_SESSION['user_type']);
header("Location: $role/index.php");
exit();
?>
