<?php
// portal/student/views/appointments.php
require_once '../../../config/database.php';
require_once '../../../src/controllers/BaseController.php';
require_once '../../../includes/portal_header.php';

$auth = new BaseController($pdo);
$auth->checkRole(['Student']);

$studentId = $_SESSION['user_id'];
// Handle Scheduling and Cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        try {
            $stmt = $pdo->prepare("INSERT INTO Appointment (studentID, facultyID, appointmentDate, time, status) VALUES (:sid, :fid, :date, :time, 'Scheduled')");
            $stmt->execute(['sid' => $studentId, 'fid' => $_POST['facultyID'], 'date' => $_POST['date'], 'time' => $_POST['time']]);
            $message = "Appointment scheduled successfully!";
        } catch (Exception $e) {
            $error = "Error scheduling: " . $e->getMessage();
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'cancel') {
        try {
            $stmt = $pdo->prepare("DELETE FROM Appointment WHERE appointmentID = :id AND studentID = :sid");
            $stmt->execute(['id' => $_POST['appointmentID'], 'sid' => $studentId]);
            $message = "Appointment cancelled successfully.";
        } catch (Exception $e) {
            $error = "Error cancelling: " . $e->getMessage();
        }
    }
}

// Fetch Student's Advisor
$stmt = $pdo->prepare("
    SELECT U.firstName, U.lastName, AA.facultyID
    FROM AdvisorAdvisee AA
    JOIN User U ON AA.facultyID = U.userID
    WHERE AA.studentID = :sid
");
$stmt->execute(['sid' => $studentId]);
$advisor = $stmt->fetch();

// Fetch Student's Appointments
$stmt = $pdo->prepare("
    SELECT A.*, U.firstName, U.lastName 
    FROM Appointment A
    JOIN User U ON A.facultyID = U.userID
    WHERE A.studentID = :sid
    ORDER BY A.appointmentDate, A.time
");
$stmt->execute(['sid' => $studentId]);
$appointments = $stmt->fetchAll();
?>
<div class="container my-5">
    <a href="../index.php" class="btn btn-outline-secondary mb-3">&larr; Dashboard</a>
    <h2 class="fw-bold mb-4">My Appointments</h2>

    <?php if ($message): ?>
        <div class="alert <?= strpos($message, 'Error') !== false ? 'alert-danger' : 'alert-success' ?>"><?= $message ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-7 mb-5">
            <h4 class="fw-bold mb-3">Upcoming Meetings</h4>
            <?php if (count($appointments) > 0): ?>
                <div class="card border-0 shadow-sm">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>Date</th><th>Time</th><th>With</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($appointments as $a): ?>
                                <tr>
                                    <td><?= htmlspecialchars($a['appointmentDate']) ?></td>
                                    <td><?= htmlspecialchars($a['time']) ?></td>
                                    <td>Prof. <?= htmlspecialchars($a['lastName']) ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($a['status']) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted">No appointments found.</p>
            <?php endif; ?>
        </div>

        <div class="col-lg-5">
            <div class="card p-4 border-0 shadow-sm">
                <h4 class="fw-bold mb-3">Schedule a Meeting</h4>
                <?php if ($advisor): ?>
                    <form method="POST">
                        <input type="hidden" name="facultyID" value="<?= $advisor['facultyID'] ?>">
                        <p class="small text-muted mb-4">Scheduling with your advisor: <strong>Prof. <?= htmlspecialchars($advisor['lastName']) ?></strong></p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Preferred Date</label>
                            <input type="date" name="date" class="form-control" required min="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Preferred Time</label>
                            <input type="time" name="time" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Request Appointment</button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-warning mb-0">
                        Please have an advisor assigned before scheduling.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php require_once '../../../includes/footer.php'; ?>
