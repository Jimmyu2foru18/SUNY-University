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

        if (!$user) {
            return false;
        }

        // Check if account is locked
        if ($user['lockout_until'] && strtotime($user['lockout_until']) > time()) {
            $_SESSION['login_error'] = "Account is locked until " . $user['lockout_until'];
            return false;
        }

        if ($password === $user['password']) {
            // Success: Reset failed attempts
            $stmt = $this->userModel->getPDO()->prepare("UPDATE Login SET failed_attempts = 0, lockout_until = NULL WHERE userID = :id");
            $stmt->execute(['id' => $user['userID']]);

            $_SESSION['user_id'] = $user['userID'];
            $_SESSION['user_type'] = $user['userType'];
            return true;
        } else {
            // Failure: Increment failed attempts
            $attempts = $user['failed_attempts'] + 1;
            $lockout = null;
            
            // Auto-lock after 5 attempts for 15 minutes (example)
            if ($attempts >= 5) {
                $lockout = date('Y-m-d H:i:s', strtotime('+15 minutes'));
            }

            $stmt = $this->userModel->getPDO()->prepare("UPDATE Login SET failed_attempts = :attempts, lockout_until = :lockout WHERE userID = :id");
            $stmt->execute(['attempts' => $attempts, 'lockout' => $lockout, 'id' => $user['userID']]);
            
            return false;
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        header("Location: ../public/login.php");
        exit();
    }
}
?>
