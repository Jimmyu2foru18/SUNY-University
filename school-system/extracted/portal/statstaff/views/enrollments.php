<?php
// portal/statstaff/views/enrollments.php
require_once '../../../config/database.php';
require_once '../../../src/controllers/BaseController.php';
require_once '../../../includes/portal_header.php';

$auth = new BaseController($pdo);
$auth->checkRole(['StatStaff']);

$stmt = $pdo->query("SELECT * FROM Enrollment");
$data = $stmt->fetchAll();
?>
<div class="container my-5">
    <a href="../index.php" class="btn btn-outline-secondary mb-3">&larr; Dashboard</a>
    <h2 class="fw-bold mb-4">View Enrollments</h2>
    <table class="table table-striped shadow-sm">
        <thead class="table-light">
            <tr><th>Student ID</th><th>CRN</th><th>Grade</th></tr>
        </thead>
        <tbody>
            <?php foreach ($data as $d): ?>
                <tr>
                    <td><?= htmlspecialchars($d['studentID']) ?></td>
                    <td><?= htmlspecialchars($d['CRN']) ?></td>
                    <td><?= htmlspecialchars($d['grade']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once '../../../includes/footer.php'; ?>
