<?php
/**
 * Unified Password Reset Handler for Bridgeport University Portal
 */
require_once __DIR__ . '/../../config/database.php';
header('Content-Type: application/json');

try {
    // Only allow POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['email']) || empty($input['email'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email is required']);
        exit;
    }

    $email = filter_var($input['email'], FILTER_SANITIZE_EMAIL);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit;
    }

    // Check if user exists in database
    $stmt = $pdo->prepare("
        SELECT U.userID, U.firstName, U.lastName 
        FROM User U 
        JOIN Login L ON U.userID = L.userID 
        WHERE L.email = ?
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // For security: don't reveal whether email exists or not
        echo json_encode([
            'success' => true,
            'message' => 'If this email exists in our system, you will receive a password reset link shortly.'
        ]);
        error_log("Password reset requested for non-existent email: " . $email);
        exit;
    }

    // Generate reset token (valid for 24 hours)
    $resetToken = bin2hex(random_bytes(32));
    $resetTokenHash = hash('sha256', $resetToken);
    $expiresAt = date('Y-m-d H:i:s', time() + (24 * 3600));

    // Store reset token in database
    $updateStmt = $pdo->prepare(
        "UPDATE Login SET reset_token = ?, reset_token_expires = ? WHERE userID = ?"
    );
    $updateStmt->execute([$resetTokenHash, $expiresAt, $user['userID']]);

    // In production, send email with reset link
    $resetLink = "https://bridgeportuniversity.infinityfree.me/public/reset-password.php?token=" . $resetToken;

    error_log("Password reset requested for user: " . $user['firstName'] . " " . $user['lastName'] . " (" . $email . ")");
    error_log("Reset link would be: " . $resetLink);

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Password reset link has been sent to your email.'
    ]);

} catch (Exception $e) {
    error_log("Password reset error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred. Please try again later.'
    ]);
}
?>
