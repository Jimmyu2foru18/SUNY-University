<?php
// portal/admin/management/place_hold.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../../../config/database.php';
require_once '../../../src/controllers/AdminController.php';
require_once '../../../includes/portal_header.php';

$auth = new AdminController($pdo);

// Handle AJAX actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_hold') {
    header('Content-Type: application/json');
    try {
        $stmt = $pdo->prepare("INSERT INTO StudentHold (studentID, holdID, dateOfHold) VALUES (?, ?, CURDATE())");
        $stmt->execute([$_POST['studentID'], $_POST['holdID']]);
        echo json_encode(['ok' => true, 'message' => 'Hold placed successfully.']);
    } catch (Exception $e) {
        echo json_encode(['ok' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// Fetch Data for the wizard
$stmt = $pdo->query("SELECT userID, firstName, lastName FROM User WHERE userID IN (SELECT studentID FROM Student)");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT holdID, holdDescription FROM Hold");
$holdTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="portal-container">
    <?php include '../../../includes/portal-sidebar.php'; ?>
    <main class="portal-main">
        <div class="portal-content">
            <h1>Place Academic Hold</h1>
            
            <div class="card p-4 border-0 shadow-sm" style="max-width: 600px;">
                <form id="holdForm">
                    <input type="hidden" name="action" value="save_hold">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Student</label>
                        <select name="studentID" class="form-select" required>
                            <option value="">Choose Student...</option>
                            <?php foreach ($students as $s): ?>
                                <option value="<?= $s['userID'] ?>"><?= htmlspecialchars($s['firstName'] . ' ' . $s['lastName'] . ' (' . $s['userID'] . ')') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Hold Reason</label>
                        <select name="holdID" class="form-select" required>
                            <?php foreach ($holdTypes as $ht): ?>
                                <option value="<?= $ht['holdID'] ?>"><?= htmlspecialchars($ht['holdDescription']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Place Hold</button>
                    <a href="holds.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
document.getElementById('holdForm').onsubmit = function(e) {
    e.preventDefault();
    fetch('place_hold.php', {
        method: 'POST',
        body: new FormData(this)
    })
    .then(r => r.json())
    .then(res => {
        alert(res.message);
        if (res.ok) window.location.href = 'holds.php';
    });
};
</script>
<?php require_once '../../../includes/footer.php'; ?>
