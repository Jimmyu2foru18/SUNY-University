<?php
// portal/admin/management/catalog.php
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
        if ($_POST['action'] === 'save') {
            if (!empty($_POST['is_update'])) {
                $stmt = $pdo->prepare("UPDATE Course SET courseName = ?, credits = ?, courseDescription = ?, departmentID = ?, courseType = ?, passingGrade = ? WHERE courseID = ?");
                $stmt->execute([$_POST['courseName'], $_POST['credits'], $_POST['description'], $_POST['departmentID'], $_POST['courseType'], $_POST['passingGrade'], $_POST['courseID']]);
                echo json_encode(['ok' => true, 'message' => 'Course updated successfully.']);
            } else {
                $stmt = $pdo->prepare("INSERT INTO Course (courseID, courseName, credits, courseDescription, departmentID, courseType, passingGrade) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$_POST['courseID'], $_POST['courseName'], $_POST['credits'], $_POST['description'], $_POST['departmentID'], $_POST['courseType'], $_POST['passingGrade']]);
                echo json_encode(['ok' => true, 'message' => 'Course created successfully.']);
            }
        } elseif ($_POST['action'] === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM Course WHERE courseID = ?");
            $stmt->execute([$_POST['courseID']]);
            echo json_encode(['ok' => true, 'message' => 'Course deleted.']);
        }
    } catch (Exception $e) {
        echo json_encode(['ok' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// Fetch Data
$stmt = $pdo->query("SELECT * FROM Course");
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT departmentID, departmentName FROM Department");
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="portal-container">
    <?php include '../../../includes/portal-sidebar.php'; ?>
    <main class="portal-main">
        <div class="portal-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Course Management</h1>
                <button class="btn btn-primary" onclick="showAddModal()">Create New Course</button>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>ID</th><th>Name</th><th>Dept</th><th>Type</th><th>Credits</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $c): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($c['courseID']) ?></strong></td>
                                    <td><?= htmlspecialchars($c['courseName']) ?></td>
                                    <td><?= htmlspecialchars($c['departmentID']) ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($c['courseType']) ?></span></td>
                                    <td><?= htmlspecialchars($c['credits']) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick='editCourse(<?= json_encode($c) ?>)'>Edit</button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteCourse('<?= htmlspecialchars($c['courseID']) ?>')">Delete</button>
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

<!-- Save Modal -->
<div id="saveModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
    <div class="modal-content bg-white p-4 rounded shadow" style="width: 600px;">
        <h3 id="modalTitle">Course Information</h3>
        <form id="saveForm">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="is_update" id="is_update" value="">
            <div class="row g-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Course ID</label>
                    <input type="text" name="courseID" id="c_id" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Course Name</label>
                    <input type="text" name="courseName" id="c_name" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Department</label>
                    <select name="departmentID" id="c_dept" class="form-select" required>
                        <?php foreach ($departments as $d): ?>
                            <option value="<?= htmlspecialchars($d['departmentID']) ?>"><?= htmlspecialchars($d['departmentName']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Course Type</label>
                    <select name="courseType" id="c_type" class="form-select">
                        <option value="Undergraduate">Undergraduate</option>
                        <option value="Graduate">Graduate</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Credits</label>
                    <input type="number" name="credits" id="c_credits" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Passing Grade</label>
                    <select name="passingGrade" id="c_pass" class="form-select">
                        <option value="C">C</option>
                        <option value="B">B</option>
                        <option value="D">D</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" id="c_desc" class="form-control" rows="3"></textarea>
            </div>
            <button type="button" class="btn btn-secondary" onclick="hideModal()">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Course</button>
        </form>
    </div>
</div>

<script>
function showAddModal() {
    document.getElementById('modalTitle').textContent = 'Create New Course';
    document.getElementById('is_update').value = '';
    document.getElementById('c_id').readOnly = false;
    document.getElementById('saveForm').reset();
    document.getElementById('saveModal').style.display = 'flex';
}

function editCourse(c) {
    document.getElementById('modalTitle').textContent = 'Edit Course: ' + c.courseID;
    document.getElementById('is_update').value = '1';
    document.getElementById('c_id').value = c.courseID;
    document.getElementById('c_id').readOnly = true;
    document.getElementById('c_name').value = c.courseName;
    document.getElementById('c_dept').value = c.departmentID;
    document.getElementById('c_type').value = c.courseType;
    document.getElementById('c_credits').value = c.credits;
    document.getElementById('c_pass').value = c.passingGrade;
    document.getElementById('c_desc').value = c.courseDescription || '';
    document.getElementById('saveModal').style.display = 'flex';
}

function hideModal() { document.getElementById('saveModal').style.display = 'none'; }

document.getElementById('saveForm').onsubmit = function(e) {
    e.preventDefault();
    fetch('catalog.php', {
        method: 'POST',
        body: new FormData(this)
    })
    .then(r => r.json())
    .then(res => {
        alert(res.message);
        if (res.ok) location.reload();
    });
};

function deleteCourse(id) {
    if (!confirm('Are you sure you want to delete ' + id + '?')) return;
    fetch('catalog.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ action: 'delete', courseID: id })
    })
    .then(r => r.json())
    .then(res => {
        alert(res.message);
        if (res.ok) location.reload();
    });
}
</script>
<?php require_once '../../../includes/footer.php'; ?>
