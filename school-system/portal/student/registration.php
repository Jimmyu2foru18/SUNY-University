<?php
// portal/student/registration.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../../config/database.php';
require_once '../../src/controllers/StudentController.php';
require_once '../../includes/portal_header.php';

$auth = new StudentController($pdo);
$studentID = $_SESSION['user_id'];

// Handle AJAX Registration Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $crn = $_POST['crn'] ?? '';
    
    try {
        if ($_POST['action'] === 'enroll') {
            $stmt = $pdo->prepare("INSERT INTO Enrollment (studentID, CRN) VALUES (:sid, :crn)");
            $result = $stmt->execute(['sid' => $studentID, 'crn' => $crn]);
            echo json_encode(['ok' => $result, 'message' => $result ? 'Enrolled successfully!' : 'Error enrolling.']);
        } elseif ($_POST['action'] === 'drop') {
            $stmt = $pdo->prepare("DELETE FROM Enrollment WHERE studentID = :sid AND CRN = :crn");
            $result = $stmt->execute(['sid' => $studentID, 'crn' => $crn]);
            echo json_encode(['ok' => $result, 'message' => $result ? 'Dropped successfully.' : 'Error dropping.']);
        }
    } catch (Exception $e) {
        echo json_encode(['ok' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// Fetch Semesters
$stmt = $pdo->query("SELECT DISTINCT semesterID FROM CourseSection ORDER BY semesterID DESC");
$semesters = $stmt->fetchAll(PDO::FETCH_COLUMN);
$currentSemester = $_GET['semester'] ?? ($semesters[0] ?? '2026SP');

// Fetch Holds
$stmt = $pdo->prepare("SELECT H.holdDescription FROM StudentHold SH JOIN Hold H ON SH.holdID = H.holdID WHERE SH.studentID = :sid");
$stmt->execute(['sid' => $studentID]);
$holds = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Available Courses
$stmt = $pdo->prepare("
    SELECT CS.CRN, C.courseID, C.courseName, U.firstName, U.lastName, CS.availableSeats 
    FROM CourseSection CS 
    JOIN Course C ON CS.courseID = C.courseID 
    JOIN User U ON CS.facultyID = U.userID 
    WHERE CS.semesterID = :sem
");
$stmt->execute(['sem' => $currentSemester]);
$availableCourses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Current Enrollments
$stmt = $pdo->prepare("
    SELECT E.CRN, C.courseID, C.courseName 
    FROM Enrollment E 
    JOIN CourseSection CS ON E.CRN = CS.CRN 
    JOIN Course C ON CS.courseID = C.courseID 
    WHERE E.studentID = :sid AND CS.semesterID = :sem
");
$stmt->execute(['sid' => $studentID, 'sem' => $currentSemester]);
$currentEnrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="portal-container">
    <?php include '../../includes/portal-sidebar.php'; ?>
    <main class="portal-main">
        <div class="portal-content">
            <h1>Course Registration</h1>
            <p class="subtitle">Search and register for classes for the <strong><?= htmlspecialchars($currentSemester) ?></strong> semester.</p>

            <?php if (!empty($holds)): ?>
                <div class="alert alert-danger shadow-sm border-0">
                    <h4 class="fw-bold"><i class="fas fa-ban"></i> Registration Restricted</h4>
                    <p>You have active holds that prevent registration.</p>
                    <ul class="mb-0">
                        <?php foreach ($holds as $hold): ?>
                            <li><?= htmlspecialchars($hold['holdDescription']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php else: ?>
                <!-- Registration Search Form -->
                <div class="card p-3 mb-4 border-0 shadow-sm">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Semester</label>
                            <select name="semester" class="form-select" onchange="this.form.submit()">
                                <?php foreach ($semesters as $sem): ?>
                                    <option value="<?= htmlspecialchars($sem) ?>" <?= ($sem === $currentSemester) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($sem) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Search CRN/Course</label>
                            <input type="text" id="courseSearch" class="form-control" placeholder="Enter CRN or Course ID...">
                        </div>
                    </form>
                </div>

                <!-- Current Schedule Summary -->
                <h3 class="fw-bold mb-3">My Current Schedule (<?= htmlspecialchars($currentSemester) ?>)</h3>
                <div class="row mb-5">
                    <?php if (empty($currentEnrollments)): ?>
                        <div class="col-12 text-muted">Not enrolled in any courses for this semester.</div>
                    <?php else: ?>
                        <?php foreach ($currentEnrollments as $en): ?>
                            <div class="col-md-4 mb-3">
                                <div class="card p-3 shadow-sm border-0 d-flex flex-row justify-content-between align-items-center">
                                    <div>
                                        <span class="fw-bold"><?= htmlspecialchars($en['courseID']) ?></span><br>
                                        <small class="text-muted"><?= htmlspecialchars($en['courseName']) ?></small>
                                    </div>
                                    <button class="btn btn-sm btn-outline-danger" onclick="handleAction('drop', '<?= htmlspecialchars($en['CRN']) ?>')">Drop</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Available Courses Table -->
                <h3 class="fw-bold mb-3">Available Classes</h3>
                <div class="card border-0 shadow-sm">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr><th>CRN</th><th>Course</th><th>Instructor</th><th>Seats</th><th>Action</th></tr>
                            </thead>
                            <tbody id="courseTableBody">
                                <?php foreach ($availableCourses as $course): ?>
                                    <tr class="course-row" data-search="<?= strtolower($course['CRN'] . ' ' . $course['courseID'] . ' ' . $course['courseName']) ?>">
                                        <td><strong><?= htmlspecialchars($course['CRN']) ?></strong></td>
                                        <td><?= htmlspecialchars($course['courseID']) ?><br><small><?= htmlspecialchars($course['courseName']) ?></small></td>
                                        <td><?= htmlspecialchars($course['firstName'] . ' ' . $course['lastName']) ?></td>
                                        <td><?= htmlspecialchars($course['availableSeats'] ?? '0') ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="handleAction('enroll', '<?= htmlspecialchars($course['CRN']) ?>')">Add</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<script>
document.getElementById('courseSearch').addEventListener('input', function(e) {
    const query = e.target.value.toLowerCase();
    document.querySelectorAll('.course-row').forEach(row => {
        row.style.display = row.getAttribute('data-search').includes(query) ? '' : 'none';
    });
});

async function handleAction(action, crn) {
    if (!confirm('Are you sure you want to ' + action + ' this course?')) return;

    try {
        const response = await fetch('registration.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ action: action, crn: crn })
        });
        const res = await response.json();
        alert(res.message);
        if (res.ok) location.reload();
    } catch (e) {
        alert('Error processing request.');
    }
}
</script>
<?php require_once '../../includes/footer.php'; ?>
