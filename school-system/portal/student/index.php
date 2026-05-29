<?php
// portal/student/index.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../../config/database.php';
require_once '../../src/controllers/StudentController.php';
require_once '../../includes/portal_header.php';

$auth = new StudentController($pdo);
$studentID = $_SESSION['user_id'];

// Fetch Profile
$stmt = $pdo->prepare("
    SELECT U.userID, U.firstName, U.lastName, S.studentType, S.year, L.email, M.majorName
    FROM User U 
    JOIN Student S ON U.userID = S.studentID
    JOIN Login L ON U.userID = L.userID
    LEFT JOIN StudentMajor SM ON S.studentID = SM.studentID
    LEFT JOIN Major M ON SM.majorID = M.majorID
    WHERE U.userID = :sid
");
$stmt->execute(['sid' => $studentID]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch Holds
$stmt = $pdo->prepare("SELECT H.holdType, H.holdDescription FROM StudentHold SH JOIN Hold H ON SH.holdID = H.holdID WHERE SH.studentID = :sid");
$stmt->execute(['sid' => $studentID]);
$holds = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Semesters where the student is actually enrolled
$stmt = $pdo->prepare("
    SELECT DISTINCT CS.semesterID 
    FROM Enrollment E
    JOIN CourseSection CS ON E.CRN = CS.CRN
    WHERE E.studentID = :sid
    ORDER BY CS.semesterID DESC
");
$stmt->execute(['sid' => $studentID]);
$semesters = $stmt->fetchAll(PDO::FETCH_COLUMN);
$currentSemester = $_GET['term'] ?? ($semesters[0] ?? '');

// Fetch Enrollments
$stmt = $pdo->prepare("
    SELECT E.CRN, C.courseID, C.courseName, U.firstName as instrFirst, U.lastName as instrLast, CS.roomID, E.grade
    FROM Enrollment E
    JOIN CourseSection CS ON E.CRN = CS.CRN
    JOIN Course C ON CS.courseID = C.courseID
    JOIN User U ON CS.facultyID = U.userID
    WHERE E.studentID = :sid AND CS.semesterID = :sem
");
$stmt->execute(['sid' => $studentID, 'sem' => $currentSemester]);
$enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="portal-container">
    <?php 
    $currentPage = 'dashboard';
    include '../../includes/portal-sidebar.php'; 
    ?>

    <main class="portal-main">
        <div class="portal-content">
            <h1>Student Dashboard</h1>
            <p class="subtitle">Welcome back, <?= htmlspecialchars($profile['firstName'] ?? '') ?>! Manage your academic journey below.</p>

            <!-- Profile Overview Card -->
            <div class="card p-4 shadow-sm border-0 mb-4">
                <h3 class="fw-bold">Academic Profile</h3>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <p><strong>ID:</strong> <?= htmlspecialchars($profile['userID'] ?? '') ?></p>
                        <p><strong>Status:</strong> <?= htmlspecialchars(($profile['studentType'] ?? 'N/A') . ' (' . ($profile['year'] ?? 'Year 1') . ')') ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Major:</strong> <?= htmlspecialchars($profile['majorName'] ?? 'Not Declared') ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($profile['email'] ?? '') ?></p>
                    </div>
                </div>
            </div>

            <!-- Holds Notification -->
            <?php if (!empty($holds)): ?>
                <div class="alert alert-danger shadow-sm border-0">
                    <h4 class="fw-bold"><i class="fas fa-exclamation-triangle"></i> Active Holds (<?= count($holds) ?>)</h4>
                    <?php foreach ($holds as $hold): ?>
                        <div class="small"><strong><?= htmlspecialchars($hold['holdType']) ?>:</strong> <?= htmlspecialchars($hold['holdDescription']) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Schedule Section -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h3 class="h5 fw-bold mb-0"><i class="fas fa-calendar-alt"></i> My Schedule</h3>
                    <form method="GET">
                        <select name="term" class="form-select form-select-sm" onchange="this.form.submit()">
                            <?php foreach ($semesters as $sem): ?>
                                <option value="<?= htmlspecialchars($sem) ?>" <?= ($sem === $currentSemester) ? 'selected' : ''; ?>><?= htmlspecialchars($sem) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>CRN</th><th>Course</th><th>Instructor</th><th>Location</th><th>Grade</th></tr>
                        </thead>
                        <tbody>
                            <?php if (empty($enrollments)): ?>
                                <tr><td colspan="5" class="text-center">No enrollments found for this semester.</td></tr>
                            <?php else: ?>
                                <?php foreach ($enrollments as $en): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($en['CRN']) ?></td>
                                        <td><strong><?= htmlspecialchars($en['courseID']) ?></strong><br><small><?= htmlspecialchars($en['courseName']) ?></small></td>
                                        <td><?= htmlspecialchars($en['instrFirst'] . ' ' . $en['instrLast']) ?></td>
                                        <td><?= htmlspecialchars($en['roomID']) ?></td>
                                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($en['grade'] ?? 'In Progress') ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Links Grid -->
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <div class="col"><a href="views/catalog.php" class="card h-100 shadow-sm border-0 p-3 text-decoration-none">
                    <h5 class="fw-bold">Course Catalog</h5>
                    <p class="text-muted small">Browse available classes</p>
                </a></div>
                <div class="col"><a href="registration.php" class="card h-100 shadow-sm border-0 p-3 text-decoration-none">
                    <h5 class="fw-bold">Registration</h5>
                    <p class="text-muted small">Add or drop classes</p>
                </a></div>
                <div class="col"><a href="transcript.php" class="card h-100 shadow-sm border-0 p-3 text-decoration-none">
                    <h5 class="fw-bold">Transcript</h5>
                    <p class="text-muted small">View grades and history</p>
                </a></div>
                <div class="col"><a href="degree_audit.php" class="card h-100 shadow-sm border-0 p-3 text-decoration-none">
                    <h5 class="fw-bold">Degree Audit</h5>
                    <p class="text-muted small">Track graduation progress</p>
                </a></div>
                <div class="col"><a href="advising.php" class="card h-100 shadow-sm border-0 p-3 text-decoration-none">
                    <h5 class="fw-bold">My Advisor</h5>
                    <p class="text-muted small">Schedule an appointment</p>
                </a></div>
            </div>
        </div>
    </main>
</div>
<?php require_once '../../includes/footer.php'; ?>
