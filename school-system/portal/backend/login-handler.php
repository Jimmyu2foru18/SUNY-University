<?php
/**
 * Unified Login Handler for Bridgeport University Portal
 * Standardized to match the database schema and security requirements.
 */

require_once __DIR__ . '/../../config/database.php';
// Note: session_start() should be handled in a central security/auth file included in db.php if possible, 
// but ensuring it's active here for security.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    
    $email = $input['username'] ?? $_POST['username'] ?? '';
    $password = $input['password'] ?? $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        throw new Exception('Email and password are required');
    }

    // Query Login table
    $stmt = $pdo->prepare("
        SELECT L.userID, L.password, L.userType, U.firstName, U.lastName 
        FROM Login L
        LEFT JOIN User U ON L.userID = U.userID
        WHERE L.email = :email
        LIMIT 1
    ");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify Password using secure hash verification
    if ($user && password_verify($password, $user['password'])) {
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);
        
        // Set session variables
        $_SESSION['user_id'] = $user['userID'];
        $_SESSION['user_type'] = $user['userType'];
        $_SESSION['firstName'] = $user['firstName'] ?? 'User';
        $_SESSION['lastName'] = $user['lastName'] ?? '';

        echo json_encode([
            'success' => true,
            'user' => [
                'id' => $user['userID'],
                'type' => $user['userType'],
                'role' => strtolower($user['userType'] === 'StatStaff' ? 'staff' : $user['userType']),
                'name' => $_SESSION['firstName'] . ' ' . $_SESSION['lastName']
            ]
        ]);
    } else {
        // Generic error message to prevent account enumeration
        echo json_encode([
            'success' => false,
            'message' => 'Invalid email or password'
        ]);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'An authentication error occurred.' // Sanitized error
    ]);
}
?>
