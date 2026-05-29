<?php
// portal/statstaff/views/schedule.php
require_once '../../../config/database.php';
require_once '../../../src/controllers/BaseController.php';
require_once '../../../includes/portal_header.php';

$auth = new BaseController($pdo);
$auth->checkRole(['StatStaff']);

$stmt = $pdo->query("SELECT * FROM TimeSlot");
$data = $stmt->fetchAll();
?>
<div class="container my-5">
    <a href="../index.php" class="btn btn-outline-secondary mb-3">&larr; Dashboard</a>
    <h2 class="fw-bold mb-4">Master Schedule</h2>
    <table class="table table-striped shadow-sm">
        <thead class="table-light">
            <tr><th>Slot ID</th></tr>
        </thead>
        <tbody>
            <?php foreach ($data as $d): ?>
                <tr>
                    <td><?= htmlspecialchars($d['timeSlotID']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once '../../../includes/footer.php'; ?>
