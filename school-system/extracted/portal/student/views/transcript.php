<?php
// portal/student/views/transcript.php
require_once '../../../config/database.php';
require_once '../../../src/controllers/BaseController.php';
require_once '../../../includes/portal_header.php';

$auth = new BaseController($pdo);
$auth->checkRole(['Student']);

$studentId = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT C.courseID, C.courseName, E.grade 
    FROM Enrollment E
    JOIN CourseSection CS ON E.CRN = CS.CRN
    JOIN Course C ON CS.courseID = C.courseID
    WHERE E.studentID = :studentId
");
$stmt->execute(['studentId' => $studentId]);
$transcript = $stmt->fetchAll();
?>
<div class="container my-5">
    <a href="../index.php" class="btn btn-outline-secondary mb-3">&larr; Dashboard</a>
    <h2 class="fw-bold mb-4">Transcript & History</h2>
    <table class="table table-striped shadow-sm">
        <thead class="table-light">
            <tr><th>Course ID</th><th>Course Name</th><th>Grade</th></tr>
        </thead>
        <tbody>
            <?php foreach ($transcript as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['courseID']) ?></td>
                    <td><?= htmlspecialchars($item['courseName']) ?></td>
                    <td><?= htmlspecialchars($item['grade'] ?? 'In Progress') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once '../../../includes/footer.php'; ?>
