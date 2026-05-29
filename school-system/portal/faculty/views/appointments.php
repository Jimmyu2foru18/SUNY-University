<?php
// portal/faculty/views/appointments.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../../../config/database.php';
require_once '../../../src/controllers/FacultyController.php';
require_once '../../../includes/portal_header.php';

$auth = new FacultyController($pdo);
$facultyID = $_SESSION['user_id'];

// Handle Actions
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $aid = $_POST['appointment_id'] ?? null;

    if ($aid) {
        if ($_POST['action'] === 'complete') {
            $stmt = $pdo->prepare("UPDATE Appointment SET status = 'Completed' WHERE appointmentID = ?");
            $stmt->execute([$aid]);
            $message = "Appointment marked as completed.";
        } elseif ($_POST['action'] === 'cancel') {
            $stmt = $pdo->prepare("UPDATE Appointment SET status = 'Cancelled' WHERE appointmentID = ?");
            $stmt->execute([$aid]);
            $message = "Appointment cancelled.";
        }
    }
}

// Fetch Appointments
$statusFilter = $_GET['status'] ?? 'Scheduled';

// Join with AdvisorAdvisee to only get appointments for students advised by this faculty
$query = "
    SELECT A.*, U.firstName, U.lastName, L.email 
    FROM Appointment A
    JOIN User U ON A.studentID = U.userID
    JOIN Login L ON U.userID = L.userID
    JOIN AdvisorAdvisee AA ON A.studentID = AA.studentID
    WHERE AA.facultyID = ?
";
$params = [$facultyID];

if ($statusFilter) {
    $query .= " AND A.status = ?";
    $params[] = $statusFilter;
}

$query .= " ORDER BY A.appointmentDate ASC, A.appointmentTime ASC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);?>

<div class="container my-5">
    <a href="../index.php" class="btn btn-outline-secondary mb-3">&larr; Dashboard</a>
    <h1>Student Appointments</h1>
    
    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="card p-3 mb-4 border-0 shadow-sm">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="Scheduled" <?= $statusFilter === 'Scheduled' ? 'selected' : ''; ?>>Upcoming</option>
                    <option value="Completed" <?= $statusFilter === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="Cancelled" <?= $statusFilter === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
        </form>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Student</th>
                        <th>Date & Time</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($appointments)): ?>
                        <tr><td colspan="5" class="text-center">No appointments found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($appointments as $appt): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($appt['firstName'] . ' ' . $appt['lastName']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($appt['email']) ?></small>
                                </td>
                                <td>
                                    <?= htmlspecialchars(date('M d, Y', strtotime($appt['appointmentDate']))) ?><br>
                                    <small class="text-muted"><?= htmlspecialchars(date('h:i A', strtotime($appt['appointmentTime']))) ?></small>
                                </td>
                                <td><?= htmlspecialchars($appt['message']) ?></td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($appt['status']) ?></span></td>
                                <td>
                                    <?php if ($appt['status'] === 'Scheduled'): ?>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="complete">
                                            <input type="hidden" name="appointment_id" value="<?= $appt['appointmentID'] ?>">
                                            <button type="submit" class="btn btn-sm btn-success">Complete</button>
                                        </form>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="cancel">
                                            <input type="hidden" name="appointment_id" value="<?= $appt['appointmentID'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Cancel</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../../../includes/footer.php'; ?>
