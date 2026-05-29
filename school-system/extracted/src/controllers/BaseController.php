<?php
// src/controllers/BaseController.php

abstract class BaseController {
    protected $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->checkAuth();
    }

    protected function checkAuth() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../../public/login.php");
            exit();
        }
    }

    protected function checkRole($allowedRoles) {
        if (!in_array($_SESSION['user_type'], $allowedRoles)) {
            $this->renderError("Access Denied", "You do not have the required permissions to view this page.");
        }
    }

    protected function renderError($title, $message) {
        require_once __DIR__ . '/../../includes/portal_header.php';
        echo "
        <div class='container my-5 text-center'>
            <div class='card p-5 border-0 shadow-sm'>
                <h1 class='display-1 fw-bold text-danger mb-4'>Oops!</h1>
                <h2 class='fw-bold mb-3'>$title</h2>
                <p class='text-muted lead mb-5'>$message</p>
                <div>
                    <a href='index.php' class='btn btn-primary px-5'>Return to Dashboard</a>
                </div>
            </div>
        </div>";
        require_once __DIR__ . '/../../includes/footer.php';
        exit();
    }
}
?>
