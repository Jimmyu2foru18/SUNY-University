<?php
// portal/admin/management/departments.php
require_once '../../../config/database.php';
require_once '../../../src/controllers/BaseController.php';
require_once '../../../includes/portal_header.php';

$auth = new BaseController($pdo);
$auth->checkRole(['Admin']);

$stmt = $pdo->query("SELECT * FROM Department");
$data = $stmt->fetchAll();
?>
<div class="container my-5">
    <a href="../index.php" class="btn btn-outline-secondary mb-3">&larr; Dashboard</a>
    <h2 class="fw-bold mb-4">Manage Departments</h2>
    <table class="table table-striped shadow-sm">
        <thead class="table-light">
            <tr><th>Dept ID</th><th>Dept Name</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($data as $d): ?>
                <tr>
                    <td><?= htmlspecialchars($d['deptID']) ?></td>
                    <td><?= htmlspecialchars($d['deptName']) ?></td>
                    <td><button class="btn btn-sm btn-outline-primary">Edit</button></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once '../../../includes/footer.php'; ?>
