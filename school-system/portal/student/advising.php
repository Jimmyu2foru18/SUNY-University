<?php
// portal/student/advising.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../../config/database.php';
require_once '../../src/controllers/StudentController.php';
require_once '../../includes/portal_header.php';

$auth = new StudentController($pdo);
$studentID = $_SESSION['user_id'];

// Fetch Advisor
$stmt = $pdo->prepare("
    SELECT U.firstName, U.lastName 
    FROM AdvisorAdvisee AA
    JOIN User U ON AA.facultyID = U.userID
    WHERE AA.studentID = :sid
");
$stmt->execute(['sid' => $studentID]);
$advisor = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle AJAX Scheduling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'schedule') {
    header('Content-Type: application/json');
    try {
        $stmt = $pdo->prepare("INSERT INTO Appointment (studentID, appointmentType, appointmentDate, appointmentTime, message, status) VALUES (?, 'academic', ?, ?, ?, 'Scheduled')");
        $stmt->execute([$studentID, $_POST['date'], $_POST['time'], $_POST['message']]);
        echo json_encode(['ok' => true, 'message' => 'Appointment booked successfully.']);
    } catch (Exception $e) {
        echo json_encode(['ok' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}
?>

<div class="portal-container">
    <?php 
    $currentPage = 'advising';
    include '../../includes/portal-sidebar.php'; 
    ?>
    <main class="portal-main">
        <div class="portal-content">
            <h1>Academic Advising</h1>
            <p class="subtitle">Manage your relationship and appointments with your academic advisor.</p>

            <div class="card p-4 border-0 shadow-sm mb-4">
                <h3 class="fw-bold">Your Advisor</h3>
                <p class="h4 text-primary"><?= htmlspecialchars(($advisor['firstName'] ?? 'No Advisor Assigned') . ' ' . ($advisor['lastName'] ?? '')) ?></p>
            </div>

            <div class="card p-4 border-0 shadow-sm">
                <h3 class="fw-bold">Schedule Appointment</h3>
                <form id="scheduleForm" class="mt-3">
                    <input type="hidden" name="action" value="schedule">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="date" class="form-control" required min="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Time</label>
                            <input type="time" name="time" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason for Visit</label>
                        <textarea name="message" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Book Appointment</button>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
document.getElementById('scheduleForm').onsubmit = function(e) {
    e.preventDefault();
    fetch('advising.php', { method: 'POST', body: new FormData(this) })
    .then(r => r.json()).then(res => {
        alert(res.message);
        if (res.ok) location.reload();
    });
};
</script>
<?php require_once '../../includes/footer.php'; ?>
