<?php
// includes/public_header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>SUNY University</title>
</head>
<body class="container">
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
        <a class="navbar-brand" href="/public/index.php">SUNY University</a>
        <div class="navbar-nav">
            <a class="nav-link" href="/public/index.php">Home</a>
            <a class="nav-link" href="/public/about.php">About</a>
            <a class="nav-link" href="/public/admissions.php">Admissions</a>
            <a class="nav-link" href="/public/programs.php">Programs</a>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <a class="nav-link" href="/portal/<?= strtolower($_SESSION['user_type']) ?>/index.php">My Dashboard</a>
                <a class="nav-link" href="/public/logout.php">Logout</a>
            <?php else: ?>
                <a class="nav-link" href="/public/login.php">Login</a>
                <a class="nav-link" href="/public/register.php">Register</a>
            <?php endif; ?>
        </div>
    </nav>
    <hr>
