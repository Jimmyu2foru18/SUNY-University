<?php
// portal/faculty/views/attendance.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../../../config/database.php';
require_once '../../../src/controllers/FacultyController.php';
require_once '../../../includes/portal_header.php';

$auth = new FacultyController($pdo);
$facultyID = $_SESSION['user_id'];

// --- Handle Actions ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $crn = $_POST['crn'] ?? '';
    $date = $_POST['date'] ?? date('Y-m-d');

    try {
        if ($_POST['action'] === 'save_attendance') {
            $records = $_POST['attendance'] ?? [];
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("DELETE FROM Attendance WHERE CRN = :crn AND attendanceDate = :date");
            $stmt->execute(['crn' => $crn, 'date' => $date]);
            
            $stmt = $pdo->prepare("INSERT INTO Attendance (CRN, studentID, attendanceDate, present) VALUES (:crn, :sid, :date, :present)");
            foreach ($records as $studentID => $isPresent) {
                $stmt->execute(['crn' => $crn, 'sid' => $studentID, 'date' => $date, 'present' => (int)$isPresent]);
            }
            $pdo->commit();
            echo json_encode(['ok' => true, 'message' => 'Attendance saved successfully.']);
        }
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        echo json_encode(['ok' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// --- Fetch Data ---
$stmt = $pdo->prepare("SELECT CRN, courseID FROM CourseSection WHERE facultyID = :fid");
$stmt->execute(['fid' => $facultyID]);
$sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

$selectedCRN = $_GET['crn'] ?? ($sections[0]['CRN'] ?? '');
$selectedDate = $_GET['date'] ?? date('Y-m-d');

$roster = [];
$attendanceRecords = [];
if ($selectedCRN) {
    // Roster
    $stmt = $pdo->prepare("
        SELECT U.firstName, U.lastName, S.studentID 
        FROM Enrollment E 
        JOIN Student S ON E.studentID = S.studentID 
        JOIN User U ON S.studentID = U.userID 
        WHERE E.CRN = :crn
    ");
    $stmt->execute(['crn' => $selectedCRN]);
    $roster = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Records
    $stmt = $pdo->prepare("SELECT studentID, present FROM Attendance WHERE CRN = :crn AND attendanceDate = :date");
    $stmt->execute(['crn' => $selectedCRN, 'date' => $selectedDate]);
    $attendanceRecords = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
}
?>

<div class="portal-container">
    <?php include '../../../includes/portal-sidebar.php'; ?>
    <main class="portal-main">
        <div class="portal-content">
            <h1>Class Attendance</h1>
            <p class="subtitle">Record and manage attendance for your course sections.</p>

            <!-- Filters -->
            <div class="card p-3 mb-4 border-0 shadow-sm">
                <form method="GET" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Course Section</label>
                        <select name="crn" class="form-select" onchange="this.form.submit()">
                            <option value="">Select a section...</option>
                            <?php foreach ($sections as $sec): ?>
                                <option value="<?= htmlspecialchars($sec['CRN']) ?>" <?= ($sec['CRN'] === $selectedCRN) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($sec['CRN'] . ' - ' . $sec['courseID']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($selectedDate) ?>" onchange="this.form.submit()">
                    </div>
                </form>
            </div>

            <!-- Attendance Table -->
            <?php if ($selectedCRN): ?>
                <form id="attendanceForm">
                    <input type="hidden" name="action" value="save_attendance">
                    <input type="hidden" name="crn" value="<?= htmlspecialchars($selectedCRN) ?>">
                    <input type="hidden" name="date" value="<?= htmlspecialchars($selectedDate) ?>">
                    
                    <div class="card border-0 shadow-sm">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr><th>Student ID</th><th>Name</th><th>Status</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($roster as $student): 
                                        $isPresent = isset($attendanceRecords[$student['studentID']]) ? (int)$attendanceRecords[$student['studentID']] : 1;
                                    ?>
                                        <tr>
                                            <td><?= htmlspecialchars($student['studentID']) ?></td>
                                            <td><?= htmlspecialchars($student['firstName'] . ' ' . $student['lastName']) ?></td>
                                            <td>
                                                <div class="d-flex gap-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="attendance[<?= $student['studentID'] ?>]" value="1" id="p_<?= $student['studentID'] ?>" <?= ($isPresent === 1) ? 'checked' : ''; ?>>
                                                        <label class="form-check-label" for="p_<?= $student['studentID'] ?>">Present</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="attendance[<?= $student['studentID'] ?>]" value="0" id="a_<?= $student['studentID'] ?>" <?= ($isPresent === 0) ? 'checked' : ''; ?>>
                                                        <label class="form-check-label" for="a_<?= $student['studentID'] ?>">Absent</label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button type="button" class="btn btn-primary" onclick="submitAttendance()">Save Attendance</button>
                    </div>
                </form>

                <h3 class="mt-5 fw-bold">Attendance History</h3>
                <div class="card border-0 shadow-sm mt-3">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr><th>Date</th><th>Student</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                <?php
                                $histStmt = $pdo->prepare("
                                    SELECT A.attendanceDate, A.present, U.firstName, U.lastName
                                    FROM Attendance A
                                    JOIN User U ON A.studentID = U.userID
                                    WHERE A.CRN = :crn
                                    ORDER BY A.attendanceDate DESC
                                ");
                                $histStmt->execute(['crn' => $selectedCRN]);
                                $history = $histStmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($history as $h): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($h['attendanceDate']) ?></td>
                                        <td><?= htmlspecialchars($h['firstName'] . ' ' . $h['lastName']) ?></td>
                                        <td><span class="badge <?= $h['present'] ? 'bg-success' : 'bg-danger' ?>"><?= $h['present'] ? 'Present' : 'Absent' ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info">Please select a section to view the roster.</div>
            <?php endif; ?>
        </div>
    </main>
</div>

<script>
async function submitAttendance() {
    const form = document.getElementById('attendanceForm');
    const formData = new FormData(form);
    try {
        const response = await fetch('attendance.php', { method: 'POST', body: formData });
        const res = await response.json();
        alert(res.message);
        if (res.ok) location.reload();
    } catch (e) { alert('Error saving attendance.'); }
}
</script>
<?php require_once '../../../includes/footer.php'; ?>
