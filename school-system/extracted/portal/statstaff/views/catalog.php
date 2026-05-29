<?php
// portal/statstaff/views/catalog.php
require_once '../../../config/database.php';
require_once '../../../src/controllers/BaseController.php';
require_once '../../../includes/portal_header.php';

$auth = new BaseController($pdo);
$auth->checkRole(['StatStaff']);

$stmt = $pdo->query("SELECT * FROM Course");
$data = $stmt->fetchAll();
?>
<div class="container my-5">
    <a href="../index.php" class="btn btn-outline-secondary mb-3">&larr; Dashboard</a>
    <h2 class="fw-bold mb-4">Course Catalog</h2>
    <table class="table table-striped shadow-sm">
        <thead class="table-light">
            <tr><th>Course ID</th><th>Name</th><th>Credits</th></tr>
        </thead>
        <tbody>
            <?php foreach ($data as $d): ?>
                <tr>
                    <td><?= htmlspecialchars($d['courseID']) ?></td>
                    <td><?= htmlspecialchars($d['courseName']) ?></td>
                    <td><?= htmlspecialchars($d['credits']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once '../../../includes/footer.php'; ?>
