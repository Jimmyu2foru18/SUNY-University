<?php
// portal/faculty/views/grading.php
require_once '../../../config/database.php';
require_once '../../../src/controllers/BaseController.php';
require_once '../../../includes/portal_header.php';

$auth = new BaseController($pdo);
$auth->checkRole(['Faculty']);

$crn = $_GET['crn'] ?? '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['grades'])) {
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("UPDATE Enrollment SET grade = :grade WHERE studentID = :studentID AND CRN = :crn");
        foreach ($_POST['grades'] as $studentID => $grade) {
            $stmt->execute(['grade' => $grade, 'studentID' => $studentID, 'crn' => $crn]);
        }
        $pdo->commit();
        $message = "Grades updated successfully!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $message = "Error updating grades: " . $e->getMessage();
    }
}

// Fetch Students in this CRN
$stmt = $pdo->prepare("
    SELECT U.firstName, U.lastName, S.studentID, E.grade
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
    <h2 class="fw-bold mb-4">Grading: <?= htmlspecialchars($crn) ?></h2>

    <?php if ($message): ?>
        <div class="alert <?= strpos($message, 'Error') !== false ? 'alert-danger' : 'alert-success' ?>" role="alert">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <table class="table table-hover shadow-sm">
            <thead class="table-light">
                <tr><th>Student Name</th><th>Student ID</th><th>Grade</th></tr>
            </thead>
            <tbody>
                <?php foreach ($students as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['firstName'] . ' ' . $s['lastName']) ?></td>
                        <td><?= htmlspecialchars($s['studentID']) ?></td>
                        <td><input type="text" name="grades[<?= $s['studentID'] ?>]" value="<?= htmlspecialchars($s['grade']) ?>" class="form-control form-control-sm" style="width: 60px;"></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary">Save Grades</button>
    </form>
</div>
<?php require_once '../../../includes/footer.php'; ?>
