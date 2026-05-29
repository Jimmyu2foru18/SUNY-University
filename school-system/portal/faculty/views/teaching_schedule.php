<?php
// portal/faculty/views/teaching_schedule.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../../../config/database.php';
require_once '../../../src/controllers/FacultyController.php';
require_once '../../../includes/portal_header.php';

$auth = new FacultyController($pdo);
$facultyID = $_SESSION['user_id'];

// Fetch semesters for filter
$stmt = $pdo->query("SELECT DISTINCT semesterID FROM CourseSection ORDER BY semesterID DESC");
$semesters = $stmt->fetchAll(PDO::FETCH_COLUMN);
$currentSemester = $_GET['semester'] ?? ($semesters[0] ?? '2026SP');

// Fetch Schedule Data
$stmt = $pdo->prepare("
    SELECT CS.CRN, CS.semesterID, C.courseID, C.courseName, CS.roomID, CS.timeSlotID,
           (SELECT GROUP_CONCAT(SUBSTRING(d.day_name, 1, 3) ORDER BY d.daysID SEPARATOR '/') 
            FROM TimeSlotDay tsd 
            JOIN Days d ON tsd.daysID = d.daysID 
            WHERE tsd.timeSlotID = CS.timeSlotID) as days_formatted,
           (SELECT p.startTime 
            FROM TimeSlotPeriod tsp 
            JOIN Period p ON tsp.periodID = p.periodID 
            WHERE tsp.timeSlotID = CS.timeSlotID LIMIT 1) as time_formatted,
           COUNT(E.CRN) as enrolledCount
    FROM CourseSection CS
    JOIN Course C ON CS.courseID = C.courseID
    LEFT JOIN Enrollment E ON CS.CRN = E.CRN
    WHERE CS.facultyID = :fid AND CS.semesterID = :sem
    GROUP BY CS.CRN
    ORDER BY C.courseID, CS.CRN
");
$stmt->execute(['fid' => $facultyID, 'sem' => $currentSemester]);
$sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="portal-container">
    <?php 
    $currentPage = 'schedule';
    include '../../../includes/portal-sidebar.php'; 
    ?>

    <main class="portal-main">
        <div class="portal-content">
            <h1>My Teaching Schedule</h1>
            <p class="subtitle">Weekly class schedule for <strong><?= htmlspecialchars($currentSemester) ?></strong>.</p>

            <!-- Filters -->
            <div class="card p-3 mb-4 border-0 shadow-sm">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Semester</label>
                        <select name="semester" class="form-select" onchange="this.form.submit()">
                            <?php foreach ($semesters as $s): ?>
                                <option value="<?= htmlspecialchars($s) ?>" <?= ($s === $currentSemester) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($s) ?>
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
                            <tr><th>CRN</th><th>Course</th><th>Time</th><th>Location</th><th>Enrolled</th></tr>
                        </thead>
                        <tbody>
                            <?php if (empty($sections)): ?>
                                <tr><td colspan="5" class="text-center">No classes scheduled for this semester.</td></tr>
                            <?php else: ?>
                                <?php foreach ($sections as $s): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($s['CRN']) ?></strong></td>
                                        <td><?= htmlspecialchars($s['courseID'] . ' - ' . $s['courseName']) ?></td>
                                        <td><?= htmlspecialchars(($s['days_formatted'] ?? 'TBA') . ' ' . ($s['time_formatted'] ?? '')) ?></td>
                                        <td><?= htmlspecialchars($s['roomID'] . ' (Slot: ' . $s['timeSlotID'] . ')') ?></td>
                                        <td><?= $s['enrolledCount'] ?> / 30</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once '../../../includes/footer.php'; ?>
