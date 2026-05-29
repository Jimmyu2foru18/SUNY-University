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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/public/assets/css/style.css" rel="stylesheet">
    <title>SUNY University</title>
</head>
<body>
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="/public/index.php">SUNY UNIVERSITY</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="/public/index.php">Home</a>
                    <a class="nav-link" href="/public/about.php">About</a>
                    <a class="nav-link" href="/public/admissions.php">Admissions</a>
                    <a class="nav-link" href="/public/programs.php">Programs</a>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a class="nav-link btn btn-primary px-3 ms-lg-3" href="/portal/<?= strtolower($_SESSION['user_type']) ?>/index.php">Dashboard</a>
                        <a class="nav-link" href="/public/logout.php">Logout</a>
                    <?php else: ?>
                        <a class="nav-link" href="/public/login.php">Login</a>
                        <a class="nav-link btn btn-primary px-4 ms-lg-3" href="/public/register.php">Apply Now</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
