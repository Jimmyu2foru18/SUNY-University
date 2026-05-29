<?php
// public/logout.php
session_start();

// Destroy the session
$_SESSION = [];
session_destroy();

// Redirect to home page
header("Location: index.php");
exit();
?>
