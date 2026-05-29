<?php
// portal/advising.php
require_once '../config/database.php';
require_once '../src/controllers/StatStaffController.php'; // Using StatStaffController as a base role-based access controller
require_once '../includes/portal_header.php';

// Auth checks
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit;
}

$userID = $_SESSION['user_id'];
$userType = $_SESSION['user_type'];

$message = '';
$error = '';

// --- Handle Actions ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        if ($_POST['action'] === 'schedule') {
            $facultyID = ($userType === 'Faculty') ? $userID : $_POST['facultyID'];
            $studentID = ($userType === 'Student') ? $userID : $_POST['studentID'];

            $stmt = $pdo->prepare("
                INSERT INTO Appointment (facultyID, studentID, appointmentDate, time, reason, status) 
                VALUES (:fid, :sid, :date, :time, :reason, 'Scheduled')
            ");
            $stmt->execute([
                'fid' => $facultyID,
                'sid' => $studentID,
                'date' => $_POST['date'],
                'time' => $_POST['time'],
                'reason' => $_POST['reason']
            ]);
            $message = "Appointment scheduled successfully.";
        } elseif ($_POST['action'] === 'cancel') {
            $stmt = $pdo->prepare("DELETE FROM Appointment WHERE appointmentID = :aid AND " . ($userType === 'Faculty' ? "facultyID" : "studentID") . " = :uid");
            $stmt->execute(['aid' => $_POST['appointmentID'], 'uid' => $userID]);
            $message = "Appointment cancelled.";
        } elseif ($_POST['action'] === 'complete' && $userType === 'Faculty') {
            $stmt = $pdo->prepare("UPDATE Appointment SET status = 'Completed' WHERE appointmentID = :aid AND facultyID = :fid");
            $stmt->execute(['aid' => $_POST['appointment_id'], 'fid' => $userID]);
            $message = "Appointment marked as completed.";
        }
    } catch (Exception $e) {
        $error = "Database Error: " . $e->getMessage();
    }
}

// --- Data Fetching ---
$advisees = [];
$advisor = null;
$appointments = [];
$studentProfile = null;
$studentTranscript = [];

if ($userType === 'Faculty') {
    // Fetch Advisees
    $stmt = $pdo->prepare("
        SELECT U.userID, U.firstName, U.lastName 
        FROM AdvisorAdvisee AA
        JOIN User U ON AA.studentID = U.userID 
        WHERE AA.facultyID = :fid
    ");
    $stmt->execute(['fid' => $userID]);
    $advisees = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $selectedID = $_GET['student_id'] ?? null;
    if ($selectedID) {
        // Fetch Profile
        $stmt = $pdo->prepare("SELECT U.userID, U.firstName, U.lastName, S.studentType FROM User U JOIN Student S ON U.userID = S.studentID WHERE U.userID = :sid");
        $stmt->execute(['sid' => $selectedID]);
        $studentProfile = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch Transcript (simplified for demo)
        $stmt = $pdo->prepare("SELECT C.courseID, C.courseName, E.grade FROM Enrollment E JOIN CourseSection CS ON E.CRN = CS.CRN JOIN Course C ON CS.courseID = C.courseID WHERE E.studentID = :sid");
        $stmt->execute(['sid' => $selectedID]);
        $studentTranscript = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch Appointments
        $stmt = $pdo->prepare("SELECT A.*, U.firstName, U.lastName FROM Appointment A JOIN User U ON A.studentID = U.userID WHERE A.facultyID = :fid AND A.studentID = :sid ORDER BY A.appointmentDate DESC");
        $stmt->execute(['fid' => $userID, 'sid' => $selectedID]);
        $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} else if ($userType === 'Student') {
    // Fetch Advisor
    $stmt = $pdo->prepare("SELECT U.userID, U.firstName, U.lastName FROM AdvisorAdvisee AA JOIN User U ON AA.facultyID = U.userID WHERE AA.studentID = :sid LIMIT 1");
    $stmt->execute(['sid' => $userID]);
    $advisor = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch Appointments
    $stmt = $pdo->prepare("SELECT A.*, U.firstName, U.lastName FROM Appointment A JOIN User U ON A.facultyID = U.userID WHERE A.studentID = :sid AND A.status != 'Cancelled' ORDER BY A.appointmentDate DESC");
    $stmt->execute(['sid' => $userID]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container my-5">
    <h1 class="fw-bold mb-4">Academic Advising</h1>
    <?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <?php if ($userType === 'Faculty'): ?>
        <div class="card p-4 border-0 shadow-sm mb-4">
            <form method="GET">
                <label>Select Advisee</label>
                <select name="student_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Choose a student...</option>
                    <?php foreach ($advisees as $a): ?>
                        <option value="<?= $a['userID'] ?>" <?= ($a['userID'] == ($selectedID ?? '')) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($a['firstName'] . ' ' . $a['lastName']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <?php if ($studentProfile): ?>
            <div class="card p-4 border-0 shadow-sm mb-4">
                <h4>Advisee: <?= htmlspecialchars($studentProfile['firstName'] . ' ' . $studentProfile['lastName']) ?></h4>
                <p>Status: <?= htmlspecialchars($studentProfile['studentType']) ?></p>
            </div>
            <!-- Appointment list and Schedule form omitted for brevity -->
        <?php endif; ?>
    <?php else: ?>
        <!-- Student View Implementation -->
        <div class="card p-4 border-0 shadow-sm">
            <h3>Schedule with Advisor: <?= $advisor ? htmlspecialchars($advisor['firstName'] . ' ' . $advisor['lastName']) : 'None' ?></h3>
            <?php if ($advisor): ?>
                <form method="POST">
                    <input type="hidden" name="action" value="schedule">
                    <input type="hidden" name="facultyID" value="<?= $advisor['userID'] ?>">
                    <input type="date" name="date" class="form-control mb-2" required>
                    <input type="time" name="time" class="form-control mb-2" required>
                    <textarea name="reason" class="form-control mb-2" placeholder="Reason..." required></textarea>
                    <button type="submit" class="btn btn-primary">Book</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?php require_once '../includes/footer.php'; ?>
