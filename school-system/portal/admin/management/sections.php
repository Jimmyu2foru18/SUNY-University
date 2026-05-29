<?php
// portal/admin/management/sections.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../../../config/database.php';
require_once '../../../src/controllers/AdminController.php';
require_once '../../../includes/portal_header.php';

$auth = new AdminController($pdo);

// Handle AJAX actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    try {
        if ($_POST['action'] === 'create') {
            $stmt = $pdo->prepare("INSERT INTO CourseSection (CRN, courseID, sectionNumber, semesterID, facultyID, roomID, timeSlotID, availableSeats) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_POST['CRN'], $_POST['courseID'], $_POST['sectionNumber'], $_POST['semesterID'], $_POST['facultyID'], $_POST['roomID'], $_POST['timeSlotID'], $_POST['availableSeats']]);
            echo json_encode(['ok' => true, 'message' => 'Section created.']);
        } elseif ($_POST['action'] === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM CourseSection WHERE CRN = ?");
            $stmt->execute([$_POST['CRN']]);
            echo json_encode(['ok' => true, 'message' => 'Section deleted.']);
        }
    } catch (Exception $e) {
        echo json_encode(['ok' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// Fetch Data
$stmt = $pdo->query("SELECT DISTINCT semesterID FROM CourseSection ORDER BY semesterID DESC");
$semesters = $stmt->fetchAll(PDO::FETCH_COLUMN);
$currentSemester = $_GET['semester'] ?? ($semesters[0] ?? '2026SP');

$stmt = $pdo->query("SELECT courseID, courseName FROM Course");
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT userID, firstName, lastName FROM User WHERE userID IN (SELECT facultyID FROM Faculty)");
$faculty = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT roomID FROM Room");
$rooms = $stmt->fetchAll(PDO::FETCH_COLUMN);

$stmt = $pdo->query("SELECT timeSlotID FROM TimeSlot");
$timeSlots = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Fetch Schedule
$stmt = $pdo->prepare("
    SELECT CS.CRN, CS.courseID, C.courseName, U.firstName, U.lastName, CS.timeSlotID, CS.availableSeats,
           COUNT(E.CRN) as enrolledCount
    FROM CourseSection CS
    JOIN Course C ON CS.courseID = C.courseID
    JOIN User U ON CS.facultyID = U.userID
    LEFT JOIN Enrollment E ON CS.CRN = E.CRN
    WHERE CS.semesterID = :semester
    GROUP BY CS.CRN
");
$stmt->execute(['semester' => $currentSemester]);
$sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="../index.php" class="btn btn-outline-secondary mb-3">&larr; Dashboard</a>
            <h2 class="fw-bold">Course Sections</h2>
        </div>
        <button class="btn btn-primary" onclick="showAddModal()">Create New Section</button>
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
                    <tr><th>CRN</th><th>Course</th><th>Instructor</th><th>TimeSlot</th><th>Enrolled/Cap</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($sections as $s): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($s['CRN']) ?></strong></td>
                            <td><?= htmlspecialchars($s['courseID'] . ' - ' . $s['courseName']) ?></td>
                            <td><?= htmlspecialchars($s['firstName'] . ' ' . $s['lastName']) ?></td>
                            <td>Slot: <?= htmlspecialchars($s['timeSlotID']) ?></td>
                            <td><?= $s['enrolledCount'] ?> / <?= $s['availableSeats'] + $s['enrolledCount'] ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteSection('<?= htmlspecialchars($s['CRN']) ?>')">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="addModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
    <div class="modal-content bg-white p-4 rounded shadow" style="width: 500px;">
        <h3>Create Course Section</h3>
        <form id="addForm" style="margin-top: 20px;">
            <input type="hidden" name="action" value="create">
            <div class="mb-3">
                <label class="form-label">Course</label>
                <select name="courseID" id="course_id" class="form-select" required>
                    <?php foreach ($courses as $c): ?>
                        <option value="<?= $c['courseID'] ?>"><?= htmlspecialchars($c['courseID'] . ' - ' . $c['courseName']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Semester</label>
                    <input type="text" name="semesterID" id="sem_id" class="form-control" value="<?= htmlspecialchars($currentSemester) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Section #</label>
                    <input type="text" name="sectionNumber" id="sec_num" class="form-control" placeholder="01" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">CRN</label>
                <input type="text" name="CRN" id="crn_id" class="form-control" placeholder="Generated Automatically" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Instructor</label>
                <select name="facultyID" class="form-select" required>
                    <?php foreach ($faculty as $f): ?>
                        <option value="<?= $f['userID'] ?>"><?= htmlspecialchars($f['firstName'] . ' ' . $f['lastName']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Room</label>
                    <select name="roomID" class="form-select" required>
                        <?php foreach ($rooms as $r): ?>
                            <option value="<?= $r ?>"><?= htmlspecialchars($r) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">TimeSlot</label>
                    <select name="timeSlotID" class="form-select" required>
                        <?php foreach ($timeSlots as $t): ?>
                            <option value="<?= $t ?>"><?= htmlspecialchars($t) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Seats</label>
                    <input type="number" name="availableSeats" class="form-control" value="30" required>
                </div>
            </div>
            <button type="button" class="btn btn-secondary" onclick="hideAddModal()">Cancel</button>
            <button type="submit" class="btn btn-primary">Create Section</button>
        </form>
    </div>
</div>

<script>
function showAddModal() { document.getElementById('addModal').style.display = 'flex'; }
function hideAddModal() { document.getElementById('addModal').style.display = 'none'; }

function updateCRN() {
    const course = document.getElementById('course_id').value;
    const sem = document.getElementById('sem_id').value;
    const sec = document.getElementById('sec_num').value;
    if (course && sem && sec) {
        document.getElementById('crn_id').value = course + '-' + sem + '-' + sec;
    }
}
['course_id', 'sem_id', 'sec_num'].forEach(id => {
    document.getElementById(id).addEventListener('change', updateCRN);
    document.getElementById(id).addEventListener('keyup', updateCRN);
});

document.getElementById('addForm').onsubmit = function(e) {
    e.preventDefault();
    fetch('sections.php', {
        method: 'POST',
        body: new FormData(this)
    })
    .then(r => r.json())
    .then(res => {
        alert(res.message);
        if (res.ok) location.reload();
    });
};

function deleteSection(crn) {
    if (!confirm('Are you sure you want to delete this section?')) return;
    fetch('sections.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ action: 'delete', CRN: crn })
    })
    .then(r => r.json())
    .then(res => {
        alert(res.message);
        if (res.ok) location.reload();
    });
}
</script>
<?php require_once '../../../includes/footer.php'; ?>
