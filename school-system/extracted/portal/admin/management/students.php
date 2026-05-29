<?php
// portal/admin/management/students.php
require_once '../../../config/database.php';
require_once '../../../src/controllers/BaseController.php';
require_once '../../../includes/portal_header.php';

$auth = new BaseController($pdo);
$auth->checkRole(['Admin']);

$stmt = $pdo->query("
    SELECT S.studentID, U.firstName, U.lastName, S.studentType, L.email 
    FROM Student S 
    JOIN User U ON S.studentID = U.userID 
    JOIN Login L ON S.studentID = L.userID
    ORDER BY U.lastName, U.firstName
");
$students = $stmt->fetchAll();
?>
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="../index.php" class="btn btn-outline-secondary mb-3">&larr; Dashboard</a>
            <h2 class="fw-bold">Manage Students</h2>
        </div>
        <div style="width: 300px;">
            <input type="text" id="adminStudentSearch" class="form-control" placeholder="Search students...">
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>ID</th><th>Name</th><th>Email</th><th>Type</th><th>Actions</th></tr>
            </thead>
            <tbody id="studentTableBody">
                <?php foreach ($students as $s): ?>
                    <tr class="student-row" data-search="<?= strtolower($s['studentID'] . ' ' . $s['firstName'] . ' ' . $s['lastName'] . ' ' . $s['email']) ?>">
                        <td><?= htmlspecialchars($s['studentID']) ?></td>
                        <td><?= htmlspecialchars($s['firstName'] . ' ' . $s['lastName']) ?></td>
                        <td><?= htmlspecialchars($s['email']) ?></td>
                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($s['studentType']) ?></span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary me-1">Edit</button>
                            <button class="btn btn-sm btn-outline-danger">Hold</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.getElementById('adminStudentSearch').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        document.querySelectorAll('.student-row').forEach(row => {
            const text = row.getAttribute('data-search');
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });
</script>
<?php require_once '../../../includes/footer.php'; ?>
