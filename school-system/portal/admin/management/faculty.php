<?php
// portal/admin/management/faculty.php
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
                // Update Faculty
                $stmt = $pdo->prepare("UPDATE Faculty SET rank_ = ?, facultyType = ?, office = ?, specialty = ?, status = ? WHERE facultyID = ?");
                $stmt->execute([$_POST['rank_'], $_POST['facultyType'], $_POST['office'], $_POST['specialty'], $_POST['status'], $_POST['facultyID']]);
                
                // Update User
                $stmt = $pdo->prepare("UPDATE User SET firstName = ?, lastName = ? WHERE userID = ?");
                $stmt->execute([$_POST['firstName'], $_POST['lastName'], $_POST['facultyID']]);
                echo json_encode(['ok' => true, 'message' => 'Faculty updated.']);
            } else {
                // Simplified Create (Admin only)
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("INSERT INTO User (firstName, lastName, userType) VALUES (?, ?, 'Faculty')");
                $stmt->execute([$_POST['firstName'], $_POST['lastName']]);
                $newID = $pdo->lastInsertId();
                $email = strtolower($_POST['firstName'] . '.' . $_POST['lastName'] . '@bridgeport.edu');
                $stmt = $pdo->prepare("INSERT INTO Login (userID, email, password, userType) VALUES (?, ?, ?, 'Faculty')");
                $stmt->execute([$newID, $email, password_hash($_POST['password'], PASSWORD_DEFAULT)]);
                $stmt = $pdo->prepare("INSERT INTO Faculty (facultyID, rank_, facultyType, office, specialty, status) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$newID, $_POST['rank_'], $_POST['facultyType'], $_POST['office'], $_POST['specialty'], $_POST['status']]);
                $pdo->commit();
                echo json_encode(['ok' => true, 'message' => 'Faculty added.']);
            }
        } elseif ($_POST['action'] === 'delete') {
            // Delete Faculty (Cascading logic needed or manual cleanup)
            $pdo->prepare("DELETE FROM Faculty WHERE facultyID = ?")->execute([$_POST['userID']]);
            $pdo->prepare("DELETE FROM Login WHERE userID = ?")->execute([$_POST['userID']]);
            $pdo->prepare("DELETE FROM User WHERE userID = ?")->execute([$_POST['userID']]);
            echo json_encode(['ok' => true, 'message' => 'Faculty deleted.']);
        }
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        echo json_encode(['ok' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// Fetch Data
$stmt = $pdo->query("
    SELECT U.userID, U.firstName, U.lastName, L.email, F.rank_, F.facultyType, F.specialty, F.office, FD.departmentID
    FROM User U
    JOIN Faculty F ON U.userID = F.facultyID
    JOIN Login L ON U.userID = L.userID
    LEFT JOIN FacultyDepartment FD ON F.facultyID = FD.facultyID
");
$faculty = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT departmentID, departmentName FROM Department");
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="portal-container">
    <?php include '../../../includes/portal-sidebar.php'; ?>
    <main class="portal-main">
        <div class="portal-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Faculty Management</h1>
                <button class="btn btn-primary" onclick="showAddModal()">Add New Faculty</button>
            </div>
            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <input type="text" id="facultySearch" class="form-control" placeholder="Search faculty...">
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="facultyTable">
                        <thead class="table-light">
                            <tr><th>ID</th><th>Name</th><th>Email</th><th>Rank</th><th>Status</th><th>Actions</th></tr>
                        </thead>
                        <tbody id="facultyBody">
                            <?php foreach ($faculty as $f): ?>
                                <tr class="faculty-row">
                                    <td><strong><?= htmlspecialchars($f['userID']) ?></strong></td>
                                    <td><?= htmlspecialchars($f['firstName'] . ' ' . $f['lastName']) ?></td>
                                    <td><?= htmlspecialchars($f['email']) ?></td>
                                    <td><?= htmlspecialchars($f['rank_']) ?></td>
                                    <td>N/A</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick='editFaculty(<?= json_encode($f) ?>)'>Edit</button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteFaculty('<?= htmlspecialchars($f['userID']) ?>')">Delete</button>
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
<div id="facultyModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
    <div class="modal-content bg-white p-4 rounded shadow" style="width: 500px;">
        <h3 id="modalTitle">Add New Faculty</h3>
        <form id="facultyForm">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="is_update" id="is_update" value="">
            <input type="hidden" name="facultyID" id="f_id" value="">
            
            <div class="mb-3">
                <label>First Name</label>
                <input type="text" name="firstName" id="f_first" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Last Name</label>
                <input type="text" name="lastName" id="f_last" class="form-control" required>
            </div>
            <div class="mb-3" id="passwordField">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Rank</label>
                    <input type="text" name="rank_" id="f_rank" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Type</label>
                    <select name="facultyType" id="f_type" class="form-select">
                        <option value="Full-Time">Full-Time</option>
                        <option value="Part-Time">Part-Time</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label>Status</label>
                <select name="status" id="f_status" class="form-select">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button type="button" class="btn btn-secondary" onclick="hideModal()">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Faculty</button>
        </form>
    </div>
</div>

<script>
function showAddModal() {
    document.getElementById('modalTitle').textContent = 'Add New Faculty';
    document.getElementById('is_update').value = '';
    document.getElementById('passwordField').style.display = 'block';
    document.getElementById('facultyForm').reset();
    document.getElementById('facultyModal').style.display = 'flex';
}
function editFaculty(f) {
    document.getElementById('modalTitle').textContent = 'Edit Faculty';
    document.getElementById('is_update').value = '1';
    document.getElementById('f_id').value = f.userID;
    document.getElementById('f_first').value = f.firstName;
    document.getElementById('f_last').value = f.lastName;
    document.getElementById('passwordField').style.display = 'none';
    document.getElementById('f_rank').value = f.rank_;
    document.getElementById('f_type').value = f.facultyType;
    document.getElementById('f_status').value = f.status;
    document.getElementById('facultyModal').style.display = 'flex';
}
function hideModal() { document.getElementById('facultyModal').style.display = 'none'; }
document.getElementById('facultyForm').onsubmit = function(e) {
    e.preventDefault();
    fetch('faculty.php', { method: 'POST', body: new FormData(this) })
    .then(r => r.json()).then(res => { alert(res.message); if (res.ok) location.reload(); });
};
function deleteFaculty(id) {
    if (!confirm('Are you sure?')) return;
    fetch('faculty.php', { method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: new URLSearchParams({ action: 'delete', userID: id }) })
    .then(r => r.json()).then(res => { alert(res.message); if (res.ok) location.reload(); });
}
document.getElementById('facultySearch').addEventListener('input', function(e) {
    const query = e.target.value.toLowerCase();
    document.querySelectorAll('.faculty-row').forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(query) ? '' : 'none';
    });
});
</script>
<?php require_once '../../../includes/footer.php'; ?>
