<?php
// src/models/User.php

class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Create a new user account (Transactional)
     */
    public function create($userData, $loginData) {
        try {
            $this->pdo->beginTransaction();

            // Insert into User
            $stmt = $this->pdo->prepare("INSERT INTO User (userID, firstName, lastName, userType) VALUES (:userID, :firstName, :lastName, :userType)");
            $stmt->execute([
                'userID' => $userData['userID'],
                'firstName' => $userData['firstName'],
                'lastName' => $userData['lastName'],
                'userType' => $userData['userType']
            ]);

            // Insert into Login
            $stmt = $this->pdo->prepare("INSERT INTO Login (userID, email, password, userType) VALUES (:userID, :email, :password, :userType)");
            $stmt->execute([
                'userID' => $userData['userID'],
                'email' => $loginData['email'],
                'password' => $loginData['password'],
                'userType' => $userData['userType']
            ]);

            $this->pdo->commit();
            return true;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    /**
     * Retrieve a user by their ID, including login information.
     */
    public function findById($userId) {
        $stmt = $this->pdo->prepare("
            SELECT u.*, l.email, l.password, l.userType 
            FROM User u
            JOIN Login l ON u.userID = l.userID
            WHERE u.userID = :userId
        ");
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetch();
    }

    /**
     * Retrieve a user by their email for authentication.
     */
    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("
            SELECT u.*, l.email, l.password, l.userType 
            FROM User u
            JOIN Login l ON u.userID = l.userID
            WHERE l.email = :email
        ");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }
}
?>
