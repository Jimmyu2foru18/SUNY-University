<?php
// portal/statstaff/views/course_details.php
require_once '../../../config/database.php';
require_once '../../../src/controllers/StatStaffController.php';
require_once '../../../includes/portal_header.php';

$auth = new StatStaffController($pdo);

$courseID = $_GET['courseID'] ?? '';

// Fetch Course Details
$stmt = $pdo->prepare("SELECT * FROM Course WHERE courseID = :id");
$stmt->execute(['id' => $courseID]);
$course = $stmt->fetch();

// Fetch Prerequisites
$stmt = $pdo->prepare("
    SELECT GROUP_CONCAT(prerequisiteID) as prereqs 
    FROM CoursePrerequisite 
    WHERE courseID = :id
");
$stmt->execute(['id' => $courseID]);
$prereqs = $stmt->fetchColumn();
?>
<div class="container my-5">
    <a href="master_schedule.php" class="btn btn-outline-secondary mb-3">&larr; Back to Schedule</a>
    <?php if ($course): ?>
        <div class="card p-4 shadow-sm border-0">
            <h2 class="fw-bold mb-4">Course Details - <?= htmlspecialchars($course['courseID']) ?></h2>
            <p><strong>Course:</strong> <?= htmlspecialchars($course['courseName']) ?></p>
            <p><strong>Credits:</strong> <?= htmlspecialchars($course['credits']) ?></p>
            <p><strong>Description:</strong> <?= htmlspecialchars($course['courseDescription'] ?? 'No description available.') ?></p>
            <p><strong>Prerequisites:</strong> <?= htmlspecialchars($prereqs ?? 'None') ?></p>
            <button class="btn btn-secondary" onclick="window.history.back()">Close</button>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">Course not found.</div>
    <?php endif; ?>
</div>
<?php require_once '../../../includes/footer.php'; ?>
