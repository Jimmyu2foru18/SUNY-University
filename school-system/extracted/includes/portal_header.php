<?php // includes/portal_header.php ?>
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
    <title>University Portal | SUNY</title>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="/public/index.php">SUNY PORTAL</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#portalNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="portalNav">
                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="/public/index.php">Public Site</a>
                    <a class="nav-link" href="/portal/index.php">My Home</a>
                    <a class="nav-link btn btn-outline-dark px-3 ms-lg-2" href="/public/logout.php">Logout</a>
                </div>
            </div>
        </div>
    </nav>
    <div class="container my-5">
