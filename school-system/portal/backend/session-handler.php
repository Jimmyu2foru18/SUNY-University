<?php
/**
 * Session Status Handler
 * Returns the current session state and user info in JSON format.
 */
require_once __DIR__ . '/../../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (isset($_SESSION['user_id'])) {
    $role = strtolower($_SESSION['user_type'] ?? 'student');
    if ($role === 'statstaff') $role = 'staff';
    
    echo json_encode([
        'isLoggedIn' => true,
        'user' => [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['email'] ?? '',
            'role' => $role,
            'name' => ($_SESSION['firstName'] ?? '') . ' ' . ($_SESSION['lastName'] ?? '')
        ]
    ]);
} else {
    echo json_encode([
        'isLoggedIn' => false,
        'user' => null
    ]);
}
?>
