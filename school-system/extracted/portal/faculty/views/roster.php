<?php
// portal/faculty/views/roster.php
require_once '../../../config/database.php';
require_once '../../../src/controllers/BaseController.php';
require_once '../../../includes/portal_header.php';

$auth = new BaseController($pdo);
$auth->checkRole(['Faculty']);

$crn = $_GET['crn'] ?? '';

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
    <h2 class="fw-bold mb-4">Class Roster: <?= htmlspecialchars($crn) ?></h2>
    <table class="table table-striped shadow-sm">
        <thead class="table-light">
            <tr><th>Student Name</th><th>Student ID</th></tr>
        </thead>
        <tbody>
            <?php foreach ($students as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['firstName'] . ' ' . $s['lastName']) ?></td>
                    <td><?= htmlspecialchars($s['studentID']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once '../../../includes/footer.php'; ?>
