<?php
// src/controllers/AuthController.php
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $userModel;

    public function __construct($pdo) {
        $this->userModel = new User($pdo);
    }

    public function register($userData, $loginData) {
        return $this->userModel->create($userData, $loginData);
    }

    public function login($email, $password) {
        $user = $this->userModel->findByEmail($email);

        if ($user && $password === $user['password']) {
            $_SESSION['user_id'] = $user['userID'];
            $_SESSION['user_type'] = $user['userType'];
            return true;
        }

        return false;
    }

    public function logout() {
        session_start();
        session_destroy();
        header("Location: ../public/login.php");
        exit();
    }
}
?>
