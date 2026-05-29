<?php
// portal/master-schedule.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../config/database.php';
require_once '../includes/portal_header.php';

// Access control
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit;
}

$userID = $_SESSION['user_id'];
$userType = $_SESSION['user_type'];

// Handle AJAX Roster (Faculty only)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'roster') {
    header('Content-Type: application/json');
    if ($userType !== 'Faculty') {
        echo json_encode(['ok' => false, 'message' => 'Unauthorized']); exit;
    }
    $crn = $_GET['crn'] ?? '';
    $stmt = $pdo->prepare("SELECT U.firstName, U.lastName, S.studentID, E.grade FROM Enrollment E JOIN Student S ON E.studentID = S.studentID JOIN User U ON S.studentID = U.userID WHERE E.CRN = :crn");
    $stmt->execute(['crn' => $crn]);
    echo json_encode(['ok' => true, 'students' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    exit;
}

// Handle AJAX Details
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_details') {
    header('Content-Type: application/json');
    $crn = $_GET['crn'] ?? '';
    $stmt = $pdo->prepare("SELECT C.courseID, C.courseName, C.credits, C.courseDescription as courseDescription, 
                           (SELECT GROUP_CONCAT(prerequisiteID) FROM CoursePrerequisite WHERE courseID = C.courseID) as prerequisites 
                           FROM Course C JOIN CourseSection CS ON C.courseID = CS.courseID WHERE CS.CRN = :crn");
    $stmt->execute(['crn' => $crn]);
    $details = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode(['ok' => ($details ? true : false), 'data' => $details]);
    exit;
}

// Fetch Semesters & Departments
$stmt = $pdo->query("SELECT DISTINCT semesterID FROM CourseSection ORDER BY semesterID DESC");
$semesters = $stmt->fetchAll(PDO::FETCH_COLUMN);
$currentSemester = $_GET['semester'] ?? ($semesters[0] ?? '2026SP');

$stmt = $pdo->query("SELECT departmentID, departmentName FROM Department ORDER BY departmentName ASC");
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Schedule
$query = "
    SELECT CS.CRN, CS.courseID, C.courseName, U.firstName as instructorFirst, U.lastName as instructorLast, 
           CS.roomID as buildingName, CS.sectionNumber as roomNumber, CS.days, CS.time as start_time, CS.time as end_time, CS.capacity, CS.facultyID,
           COUNT(E.CRN) as enrolledCount
    FROM CourseSection CS
    JOIN Course C ON CS.courseID = C.courseID
    JOIN User U ON CS.facultyID = U.userID
    LEFT JOIN Enrollment E ON CS.CRN = E.CRN
    WHERE CS.semesterID = :semester
";
$params = ['semester' => $currentSemester];

if ($userType === 'Faculty' && ($_GET['view'] ?? '') === 'my') {
    $query .= " AND CS.facultyID = :fid";
    $params['fid'] = $userID;
}
if (!empty($_GET['crn'])) {
    $query .= " AND (CS.CRN = :crn OR C.courseID = :crn)";
    $params['crn'] = $_GET['crn'];
}
if (!empty($_GET['department'])) {
    $query .= " AND C.departmentID = :dept";
    $params['dept'] = $_GET['department'];
}
if (!empty($_GET['level'])) {
    $query .= " AND C.courseID LIKE CONCAT(:lvl, '%')";
    $params['lvl'] = $_GET['level'];
}
if (!empty($_GET['days'])) {
    $query .= " AND CS.days = :days";
    $params['days'] = $_GET['days'];
}

$query .= " GROUP BY CS.CRN";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="portal-container">
    <?php include '../includes/portal-sidebar.php'; ?>
    <main class="portal-main">
        <div class="portal-content">
            <h1>Master Schedule</h1>
            <p style="margin-bottom: 20px;">View all course offerings for <strong><?= htmlspecialchars($currentSemester); ?></strong>.</p>

            <div class="card p-3 mb-4 border-0 shadow-sm">
                <form method="GET" class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">Semester</label>
                        <select name="semester" class="form-select" onchange="this.form.submit()">
                            <?php foreach ($semesters as $sem): ?>
                                <option value="<?= htmlspecialchars($sem); ?>" <?= ($sem === $currentSemester) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($sem); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Department</label>
                        <select name="department" class="form-select" onchange="this.form.submit()">
                            <option value="">All Departments</option>
                            <?php foreach ($departments as $d): ?>
                                <option value="<?= htmlspecialchars($d['departmentID']); ?>" <?= (($_GET['department'] ?? '') === $d['departmentID']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($d['departmentName']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Level</label>
                        <select name="level" class="form-select" onchange="this.form.submit()">
                            <option value="">All Levels</option>
                            <?php foreach ([100, 200, 300, 400] as $lvl): ?>
                                <option value="<?= $lvl; ?>" <?= (($_GET['level'] ?? '') == $lvl) ? 'selected' : ''; ?>><?= $lvl; ?> Level</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Search</label>
                        <input type="text" name="crn" class="form-control" value="<?= htmlspecialchars($_GET['crn'] ?? ''); ?>" placeholder="CRN/Code">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100 mt-4">Filter</button>
                    </div>
                </form>
            </div>

            <div class="row">
                <?php if (empty($sections)): ?>
                    <div class="col-12"><div class="card p-4">No sections found.</div></div>
                <?php else: ?>
                    <?php foreach ($sections as $s): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card p-3 shadow-sm border-0 h-100">
                                <div class="d-flex justify-content-between mb-2">
                                    <h4 class="text-primary fw-bold"><?= htmlspecialchars($s['courseID']); ?></h4>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($s['CRN']); ?></span>
                                </div>
                                <h5 class="fw-bold"><?= htmlspecialchars($s['courseName']); ?></h5>
                                <p class="small text-muted mb-1"><i class="fas fa-user"></i> <?= htmlspecialchars($s['instructorFirst'] . ' ' . $s['instructorLast']); ?></p>
                                <p class="small text-muted mb-1"><i class="fas fa-clock"></i> <?= htmlspecialchars($s['days'] . ' ' . $s['start_time']); ?></p>
                                <p class="small text-muted mb-2"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($s['buildingName']); ?></p>
                                
                                <div class="d-flex gap-3 mb-3 bg-light p-2 rounded">
                                    <div><small>Enrolled</small> <strong><?= $s['enrolledCount']; ?></strong></div>
                                    <div><small>Available</small> <strong><?= $s['capacity'] - $s['enrolledCount']; ?></strong></div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-secondary" onclick="showDetails('<?= htmlspecialchars($s['CRN']); ?>')">Details</button>
                                    <?php if ($userType === 'Faculty' && $s['facultyID'] == $userID): ?>
                                        <button class="btn btn-sm btn-primary" onclick="viewRoster('<?= htmlspecialchars($s['CRN']); ?>')">Roster</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<div id="infoModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); align-items:center; justify-content:center; z-index:1050;">
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
function showDetails(crn) {
    fetch('master-schedule.php?action=get_details&crn=' + crn)
    .then(r => r.json())
    .then(res => {
        if (res.ok) {
            const d = res.data;
            document.getElementById('modalTitle').textContent = 'Course Details - ' + d.courseID;
            document.getElementById('modalBody').innerHTML = `<p><strong>Name:</strong> ${d.courseName}</p><p><strong>Credits:</strong> ${d.credits}</p><p><strong>Description:</strong> ${d.courseDescription || 'N/A'}</p><p><strong>Prerequisites:</strong> ${d.prerequisites || 'None'}</p>`;
            showModal();
        }
    });
}
function viewRoster(crn) {
    fetch('master-schedule.php?action=roster&crn=' + crn)
    .then(r => r.json())
    .then(res => {
        if (res.ok) {
            let html = '<table class="table"><thead><tr><th>ID</th><th>Name</th><th>Grade</th></tr></thead><tbody>';
            res.students.forEach(s => html += `<tr><td>${s.studentID}</td><td>${s.firstName} ${s.lastName}</td><td>${s.grade || 'N/A'}</td></tr>`);
            html += '</tbody></table>';
            document.getElementById('modalTitle').textContent = 'Roster - ' + crn;
            document.getElementById('modalBody').innerHTML = html;
            showModal();
        }
    });
}
</script>
<?php require_once '../includes/footer.php'; ?>
