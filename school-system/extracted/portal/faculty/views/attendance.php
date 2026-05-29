<?php
// portal/faculty/views/attendance.php
require_once '../../../config/database.php';
require_once '../../../src/controllers/BaseController.php';
require_once '../../../includes/portal_header.php';

$auth = new BaseController($pdo);
$auth->checkRole(['Faculty']);

$crn = $_GET['crn'] ?? '';
$date = date('Y-m-d'); 
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        
        // Fetch timeSlotID for this CRN
        $stmt = $pdo->prepare("SELECT timeSlotID FROM CourseSection WHERE CRN = :crn");
        $stmt->execute(['crn' => $crn]);
        $timeSlotID = $stmt->fetchColumn();

        $stmt = $pdo->prepare("
            INSERT INTO Attendance (CRN, studentID, attendanceDate, timeSlotID, present) 
            VALUES (:crn, :studentID, :date, :tsid, :present)
            ON DUPLICATE KEY UPDATE present = :present2
        ");

        foreach ($students as $s) {
            $isPresent = isset($_POST['attendance'][$s['studentID']]) ? 1 : 0;
            $stmt->execute([
                'crn' => $crn,
                'studentID' => $s['studentID'],
                'date' => $date,
                'tsid' => $timeSlotID,
                'present' => $isPresent,
                'present2' => $isPresent
            ]);
        }
        $pdo->commit();
        $message = "Attendance saved successfully!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $message = "Error saving attendance: " . $e->getMessage();
    }
}

// Fetch Students in this CRN
$stmt = $pdo->prepare("
    SELECT U.firstName, U.lastName, S.studentID
    FROM Enrollment E
    JOIN Student S ON E.studentID = S.studentID
    JOIN User U ON S.studentID = U.userID
    WHERE E.CRN = :crn
");
$stmt->execute(['crn' => $crn]);
$students = $stmt->fetchAll();
?>
<div class="container my-5">
    <a href="teaching_schedule.php" class="btn btn-outline-secondary mb-3">&larr; Schedule</a>
    <h2 class="fw-bold mb-4">Attendance: <?= htmlspecialchars($crn) ?> - <?= htmlspecialchars($date) ?></h2>

    <?php if ($message): ?>
        <div class="alert <?= strpos($message, 'Error') !== false ? 'alert-danger' : 'alert-success' ?>" role="alert">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <table class="table table-hover shadow-sm">
            <thead class="table-light">
                <tr><th>Student Name</th><th>Student ID</th><th>Present?</th></tr>
            </thead>
            <tbody>
                <?php foreach ($students as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['firstName'] . ' ' . $s['lastName']) ?></td>
                        <td><?= htmlspecialchars($s['studentID']) ?></td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="attendance[<?= $s['studentID'] ?>]" checked>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary">Save Attendance</button>
    </form>
</div>
<?php require_once '../../../includes/footer.php'; ?>
