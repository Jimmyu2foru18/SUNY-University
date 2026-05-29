<?php
// portal/admin/management/programs.php
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
        if ($_POST['action'] === 'save_major') {
            $isUpdate = !empty($_POST['is_update']);
            if ($isUpdate) {
                $stmt = $pdo->prepare("UPDATE Major SET majorName = ?, departmentID = ?, creditsRequired = ? WHERE majorID = ?");
                $stmt->execute([$_POST['majorName'], $_POST['departmentID'], $_POST['creditsRequired'], $_POST['majorID']]);
                echo json_encode(['ok' => true, 'message' => 'Major updated.']);
            } else {
                $stmt = $pdo->prepare("INSERT INTO Major (majorID, majorName, departmentID, creditsRequired) VALUES (?, ?, ?, ?)");
                $stmt->execute([$_POST['majorID'], $_POST['majorName'], $_POST['departmentID'], $_POST['creditsRequired']]);
                echo json_encode(['ok' => true, 'message' => 'Major created.']);
            }
        } elseif ($_POST['action'] === 'delete_major') {
            $stmt = $pdo->prepare("DELETE FROM Major WHERE majorID = ?");
            $stmt->execute([$_POST['majorID']]);
            echo json_encode(['ok' => true, 'message' => 'Major deleted.']);
        }
    } catch (Exception $e) {
        echo json_encode(['ok' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// Fetch Data
$stmt = $pdo->query("SELECT * FROM Major");
$majors = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT departmentID, departmentName FROM Department");
$depts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="portal-container">
    <?php include '../../../includes/portal-sidebar.php'; ?>
    <main class="portal-main">
        <div class="portal-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Academic Programs</h1>
                <button class="btn btn-primary" onclick="showAddModal()">Add Major</button>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr><th>ID</th><th>Name</th><th>Department</th><th>Credits</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($majors as $m): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($m['majorID']) ?></strong></td>
                                    <td><?= htmlspecialchars($m['majorName']) ?></td>
                                    <td><?= htmlspecialchars($m['departmentID']) ?></td>
                                    <td><?= htmlspecialchars($m['creditsRequired']) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-secondary" onclick='editMajor(<?= json_encode($m) ?>)'>Edit</button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteMajor('<?= htmlspecialchars($m['majorID']) ?>')">Delete</button>
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
<div id="majorModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
    <div class="modal-content bg-white p-4 rounded shadow" style="width: 500px;">
        <h3 id="modalTitle">Major Info</h3>
        <form id="majorForm">
            <input type="hidden" name="action" value="save_major">
            <input type="hidden" name="is_update" id="is_update" value="">
            <div class="mb-3">
                <label>Major ID</label>
                <input type="text" name="majorID" id="m_id" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Major Name</label>
                <input type="text" name="majorName" id="m_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Department</label>
                <select name="departmentID" id="m_dept" class="form-select" required>
                    <?php foreach ($depts as $d): ?>
                        <option value="<?= htmlspecialchars($d['departmentID']) ?>"><?= htmlspecialchars($d['departmentName']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label>Credits Required</label>
                <input type="number" name="creditsRequired" id="m_credits" class="form-control" value="120" required>
            </div>
            <button type="button" class="btn btn-secondary" onclick="hideModal()">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Major</button>
        </form>
    </div>
</div>

<script>
function showAddModal() {
    document.getElementById('modalTitle').textContent = 'Add Major';
    document.getElementById('is_update').value = '';
    document.getElementById('m_id').readOnly = false;
    document.getElementById('majorForm').reset();
    document.getElementById('majorModal').style.display = 'flex';
}
function editMajor(m) {
    document.getElementById('modalTitle').textContent = 'Edit Major';
    document.getElementById('is_update').value = '1';
    document.getElementById('m_id').value = m.majorID;
    document.getElementById('m_id').readOnly = true;
    document.getElementById('m_name').value = m.majorName;
    document.getElementById('m_dept').value = m.departmentID;
    document.getElementById('m_credits').value = m.creditsRequired;
    document.getElementById('majorModal').style.display = 'flex';
}
function hideModal() { document.getElementById('majorModal').style.display = 'none'; }
document.getElementById('majorForm').onsubmit = function(e) {
    e.preventDefault();
    fetch('programs.php', { method: 'POST', body: new FormData(this) })
    .then(r => r.json()).then(res => { alert(res.message); if (res.ok) location.reload(); });
};
function deleteMajor(id) {
    if (!confirm('Are you sure?')) return;
    fetch('programs.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: new URLSearchParams({ action: 'delete_major', majorID: id }) })
    .then(r => r.json()).then(res => { alert(res.message); if (res.ok) location.reload(); });
}
</script>
<?php require_once '../../../includes/footer.php'; ?>
