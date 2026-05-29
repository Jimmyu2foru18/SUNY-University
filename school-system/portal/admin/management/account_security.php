<?php
// portal/admin/management/account_security.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../../../config/database.php';
require_once '../../../src/controllers/AdminController.php';
require_once '../../../includes/portal_header.php';

$auth = new AdminController($pdo);
$userID = $_SESSION['user_id'];

// Check Super Admin privileges (adminType = 2)
$stmt = $pdo->prepare("SELECT adminType FROM Admin WHERE adminID = :id");
$stmt->execute(['id' => $userID]);
if ($stmt->fetchColumn() != 2) {
    die("Access Denied: Super Admin privileges required.");
}

// Handle AJAX actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'unlock') {
    header('Content-Type: application/json');
    try {
        $stmt = $pdo->prepare("UPDATE Login SET failed_attempts = 0, lockout_until = NULL WHERE email = ?");
        $stmt->execute([$_POST['email']]);
        echo json_encode(['ok' => true, 'message' => 'Account unlocked successfully.']);
    } catch (Exception $e) {
        echo json_encode(['ok' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// Fetch Locked Accounts
$stmt = $pdo->query("
    SELECT U.userID, U.firstName, U.lastName, L.email, L.failed_attempts, L.lockout_until
    FROM Login L
    JOIN User U ON L.userID = U.userID
    WHERE L.lockout_until IS NOT NULL
    ORDER BY L.lockout_until DESC
");
$locked = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="portal-container">
    <?php include '../../../includes/portal-sidebar.php'; ?>
    <main class="portal-main">
        <div class="portal-content">
            <h1>Account Lockouts</h1>
            <p class="subtitle">Unlock user accounts that have been restricted due to failed login attempts.</p>

            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>User ID</th><th>Name</th><th>Email</th><th>Failed Attempts</th><th>Lockout Until</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php if (empty($locked)): ?>
                                <tr><td colspan="6" class="text-center">No accounts are currently locked.</td></tr>
                            <?php else: ?>
                                <?php foreach ($locked as $l): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($l['userID']) ?></td>
                                        <td><?= htmlspecialchars($l['firstName'] . ' ' . $l['lastName']) ?></td>
                                        <td><?= htmlspecialchars($l['email']) ?></td>
                                        <td><?= htmlspecialchars($l['failed_attempts']) ?></td>
                                        <td><?= htmlspecialchars(date('M d, h:i A', strtotime($l['lockout_until']))) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-success" onclick="unlockAccount('<?= htmlspecialchars($l['email']) ?>')">Unlock</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once '../../../includes/footer.php'; ?>

<script>
function unlockAccount(email) {
    if (!confirm('Unlock this account?')) return;
    fetch('account_security.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ action: 'unlock', email: email })
    })
    .then(r => r.json())
    .then(res => {
        alert(res.message);
        if (res.ok) location.reload();
    });
}
</script>
