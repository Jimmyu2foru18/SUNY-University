<?php
// portal/admin/management/departments.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../../../config/database.php';
require_once '../../../src/controllers/AdminController.php';
require_once '../../../includes/portal_header.php';

$auth = new AdminController($pdo);

// Handle AJAX Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    try {
        if ($_POST['action'] === 'save') {
            $isUpdate = !empty($_POST['is_update']);
            if ($isUpdate) {
                $stmt = $pdo->prepare("UPDATE Department SET departmentName = ?, chairID = ? WHERE departmentID = ?");
                $stmt->execute([$_POST['departmentName'], $_POST['chairID'], $_POST['departmentID']]);
                echo json_encode(['ok' => true, 'message' => 'Department updated.']);
            } else {
                $stmt = $pdo->prepare("INSERT INTO Department (departmentID, departmentName, chairID, roomID) VALUES (?, ?, ?, 'TBA')");
                $stmt->execute([$_POST['departmentID'], $_POST['departmentName'], $_POST['chairID']]);
                echo json_encode(['ok' => true, 'message' => 'Department created.']);
            }
        } elseif ($_POST['action'] === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM Department WHERE departmentID = ?");
            $stmt->execute([$_POST['departmentID']]);
            echo json_encode(['ok' => true, 'message' => 'Department deleted.']);
        }
    } catch (Exception $e) {
        echo json_encode(['ok' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// Fetch Data
$stmt = $pdo->query("SELECT D.*, U.firstName, U.lastName FROM Department D LEFT JOIN User U ON D.chairID = U.userID");
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT F.facultyID as userID, U.firstName, U.lastName FROM Faculty F JOIN User U ON F.facultyID = U.userID");
$faculty = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="portal-container">
    <?php include '../../../includes/portal-sidebar.php'; ?>
    <main class="portal-main">
        <div class="portal-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Academic Departments</h1>
                <button class="btn btn-primary" onclick="showAddModal()">Add New Department</button>
            </div>
            <div class="card border-0 shadow-sm">
                <table class="table table-hover mb-0">
                    <thead><tr><th>ID</th><th>Name</th><th>Chair</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php foreach ($departments as $d): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($d['departmentID']) ?></strong></td>
                                <td><?= htmlspecialchars($d['departmentName']) ?></td>
                                <td><?= htmlspecialchars(($d['firstName'] ?? 'None') . ' ' . ($d['lastName'] ?? '')) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-secondary" onclick='editDept(<?= json_encode($d) ?>)'>Edit</button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteDept('<?= htmlspecialchars($d['departmentID']) ?>')">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- Modal -->
<div id="saveModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
    <div class="modal-content bg-white p-4 rounded shadow" style="width: 500px;">
        <h3 id="modalTitle">Department Info</h3>
        <form id="saveForm">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="is_update" id="is_update" value="">
            <div class="mb-3">
                <label>Department ID</label>
                <input type="text" name="departmentID" id="dept_id" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Department Name</label>
                <input type="text" name="departmentName" id="dept_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Chair (Faculty)</label>
                <select name="chairID" id="dept_chair" class="form-select">
                    <option value="">Select Chair...</option>
                    <?php foreach ($faculty as $f): ?>
                        <option value="<?= $f['userID'] ?>"><?= htmlspecialchars($f['firstName'] . ' ' . $f['lastName']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="button" class="btn btn-secondary" onclick="hideModal()">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Department</button>
        </form>
    </div>
</div>

<script>
function showAddModal() {
    document.getElementById('modalTitle').textContent = 'Add Department';
    document.getElementById('is_update').value = '';
    document.getElementById('dept_id').readOnly = false;
    document.getElementById('saveForm').reset();
    document.getElementById('saveModal').style.display = 'flex';
}
function editDept(d) {
    document.getElementById('modalTitle').textContent = 'Edit Department';
    document.getElementById('is_update').value = '1';
    document.getElementById('dept_id').value = d.departmentID;
    document.getElementById('dept_id').readOnly = true;
    document.getElementById('dept_name').value = d.departmentName;
    document.getElementById('dept_chair').value = d.chairID || '';
    document.getElementById('saveModal').style.display = 'flex';
}
function hideModal() { document.getElementById('saveModal').style.display = 'none'; }
document.getElementById('saveForm').onsubmit = function(e) {
    e.preventDefault();
    fetch('departments.php', { method: 'POST', body: new FormData(this) })
    .then(r => r.json()).then(res => { alert(res.message); if (res.ok) location.reload(); });
};
function deleteDept(id) {
    if (!confirm('Are you sure?')) return;
    fetch('departments.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: new URLSearchParams({ action: 'delete', departmentID: id }) })
    .then(r => r.json()).then(res => { alert(res.message); if (res.ok) location.reload(); });
}
</script>
<?php require_once '../../../includes/footer.php'; ?>
