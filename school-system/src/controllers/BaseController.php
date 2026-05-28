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
            die("Access Denied.");
        }
    }
}
?>
