<?php
// portal/faculty/views/grading.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../../../config/database.php';
require_once '../../../src/controllers/FacultyController.php';
require_once '../../../includes/portal_header.php';

$auth = new FacultyController($pdo);
$facultyID = $_SESSION['user_id'];

// Handle AJAX actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    try {
        if ($_POST['action'] === 'update_grade') {
            $stmt = $pdo->prepare("UPDATE Enrollment SET grade = :grade WHERE studentID = :sid AND CRN = :crn");
            $result = $stmt->execute(['grade' => $_POST['grade'], 'sid' => $_POST['studentID'], 'crn' => $_POST['crn']]);
            echo json_encode(['ok' => $result, 'message' => $result ? 'Grade updated successfully.' : 'Error updating grade.']);
        } elseif ($_POST['action'] === 'bulk_grade') {
            $stmt = $pdo->prepare("UPDATE Enrollment SET grade = :grade WHERE studentID = :sid AND CRN = :crn");
            $pdo->beginTransaction();
            foreach ($_POST['grades'] as $sid => $grade) {
                $stmt->execute(['grade' => $grade, 'sid' => $sid, 'crn' => $_POST['crn']]);
            }
            $pdo->commit();
            echo json_encode(['ok' => true, 'message' => 'Bulk grades updated successfully.']);
        }
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        echo json_encode(['ok' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// Fetch sections for filter - only include sections with enrollments
$stmt = $pdo->prepare("
    SELECT DISTINCT CS.CRN, C.courseID, C.courseName, CS.sectionNumber 
    FROM CourseSection CS 
    JOIN Course C ON CS.courseID = C.courseID
    JOIN Enrollment E ON CS.CRN = E.CRN
    WHERE CS.facultyID = :fid
");
$stmt->execute(['fid' => $facultyID]);
$sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

$selectedCRN = $_GET['crn'] ?? ($sections[0]['CRN'] ?? '');
$roster = [];
if ($selectedCRN) {
    $stmt = $pdo->prepare("
        SELECT U.firstName, U.lastName, S.studentID, L.email, E.grade 
        FROM Enrollment E
        JOIN Student S ON E.studentID = S.studentID
        JOIN User U ON S.studentID = U.userID
        JOIN Login L ON S.studentID = L.userID
        WHERE E.CRN = :crn
    ");
    $stmt->execute(['crn' => $selectedCRN]);
    $roster = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="portal-container">
    <?php include '../../../includes/portal-sidebar.php'; ?>
    <main class="portal-main">
        <div class="portal-content">
            <h1>Student Grading</h1>
            <p class="subtitle">Select a course section below to manage student grades.</p>

            <div class="card p-3 mb-4 border-0 shadow-sm">
                <form method="GET" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Course Section</label>
                        <select name="crn" class="form-select" onchange="this.form.submit()">
                            <option value="">Select a section...</option>
                            <?php foreach ($sections as $sec): ?>
                                <option value="<?= htmlspecialchars($sec['CRN']) ?>" <?= ($sec['CRN'] === $selectedCRN) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($sec['CRN'] . ' - ' . $sec['courseID'] . ' (Sec: ' . $sec['sectionNumber'] . ')') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>

            <?php if ($selectedCRN): ?>
                <div class="card border-0 shadow-sm">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr><th>Student ID</th><th>Name</th><th>Email</th><th>Grade</th><th>Action</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($roster as $student): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($student['studentID']) ?></td>
                                        <td><?= htmlspecialchars($student['firstName'] . ' ' . $student['lastName']) ?></td>
                                        <td><?= htmlspecialchars($student['email']) ?></td>
                                        <td>
                                            <select class="form-select form-select-sm grade-select" id="grade-<?= htmlspecialchars($student['studentID']) ?>">
                                                <option value="">Select Grade</option>
                                                <?php foreach (['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D+', 'D', 'F', 'P', 'S', 'I', 'W'] as $g): ?>
                                                    <option value="<?= $g ?>" <?= ($g === $student['grade']) ? 'selected' : '' ?>><?= $g ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" onclick="saveGrade('<?= htmlspecialchars($student['studentID']) ?>', '<?= htmlspecialchars($selectedCRN) ?>')">Save</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info">Please select a section to view the roster.</div>
            <?php endif; ?>
        </div>
    </main>
</div>

<script>
function saveGrade(studentID, crn) {
    const grade = document.getElementById('grade-' + studentID).value;
    fetch('grading.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ action: 'update_grade', studentID: studentID, crn: crn, grade: grade })
    })
    .then(r => r.json())
    .then(res => { alert(res.message); if (res.ok) location.reload(); });
}
</script>
<?php require_once '../../../includes/footer.php'; ?>
