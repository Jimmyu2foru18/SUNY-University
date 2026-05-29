<?php
// portal/statstaff/views/sections.php
require_once '../../../config/database.php';
require_once '../../../src/controllers/BaseController.php';
require_once '../../../includes/portal_header.php';

$auth = new BaseController($pdo);
$auth->checkRole(['StatStaff']);

$stmt = $pdo->query("SELECT * FROM CourseSection");
$data = $stmt->fetchAll();
?>
<div class="container my-5">
    <a href="../index.php" class="btn btn-outline-secondary mb-3">&larr; Dashboard</a>
    <h2 class="fw-bold mb-4">View Sections</h2>
    <table class="table table-striped shadow-sm">
        <thead class="table-light">
            <tr><th>CRN</th><th>Course ID</th></tr>
        </thead>
        <tbody>
            <?php foreach ($data as $d): ?>
                <tr>
                    <td><?= htmlspecialchars($d['CRN']) ?></td>
                    <td><?= htmlspecialchars($d['courseID']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once '../../../includes/footer.php'; ?>
