<?php
// portal/admin/management/account_security.php
require_once '../../../config/database.php';
require_once '../../../src/controllers/BaseController.php';
require_once '../../../includes/portal_header.php';

$auth = new BaseController($pdo);
$auth->checkRole(['Admin']);

// Restrict to Super Admin (adminType = 2)
$stmt = $pdo->prepare("SELECT adminType FROM Admin WHERE adminID = :id");
$stmt->execute(['id' => $_SESSION['user_id']]);
$admin = $stmt->fetch();
if (!$admin || $admin['adminType'] != 2) {
    die("Access Denied: Only the Super Admin can access this page.");
}

// Handle lock/unlock actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userID = $_POST['userID'];
    $action = $_POST['action'];

    if ($action === 'unlock') {
        $stmt = $pdo->prepare("UPDATE Login SET failed_attempts = 0, lockout_until = NULL WHERE userID = :id");
        $stmt->execute(['id' => $userID]);
    } elseif ($action === 'lock') {
        $stmt = $pdo->prepare("UPDATE Login SET lockout_until = '2099-01-01 00:00:00' WHERE userID = :id");
        $stmt->execute(['id' => $userID]);
    }
}

$stmt = $pdo->query("SELECT userID, email, userType, failed_attempts, lockout_until FROM Login");
$users = $stmt->fetchAll();
?>
<div class="container my-5">
    <a href="../index.php" class="btn btn-outline-secondary mb-3">&larr; Dashboard</a>
    <h2 class="fw-bold mb-4">Account Security Management</h2>
    <table class="table table-striped shadow-sm">
        <thead class="table-light">
            <tr><th>Email</th><th>User Type</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['userType']) ?></td>
                    <td><?= $u['lockout_until'] ? '<span class="text-danger fw-bold">LOCKED</span>' : '<span class="text-success fw-bold">ACTIVE</span>' ?></td>
                    <td>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="userID" value="<?= $u['userID'] ?>">
                            <?php if ($u['lockout_until']): ?>
                                <button type="submit" name="action" value="unlock" class="btn btn-sm btn-success">Unlock</button>
                            <?php else: ?>
                                <button type="submit" name="action" value="lock" class="btn btn-sm btn-danger">Lock</button>
                            <?php endif; ?>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once '../../../includes/footer.php'; ?>
