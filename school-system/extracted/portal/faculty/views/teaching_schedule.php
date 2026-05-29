<?php
// portal/faculty/views/teaching_schedule.php
require_once '../../../config/database.php';
require_once '../../../src/controllers/BaseController.php';
require_once '../../../includes/portal_header.php';

$auth = new BaseController($pdo);
$auth->checkRole(['Faculty']);

$facultyId = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT C.courseName, CS.CRN, R.roomName
    FROM CourseSection CS 
    JOIN Course C ON CS.courseID = C.courseID
    JOIN Room R ON CS.roomID = R.roomID
    WHERE CS.facultyID = :facultyId
");
$stmt->execute(['facultyId' => $facultyId]);
$classes = $stmt->fetchAll();
?>
<div class="container my-5">
    <a href="../index.php" class="btn btn-outline-secondary mb-3">&larr; Dashboard</a>
    <h2 class="fw-bold mb-4">My Teaching Schedule</h2>
    <table class="table table-hover shadow-sm">
        <thead class="table-light">
            <tr><th>Course Name</th><th>CRN</th><th>Location</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($classes as $class): ?>
                <tr>
                    <td><?= htmlspecialchars($class['courseName']) ?></td>
                    <td><?= htmlspecialchars($class['CRN']) ?></td>
                    <td><?= htmlspecialchars($class['roomName']) ?></td>
                    <td>
                        <a href="grading.php?crn=<?= htmlspecialchars($class['CRN']) ?>" class="btn btn-sm btn-primary">Grades</a>
                        <a href="roster.php?crn=<?= htmlspecialchars($class['CRN']) ?>" class="btn btn-sm btn-outline-secondary">Roster</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once '../../../includes/footer.php'; ?>
