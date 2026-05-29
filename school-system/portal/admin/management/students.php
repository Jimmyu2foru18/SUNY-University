<?php
// portal/admin/management/students.php
ini_set('display_errors', 0); // Hide errors to prevent CORB issues
error_reporting(E_ALL);
require_once '../../../config/database.php';
require_once '../../../src/controllers/AdminController.php';
require_once '../../../includes/portal_header.php';

$auth = new AdminController($pdo);

// Handle AJAX Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    ob_start(); // Prevent non-JSON output from breaking response
    header('Content-Type: application/json');
    try {
        if ($_POST['action'] === 'save') {
            $isUpdate = !empty($_POST['is_update']);
            if ($isUpdate) {
                $stmt = $pdo->prepare("UPDATE User SET firstName = ?, lastName = ? WHERE userID = ?");
                $stmt->execute([$_POST['firstName'], $_POST['lastName'], $_POST['studentID']]);
                
                $stmt = $pdo->prepare("UPDATE Student SET studentType = ?, year = ? WHERE studentID = ?");
                $stmt->execute([$_POST['studentType'], $_POST['year'], $_POST['studentID']]);
                
                echo json_encode(['ok' => true, 'message' => 'Student updated successfully.']);
            } else {
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("INSERT INTO User (firstName, lastName, userType) VALUES (?, ?, 'Student')");
                $stmt->execute([$_POST['firstName'], $_POST['lastName']]);
                $newID = $pdo->lastInsertId();
                
                $email = strtolower($_POST['firstName'] . '.' . $_POST['lastName'] . rand(100, 999) . '@bridgeport.edu');
                $stmt = $pdo->prepare("INSERT INTO Login (userID, email, password, userType) VALUES (?, ?, ?, 'Student')");
                $stmt->execute([$newID, $email, password_hash($_POST['password'], PASSWORD_DEFAULT)]);
                
                $stmt = $pdo->prepare("INSERT INTO Student (studentID, studentType, year) VALUES (?, ?, ?)");
                $stmt->execute([$newID, $_POST['studentType'], $_POST['year']]);
                
                $pdo->commit();
                echo json_encode(['ok' => true, 'message' => 'Student added successfully.']);
            }
        } elseif ($_POST['action'] === 'delete') {
            $pdo->prepare("DELETE FROM Student WHERE studentID = ?")->execute([$_POST['userID']]);
            $pdo->prepare("DELETE FROM Login WHERE userID = ?")->execute([$_POST['userID']]);
            $pdo->prepare("DELETE FROM User WHERE userID = ?")->execute([$_POST['userID']]);
            echo json_encode(['ok' => true, 'message' => 'Student removed successfully.']);
        }
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        echo json_encode(['ok' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    ob_end_flush();
    exit;
}

// Fetch Data
$stmt = $pdo->query("
    SELECT U.userID, U.firstName, U.lastName, S.studentType, S.year, L.email, 
           GROUP_CONCAT(M.majorName SEPARATOR ', ') as majorName
    FROM User U 
    JOIN Student S ON U.userID = S.studentID 
    JOIN Login L ON U.userID = L.userID
    LEFT JOIN StudentMajor SM ON U.userID = SM.studentID
    LEFT JOIN Major M ON SM.majorID = M.majorID
    GROUP BY U.userID
");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="portal-container">
    <?php include '../../../includes/portal-sidebar.php'; ?>
    <main class="portal-main">
        <div class="portal-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Student Management</h1>
                <button class="btn btn-primary" onclick="showAddModal()">Add New Student</button>
            </div>
            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <input type="text" id="studentSearch" class="form-control" placeholder="Search students...">
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="studentTable">
                        <thead class="table-light">
                            <tr><th>ID</th><th>Name</th><th>Email</th><th>Type</th><th>Year</th><th>Major</th><th>Actions</th></tr>
                        </thead>
                        <tbody id="studentBody">
                            <?php foreach ($students as $s): ?>
                                <tr class="student-row">
                                    <td><strong><?= htmlspecialchars($s['userID']) ?></strong></td>
                                    <td><?= htmlspecialchars($s['firstName'] . ' ' . $s['lastName']) ?></td>
                                    <td><?= htmlspecialchars($s['email']) ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($s['studentType']) ?></span></td>
                                    <td><?= htmlspecialchars($s['year']) ?></td>
                                    <td><?= htmlspecialchars($s['majorName'] ?? 'None') ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick='editStudent(<?= htmlspecialchars(json_encode($s), ENT_QUOTES, "UTF-8") ?>)'>Edit</button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteStudent('<?= htmlspecialchars($s['userID']) ?>')">Delete</button>
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
<div id="studentModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
    <div class="modal-content bg-white p-4 rounded shadow" style="width: 500px;">
        <h3 id="modalTitle">Add New Student</h3>
        <form id="studentForm" style="margin-top: 20px;">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="is_update" id="is_update" value="">
            <input type="hidden" name="studentID" id="s_id" value="">
            
            <div class="mb-3">
                <label for="s_first">First Name</label>
                <input type="text" name="firstName" id="s_first" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="s_last">Last Name</label>
                <input type="text" name="lastName" id="s_last" class="form-control" required>
            </div>
            <div class="mb-3" id="passwordField">
                <label for="s_pass">Password</label>
                <input type="password" name="password" id="s_pass" class="form-control">
            </div>
            <div class="mb-3">
                <label for="s_type">Student Type</label>
                <select name="studentType" id="s_type" class="form-select">
                    <option value="Undergraduate">Undergraduate</option>
                    <option value="Graduate">Graduate</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="s_year">Year</label>
                <input type="number" name="year" id="s_year" class="form-control" value="<?= date('Y') ?>">
            </div>
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" onclick="hideModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Student</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const studentForm = document.getElementById('studentForm');
    
    studentForm.onsubmit = function(e) {
        e.preventDefault();
        
        const submitBtn = studentForm.querySelector('button[type="submit"]');
        if (submitBtn.disabled) return;
        submitBtn.disabled = true;
        
        fetch('students.php', { method: 'POST', body: new FormData(this) })
        .then(r => r.json())
        .then(res => { 
            alert(res.message); 
            if (res.ok) window.location.reload();
            else submitBtn.disabled = false;
        })
        .catch(() => { submitBtn.disabled = false; });
    };
    
    document.getElementById('studentSearch').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        document.querySelectorAll('.student-row').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(query) ? '' : 'none';
        });
    });
});

window.showAddModal = function() {
    document.getElementById('modalTitle').textContent = 'Add New Student';
    document.getElementById('is_update').value = '';
    document.getElementById('passwordField').style.display = 'block';
    document.getElementById('studentForm').reset();
    document.getElementById('studentModal').style.display = 'flex';
};

window.editStudent = function(s) {
    document.getElementById('modalTitle').textContent = 'Edit Student';
    document.getElementById('is_update').value = '1';
    document.getElementById('s_id').value = s.userID;
    document.getElementById('s_first').value = s.firstName;
    document.getElementById('s_last').value = s.lastName;
    document.getElementById('passwordField').style.display = 'none';
    document.getElementById('s_type').value = s.studentType;
    document.getElementById('s_year').value = s.year;
    document.getElementById('studentModal').style.display = 'flex';
};

window.hideModal = function() { document.getElementById('studentModal').style.display = 'none'; };

window.deleteStudent = function(id) {
    if (!confirm('Are you sure?')) return;
    fetch('students.php', { 
        method: 'POST', 
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}, 
        body: new URLSearchParams({ action: 'delete', userID: id }) 
    })
    .then(r => r.json()).then(res => { 
        alert(res.message); 
        if (res.ok) window.location.reload(); 
    });
};
</script>
<?php require_once '../../../includes/footer.php'; ?>
