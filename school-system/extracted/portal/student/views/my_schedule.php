<?php
// portal/student/views/my_schedule.php
require_once '../../../config/database.php';
require_once '../../../src/controllers/BaseController.php';
require_once '../../../includes/portal_header.php';

$auth = new BaseController($pdo);
$auth->checkRole(['Student']);

$studentId = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT C.courseID, C.courseName, CS.CRN, U.firstName as instrFirst, U.lastName as instrLast, R.roomName, E.grade 
    FROM Enrollment E 
    JOIN CourseSection CS ON E.CRN = CS.CRN 
    JOIN Course C ON CS.courseID = C.courseID
    JOIN Faculty F ON CS.facultyID = F.facultyID
    JOIN User U ON F.facultyID = U.userID
    JOIN Room R ON CS.roomID = R.roomID
    WHERE E.studentID = :studentId
");
$stmt->execute(['studentId' => $studentId]);
$schedule = $stmt->fetchAll();
?>
<div class="container my-5">
    <a href="../index.php" class="btn btn-outline-secondary mb-3">&larr; Dashboard</a>
    <h2 class="fw-bold mb-4">My Schedule (2026SP)</h2>
    <table class="table table-hover shadow-sm">
        <thead class="table-light">
            <tr><th>CRN</th><th>Course</th><th>Instructor</th><th>Location</th><th>Grade</th></tr>
        </thead>
        <tbody>
            <?php foreach ($schedule as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['CRN']) ?></td>
                    <td><?= htmlspecialchars($item['courseID'] . ' - ' . $item['courseName']) ?></td>
                    <td><?= htmlspecialchars($item['instrFirst'] . ' ' . $item['instrLast']) ?></td>
                    <td><?= htmlspecialchars($item['roomName']) ?></td>
                    <td><?= htmlspecialchars($item['grade'] ?? 'In Progress') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once '../../../includes/footer.php'; ?>
