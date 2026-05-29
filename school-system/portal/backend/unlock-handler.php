<?php
/**
 * Unified Unlock Handler for Bridgeport University Portal
 */

require_once __DIR__ . '/../../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

try {
    // 1. Authorization: Only allow POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // 2. Authorization: Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Unauthorized');
    }

    // 3. Authorization: Check for Super Admin privileges (adminType = 2)
    $stmt = $pdo->prepare("SELECT adminType FROM Admin WHERE adminID = ? LIMIT 1");
    $stmt->execute([$_SESSION['user_id']]);
    $adminType = (int)$stmt->fetchColumn();

    if ($adminType !== 2) {
        throw new Exception('Unauthorized: Super Admin privileges required');
    }

    // 4. Process Request
    $input = json_decode(file_get_contents('php://input'), true);
    $email = $input['email'] ?? null;
    $userID = $input['userID'] ?? null;

    if (!$email && !$userID) {
        throw new Exception('Provide email or userID');
    }

    // 5. Update Lockout Status
    if ($email) {
        $upd = $pdo->prepare("UPDATE Login SET failed_attempts = 0, lockout_until = NULL WHERE email = ? LIMIT 1");
        $upd->execute([$email]);
    } else {
        $upd = $pdo->prepare("UPDATE Login SET failed_attempts = 0, lockout_until = NULL WHERE userID = ? LIMIT 1");
        $upd->execute([$userID]);
    }

    echo json_encode(['success' => true, 'message' => 'Account lockout cleared successfully.']);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
