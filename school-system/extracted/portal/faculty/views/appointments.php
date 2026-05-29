<?php
// portal/faculty/views/appointments.php
require_once '../../../config/database.php';
require_once '../../../src/controllers/BaseController.php';
require_once '../../../includes/portal_header.php';

$auth = new BaseController($pdo);
$auth->checkRole(['Faculty']);

// Assuming an 'Appointment' table exists based on requirements
try {
    $stmt = $pdo->prepare("
        SELECT A.appointmentID, A.appointmentDate, A.time, A.status, U.firstName, U.lastName 
        FROM Appointment A
        JOIN Student S ON A.studentID = S.studentID
        JOIN User U ON S.studentID = U.userID
        WHERE A.facultyID = :facultyId
        ORDER BY A.appointmentDate, A.time
    ");
    $stmt->execute(['facultyId' => $_SESSION['user_id']]);
    $appointments = $stmt->fetchAll();
} catch (Exception $e) {
    $appointments = [];
}
?>
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="../index.php" class="btn btn-outline-secondary mb-3">&larr; Dashboard</a>
            <h2 class="fw-bold">Manage Appointments</h2>
        </div>
        <button class="btn btn-primary">Schedule New</button>
    </div>

    <?php if (count($appointments) > 0): ?>
        <div class="card border-0 shadow-sm">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>Date</th><th>Time</th><th>Student</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $a): ?>
                        <tr>
                            <td><?= htmlspecialchars($a['appointmentDate']) ?></td>
                            <td><?= htmlspecialchars($a['time']) ?></td>
                            <td><?= htmlspecialchars($a['firstName'] . ' ' . $a['lastName']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($a['status']) ?></span></td>
                            <td><button class="btn btn-sm btn-outline-danger">Cancel</button></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <h4 class="text-muted">No appointments scheduled.</h4>
        </div>
    <?php endif; ?>
</div>
<?php require_once '../../../includes/footer.php'; ?>
