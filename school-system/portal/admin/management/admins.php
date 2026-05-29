<?php
// portal/admin/management/admins.php
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    try {
        if ($_POST['action'] === 'add') {
            $pdo->beginTransaction();
            // 1. Create User
            $stmt = $pdo->prepare("INSERT INTO User (firstName, lastName, userType) VALUES (?, ?, 'Admin')");
            $stmt->execute([$_POST['firstName'], $_POST['lastName']]);
            $newID = $pdo->lastInsertId();
            // 2. Create Login
            $stmt = $pdo->prepare("INSERT INTO Login (userID, email, password, userType) VALUES (?, ?, ?, 'Admin')");
            $stmt->execute([$newID, $_POST['email'], password_hash($_POST['password'], PASSWORD_DEFAULT)]);
            // 3. Create Admin record
            $stmt = $pdo->prepare("INSERT INTO Admin (adminID, adminType) VALUES (?, ?)");
            $stmt->execute([$newID, $_POST['adminType']]);
            $pdo->commit();
            echo json_encode(['ok' => true, 'message' => 'Admin added successfully.']);
        } elseif ($_POST['action'] === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM Admin WHERE adminID = ?");
            $stmt->execute([$_POST['userID']]);
            $stmt = $pdo->prepare("DELETE FROM Login WHERE userID = ?");
            $stmt->execute([$_POST['userID']]);
            $stmt = $pdo->prepare("DELETE FROM User WHERE userID = ?");
            $stmt->execute([$_POST['userID']]);
            echo json_encode(['ok' => true, 'message' => 'Admin removed successfully.']);
        }
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        echo json_encode(['ok' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// Fetch Admins
$stmt = $pdo->query("
    SELECT U.userID, U.firstName, U.lastName, L.email, A.adminType 
    FROM User U
    JOIN Admin A ON U.userID = A.adminID
    JOIN Login L ON U.userID = L.userID
    ORDER BY U.lastName ASC
");
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="portal-container">
    <?php include '../../../includes/portal-sidebar.php'; ?>
    <main class="portal-main">
        <div class="portal-content">
            <h1>Administrator Management</h1>
            <button class="btn btn-primary mb-4" onclick="showAddModal()">Add New Admin</button>

            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>ID</th><th>Name</th><th>Email</th><th>Level</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($admins as $a): ?>
                                <tr>
                                    <td><?= htmlspecialchars($a['userID']) ?></td>
                                    <td><?= htmlspecialchars($a['firstName'] . ' ' . $a['lastName']) ?></td>
                                    <td><?= htmlspecialchars($a['email']) ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= $a['adminType'] == 2 ? 'Super Admin' : 'Staff Admin'; ?></span></td>
                                    <td>
                                        <?php if ($a['userID'] != $userID): ?>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteAdmin('<?= htmlspecialchars($a['userID']) ?>')">Remove</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modal -->
<div id="addModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
    <div class="modal-content bg-white p-4 rounded shadow" style="width: 400px;">
        <h3>Add Administrator</h3>
        <form id="addForm" style="margin-top: 20px;">
            <input type="hidden" name="action" value="add">
            <div class="mb-3">
                <label class="form-label">First Name</label>
                <input type="text" name="firstName" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Last Name</label>
                <input type="text" name="lastName" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Admin Level</label>
                <select name="adminType" class="form-select">
                    <option value="1">Staff Admin (View Only)</option>
                    <option value="2">Super Admin (Full Access)</option>
                </select>
            </div>
            <button type="button" class="btn btn-secondary" onclick="hideAddModal()">Cancel</button>
            <button type="submit" class="btn btn-primary">Create Admin</button>
        </form>
    </div>
</div>

<script>
function showAddModal() { document.getElementById('addModal').style.display = 'flex'; }
function hideAddModal() { document.getElementById('addModal').style.display = 'none'; }

document.getElementById('addForm').onsubmit = function(e) {
    e.preventDefault();
    fetch('admins.php', {
        method: 'POST',
        body: new FormData(this)
    })
    .then(r => r.json())
    .then(res => {
        alert(res.message);
        if (res.ok) location.reload();
    });
};

function deleteAdmin(id) {
    if (!confirm('Are you sure?')) return;
    fetch('admins.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ action: 'delete', userID: id })
    })
    .then(r => r.json())
    .then(res => {
        alert(res.message);
        if (res.ok) location.reload();
    });
}
</script>
<?php require_once '../../../includes/footer.php'; ?>
