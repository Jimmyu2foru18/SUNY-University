<?php
// portal/admin/management/holds.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../../../config/database.php';
require_once '../../../src/controllers/AdminController.php';
require_once '../../../includes/portal_header.php';

$auth = new AdminController($pdo);

// Handle AJAX actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    try {
        if ($_POST['action'] === 'add') {
            $stmt = $pdo->prepare("INSERT INTO StudentHold (studentID, holdID) VALUES (?, ?)");
            $stmt->execute([$_POST['studentID'], $_POST['holdID']]);
            echo json_encode(['ok' => true, 'message' => 'Hold placed successfully.']);
        } elseif ($_POST['action'] === 'remove') {
            $stmt = $pdo->prepare("DELETE FROM StudentHold WHERE studentID = ? AND holdID = ?");
            $stmt->execute([$_POST['studentID'], $_POST['holdID']]);
            echo json_encode(['ok' => true, 'message' => 'Hold removed successfully.']);
        }
    } catch (Exception $e) {
        echo json_encode(['ok' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// Fetch Data
$stmt = $pdo->query("
    SELECT SH.studentID, U.firstName, U.lastName, SH.holdID, H.holdDescription, '2026-05-28' as dateOfHold
    FROM StudentHold SH
    JOIN User U ON SH.studentID = U.userID
    JOIN Hold H ON SH.holdID = H.holdID
");
$holds = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT userID, firstName, lastName FROM User WHERE userID IN (SELECT studentID FROM Student)");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM Hold");
$holdTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="portal-container">
    <?php include '../../../includes/portal-sidebar.php'; ?>
    <main class="portal-main">
        <div class="portal-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Academic Holds Management</h1>
                <button class="btn btn-primary" onclick="showAddModal()">Place New Hold</button>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>Student</th><th>ID</th><th>Description</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($holds as $h): ?>
                                <tr>
                                    <td><?= htmlspecialchars($h['firstName'] . ' ' . $h['lastName']) ?></td>
                                    <td><?= htmlspecialchars($h['studentID']) ?></td>
                                    <td><?= htmlspecialchars($h['holdDescription']) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-danger" onclick="removeHold('<?= htmlspecialchars($h['studentID']) ?>', '<?= htmlspecialchars($h['holdID']) ?>')">Remove</button>
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

<!-- Add Modal -->
<div id="addModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
    <div class="modal-content bg-white p-4 rounded shadow" style="width: 500px;">
        <h3>Place Academic Hold</h3>
        <form id="addForm" style="margin-top: 20px;">
            <input type="hidden" name="action" value="add">
            <div class="mb-3">
                <label class="form-label">Select Student</label>
                <select name="studentID" class="form-select" required>
                    <?php foreach ($students as $s): ?>
                        <option value="<?= $s['userID'] ?>"><?= htmlspecialchars($s['firstName'] . ' ' . $s['lastName'] . ' (' . $s['userID'] . ')') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Hold Reason</label>
                <select name="holdID" class="form-select" required>
                    <?php foreach ($holdTypes as $ht): ?>
                        <option value="<?= $ht['holdID'] ?>"><?= htmlspecialchars($ht['holdDescription']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="button" class="btn btn-secondary" onclick="hideModal()">Cancel</button>
            <button type="submit" class="btn btn-primary">Place Hold</button>
        </form>
    </div>
</div>

<script>
function showAddModal() { document.getElementById('addModal').style.display = 'flex'; }
function hideModal() { document.getElementById('addModal').style.display = 'none'; }

document.getElementById('addForm').onsubmit = function(e) {
    e.preventDefault();
    fetch('holds.php', {
        method: 'POST',
        body: new FormData(this)
    })
    .then(r => r.json())
    .then(res => {
        alert(res.message);
        if (res.ok) location.reload();
    });
};

function removeHold(studentID, holdID) {
    if (!confirm('Are you sure you want to remove this hold?')) return;
    fetch('holds.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ action: 'remove', studentID: studentID, holdID: holdID })
    })
    .then(r => r.json())
    .then(res => {
        alert(res.message);
        if (res.ok) location.reload();
    });
}
</script>
<?php require_once '../../../includes/footer.php'; ?>
