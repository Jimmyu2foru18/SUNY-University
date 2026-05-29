<?php
// portal/statstaff/views/enrollments.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../../../config/database.php';
require_once '../../../src/controllers/StatStaffController.php';
require_once '../../../includes/portal_header.php';

$auth = new StatStaffController($pdo);

// Fetch all semesters
$stmt = $pdo->query("SELECT DISTINCT semesterID FROM CourseSection ORDER BY semesterID DESC");
$semesters = $stmt->fetchAll(PDO::FETCH_COLUMN);
$currentSemester = $_GET['semester'] ?? ($semesters[0] ?? '');

// Fetch current enrollments
$stmt = $pdo->prepare("
    SELECT e.studentID, e.CRN, u.firstName, u.lastName, c.courseID, c.courseName
    FROM Enrollment e
    JOIN User u ON e.studentID = u.userID
    JOIN CourseSection cs ON e.CRN = cs.CRN
    JOIN Course c ON cs.courseID = c.courseID
    WHERE cs.semesterID = :sem
    ORDER BY u.lastName ASC, u.firstName ASC
");
$stmt->execute(['sem' => $currentSemester]);
$enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="../index.php" class="btn btn-outline-secondary mb-3">&larr; Dashboard</a>
            <h2 class="fw-bold">Manage Enrollments</h2>
        </div>
    </div>

    <!-- Filters -->
    <div class="card p-3 mb-4 border-0 shadow-sm">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Semester</label>
                <select name="semester" class="form-select" onchange="this.form.submit()">
                    <?php foreach ($semesters as $sem): ?>
                        <option value="<?= htmlspecialchars($sem) ?>" <?= ($sem === $currentSemester) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($sem) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>Student</th><th>CRN</th><th>Course</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($enrollments)): ?>
                        <tr><td colspan="3" class="text-center">No enrollments found for this semester.</td></tr>
                    <?php else: ?>
                        <?php foreach ($enrollments as $e): ?>
                            <tr>
                                <td><?= htmlspecialchars($e['firstName'] . ' ' . $e['lastName'] . ' (' . $e['studentID'] . ')') ?></td>
                                <td><?= htmlspecialchars($e['CRN']) ?></td>
                                <td><?= htmlspecialchars($e['courseID'] . ' - ' . $e['courseName']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../../../includes/footer.php'; ?>
