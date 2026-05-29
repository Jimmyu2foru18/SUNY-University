<?php
// portal/statstaff/views/master_schedule.php
ini_set('display_errors', 0);
error_reporting(E_ALL);
require_once '../../../config/database.php';
require_once '../../../src/controllers/StatStaffController.php';

// Handle AJAX actions - Must be before any output
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    $auth = new StatStaffController($pdo);
    $crn = $_GET['crn'] ?? '';
    if ($_GET['action'] === 'roster') {
        $stmt = $pdo->prepare("SELECT U.firstName, U.lastName, S.studentID, E.grade FROM Enrollment E JOIN Student S ON E.studentID = S.studentID JOIN User U ON S.studentID = U.userID WHERE E.CRN = :crn");
        $stmt->execute(['crn' => $crn]);
        echo json_encode(['ok' => true, 'students' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    } elseif ($_GET['action'] === 'get_details') {
        $stmt = $pdo->prepare("SELECT C.courseID, C.courseName, C.credits, C.courseDescription FROM Course C JOIN CourseSection CS ON C.courseID = CS.courseID WHERE CS.CRN = :crn");
        $stmt->execute(['crn' => $crn]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(['ok' => $data ? true : false, 'data' => $data]);
    }
    exit;
}

require_once '../../../includes/portal_header.php';
$auth = new StatStaffController($pdo);

// Fetch all semesters
$stmt = $pdo->query("SELECT DISTINCT semesterID FROM CourseSection ORDER BY semesterID DESC");
$semesters = $stmt->fetchAll(PDO::FETCH_COLUMN);
$currentSemester = $_GET['semester'] ?? ($semesters[0] ?? '');

// Fetch Schedule Data
try {
    $stmt = $pdo->prepare("
        SELECT CS.CRN, CS.semesterID, C.courseID, C.courseName, U.firstName, U.lastName, CS.roomID, CS.availableSeats, CS.timeSlotID,
               COUNT(E.CRN) as enrolledCount
        FROM CourseSection CS
        JOIN Course C ON CS.courseID = C.courseID
        JOIN User U ON CS.facultyID = U.userID
        LEFT JOIN Enrollment E ON CS.CRN = E.CRN
        WHERE CS.semesterID = :semester
        GROUP BY CS.CRN
        ORDER BY C.courseID, CS.CRN
    ");
    $stmt->execute(['semester' => $currentSemester]);
    $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Database Query Error: " . $e->getMessage());
}
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="../index.php" class="btn btn-outline-secondary mb-3">&larr; Dashboard</a>
            <h2 class="fw-bold">Master Schedule</h2>
            <p>View all course offerings for <strong><?= htmlspecialchars($currentSemester) ?></strong>.</p>
        </div>
        <form method="GET" class="d-flex gap-2">
            <select name="semester" class="form-select" onchange="this.form.submit()">
                <?php foreach ($semesters as $s): ?>
                    <option value="<?= htmlspecialchars($s) ?>" <?= $s === $currentSemester ? 'selected' : '' ?>><?= htmlspecialchars($s) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" id="search" class="form-control" placeholder="Search CRN/Course...">
        </form>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>CRN</th>
                        <th>Course</th>
                        <th>Instructor</th>
                        <th>Location</th>
                        <th>Enrolled/Cap</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php foreach ($sections as $s): ?>
                        <tr class="schedule-row" data-search="<?= strtolower($s['CRN'] . ' ' . $s['courseID'] . ' ' . $s['courseName']) ?>">
                            <td><?= htmlspecialchars($s['CRN']) ?></td>
                            <td><span class="fw-bold"><?= htmlspecialchars($s['courseID']) ?></span><br><small><?= htmlspecialchars($s['courseName']) ?></small></td>
                            <td><?= htmlspecialchars($s['firstName'] . ' ' . $s['lastName']) ?></td>
                            <td><?= htmlspecialchars($s['roomID']) ?> (Slot: <?= htmlspecialchars($s['timeSlotID']) ?>)</td>
                            <td><?= $s['enrolledCount'] ?> / <?= $s['availableSeats'] + $s['enrolledCount'] ?></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-secondary" onclick="showDetails('<?= htmlspecialchars($s['CRN']) ?>')">Details</button>
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewRoster('<?= htmlspecialchars($s['CRN']) ?>')">Roster</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="infoModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
    <div class="modal-content bg-white p-4 rounded shadow" style="width: 500px; max-height: 80vh; overflow-y: auto;">
        <h3 id="modalTitle" class="fw-bold">Course Info</h3>
        <hr>
        <div id="modalBody"></div>
        <button class="btn btn-secondary mt-3" onclick="hideModal()">Close</button>
    </div>
</div>

<script>
function showModal() { document.getElementById('infoModal').style.display = 'flex'; }
function hideModal() { document.getElementById('infoModal').style.display = 'none'; }

document.getElementById('search').addEventListener('input', function(e) {
    const query = e.target.value.toLowerCase();
    document.querySelectorAll('.schedule-row').forEach(row => {
        row.style.display = row.getAttribute('data-search').includes(query) ? '' : 'none';
    });
});

function showDetails(crn) {
    fetch('master_schedule.php?action=get_details&crn=' + crn)
    .then(r => r.json())
    .then(res => {
        if (res.ok) {
            const d = res.data;
            document.getElementById('modalTitle').textContent = 'Course Details - ' + d.courseID;
            document.getElementById('modalBody').innerHTML = `<p><strong>Name:</strong> ${d.courseName}</p><p><strong>Credits:</strong> ${d.credits}</p><p><strong>Description:</strong> ${d.courseDescription}</p>`;
            showModal();
        }
    });
}

function viewRoster(crn) {
    fetch('master_schedule.php?action=roster&crn=' + crn)
    .then(r => r.json())
    .then(res => {
        if (res.ok) {
            let html = '<table class="table"><thead><tr><th>ID</th><th>Name</th></tr></thead><tbody>';
            res.students.forEach(s => html += `<tr><td>${s.studentID}</td><td>${s.firstName} ${s.lastName}</td></tr>`);
            html += '</tbody></table>';
            document.getElementById('modalTitle').textContent = 'Roster - ' + crn;
            document.getElementById('modalBody').innerHTML = html;
            showModal();
        }
    });
}
</script>
<?php require_once '../../../includes/footer.php'; ?>
