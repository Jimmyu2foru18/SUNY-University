<?php
// portal/faculty/views/advisees.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../../../config/database.php';
require_once '../../../src/controllers/FacultyController.php';
require_once '../../../includes/portal_header.php';

$auth = new FacultyController($pdo);
$facultyID = $_SESSION['user_id'];

// --- Handle AJAX Scheduling ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'schedule') {
    header('Content-Type: application/json');
    $studentID = $_POST['studentID'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $message = $_POST['message'];

    try {
        // 1. Basic Validation: Past Date
        if (strtotime($date . ' ' . $time) < time()) {
            throw new Exception("Cannot schedule appointments in the past.");
        }

        // 2. Conflict Check: Is faculty teaching?
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM CourseSection WHERE facultyID = :fid AND time = :time");
        $stmt->execute(['fid' => $facultyID, 'time' => $time]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Conflict: You are teaching at this time.");
        }

        // 3. Insert Appointment
        $stmt = $pdo->prepare("INSERT INTO Appointment (studentID, appointmentType, appointmentDate, appointmentTime, message, status) VALUES (?, 'academic', ?, ?, ?, 'Scheduled')");
        $stmt->execute([$studentID, $date, $time, $message]);
        
        echo json_encode(['ok' => true, 'message' => 'Meeting scheduled successfully.']);
    } catch (Exception $e) {
        echo json_encode(['ok' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// Fetch Advisees
$stmt = $pdo->prepare("
    SELECT U.firstName, U.lastName, S.studentID, L.email
    FROM AdvisorAdvisee AA
    JOIN Student S ON AA.studentID = S.studentID
    JOIN User U ON S.studentID = U.userID
    JOIN Login L ON S.studentID = L.userID
    WHERE AA.facultyID = :fid
");
$stmt->execute(['fid' => $facultyID]);
$advisees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container my-5">
    <a href="../index.php" class="btn btn-outline-secondary mb-3">&larr; Dashboard</a>
    <h2 class="fw-bold mb-4">My Advisees</h2>

    <div class="card border-0 shadow-sm">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Name</th><th>Student ID</th><th>Email</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($advisees as $s): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($s['firstName'] . ' ' . $s['lastName']) ?></strong></td>
                        <td><?= htmlspecialchars($s['studentID']) ?></td>
                        <td><?= htmlspecialchars($s['email']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="showScheduleModal('<?= $s['studentID'] ?>', '<?= htmlspecialchars($s['firstName'].' '.$s['lastName']) ?>')">
                                Schedule Meeting
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Scheduling Modal -->
<div id="scheduleModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
    <div class="modal-content bg-white p-4 rounded shadow" style="width: 500px;">
        <h3 id="modalTitle">Schedule Meeting</h3>
        <form id="scheduleForm">
            <input type="hidden" name="action" value="schedule">
            <input type="hidden" name="studentID" id="modalStudentID">
            <div class="mb-3">
                <label>Date</label>
                <input type="date" name="date" class="form-control" required min="<?= date('Y-m-d') ?>">
            </div>
            <div class="mb-3">
                <label>Time</label>
                <input type="time" name="time" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Message/Notes</label>
                <textarea name="message" class="form-control" rows="3"></textarea>
            </div>
            <button type="button" class="btn btn-secondary" onclick="hideModal()">Cancel</button>
            <button type="submit" class="btn btn-primary">Book Meeting</button>
        </form>
    </div>
</div>

<script>
function showScheduleModal(id, name) {
    document.getElementById('modalTitle').textContent = 'Schedule Meeting with ' + name;
    document.getElementById('modalStudentID').value = id;
    document.getElementById('scheduleModal').style.display = 'flex';
}
function hideModal() { document.getElementById('scheduleModal').style.display = 'none'; }

document.getElementById('scheduleForm').onsubmit = function(e) {
    e.preventDefault();
    fetch('advisees.php', { method: 'POST', body: new FormData(this) })
    .then(r => r.json()).then(res => { alert(res.message); if (res.ok) location.reload(); });
};
</script>
<?php require_once '../../../includes/footer.php'; ?>
