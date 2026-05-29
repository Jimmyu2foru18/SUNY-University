<?php
// portal/faculty/schedule.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../../config/database.php';
require_once '../../src/controllers/FacultyController.php';
require_once '../../includes/portal_header.php';

$auth = new FacultyController($pdo);
$facultyID = $_SESSION['user_id'];

// Fetch semesters for filter
$stmt = $pdo->query("SELECT DISTINCT semesterID FROM CourseSection ORDER BY semesterID DESC");
$semesters = $stmt->fetchAll(PDO::FETCH_COLUMN);
$currentSemester = $_GET['semester'] ?? ($semesters[0] ?? '2026SP');

// Fetch Schedule Data
$stmt = $pdo->prepare("
    SELECT CS.CRN, C.courseID, C.courseName, CS.days, CS.time, CS.roomID, CS.capacity,
           COUNT(E.CRN) as enrolledCount
    FROM CourseSection CS
    JOIN Course C ON CS.courseID = C.courseID
    LEFT JOIN Enrollment E ON CS.CRN = E.CRN
    WHERE CS.facultyID = :fid AND CS.semesterID = :sem
    GROUP BY CS.CRN
    ORDER BY CS.CRN
");
$stmt->execute(['fid' => $facultyID, 'sem' => $currentSemester]);
$sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "My Schedule - Bridgeport University";
?>

<div class="portal-container">
    <?php 
    $currentPage = 'schedule';
    include '../../includes/portal-sidebar.php'; 
    ?>

    <main class="portal-main">
        <div class="portal-content">
            <h1>My Teaching Schedule</h1>
            <p class="subtitle">Weekly class schedule for <strong><?= htmlspecialchars($currentSemester); ?></strong>.</p>

            <!-- Filters -->
            <div class="search-box">
                <form method="GET" class="filter-form">
                    <div class="filter-group">
                        <label>Semester</label>
                        <select name="semester" class="form-control" onchange="this.form.submit()">
                            <?php foreach ($semesters as $sem): ?>
                                <option value="<?= htmlspecialchars($sem); ?>" <?= ($sem === $currentSemester) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($sem); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>CRN</th>
                            <th>Course</th>
                            <th>Days</th>
                            <th>Time</th>
                            <th>Location</th>
                            <th>Enrolled</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($sections)): ?>
                            <tr><td colspan="6" class="text-center">No classes scheduled for this semester.</td></tr>
                        <?php else: ?>
                            <?php foreach ($sections as $s): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($s['CRN']); ?></strong></td>
                                    <td><?= htmlspecialchars($s['courseID'] . ' - ' . $s['courseName']); ?></td>
                                    <td><?= htmlspecialchars($s['days']); ?></td>
                                    <td><?= htmlspecialchars($s['time'] ?: 'TBA'); ?></td>
                                    <td><?= htmlspecialchars($s['roomID']); ?></td>
                                    <td><?= $s['enrolledCount']; ?> / <?= $s['capacity']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php require_once '../../includes/footer.php'; ?>
</body>
</html>
