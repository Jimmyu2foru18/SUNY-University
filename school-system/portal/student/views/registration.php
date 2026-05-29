<?php
// portal/student/views/registration.php
require_once '../../../config/database.php';
require_once '../../../src/controllers/BaseController.php';
require_once '../../../includes/portal_header.php';

$auth = new BaseController($pdo);
$auth->checkRole(['Student']);

$studentId = $_SESSION['user_id'];
$message = '';
$error = '';

// Handle Add/Drop
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $crn = $_POST['crn'];

        if ($_POST['action'] === 'add') {
            try {
                // 1. Get courseID for this CRN
                $stmt = $pdo->prepare("SELECT courseID FROM CourseSection WHERE CRN = :crn");
                $stmt->execute(['crn' => $crn]);
                $courseID = $stmt->fetchColumn();

                // 2. Check Prerequisites
                $stmt = $pdo->prepare("SELECT prerequisiteID, gradeRequired FROM CoursePrerequisite WHERE courseID = :courseID");
                $stmt->execute(['courseID' => $courseID]);
                $prereqs = $stmt->fetchAll();

                foreach ($prereqs as $prereq) {
                    $stmt = $pdo->prepare("SELECT grade FROM Enrollment WHERE studentID = :sid AND CRN LIKE :pid");
                    // Simplification: search for the course ID in the CRN string
                    $stmt->execute(['sid' => $studentId, 'pid' => $prereq['prerequisiteID'] . '%']);
                    $history = $stmt->fetch();

                    if (!$history || $history['grade'] > $prereq['gradeRequired']) {
                        throw new Exception("Prerequisite not met: " . $prereq['prerequisiteID'] . " (Required: " . $prereq['gradeRequired'] . ")");
                    }
                }

                // 3. Add Enrollment
                $stmt = $pdo->prepare("INSERT INTO Enrollment (studentID, CRN, grade) VALUES (:sid, :crn, NULL)");
                $stmt->execute(['sid' => $studentId, 'crn' => $crn]);
                $message = "Successfully registered for $crn!";
            } catch (Exception $e) {
                $error = "Registration failed: " . $e->getMessage();
            }
        } elseif ($_POST['action'] === 'drop') {
            $stmt = $pdo->prepare("DELETE FROM Enrollment WHERE studentID = :sid AND CRN = :crn");
            $stmt->execute(['sid' => $studentId, 'crn' => $crn]);
            $message = "Successfully dropped $crn.";
        }
    }
}

// Fetch Current Enrollments
$stmt = $pdo->prepare("
    SELECT E.CRN, C.courseName 
    FROM Enrollment E 
    JOIN CourseSection CS ON E.CRN = CS.CRN 
    JOIN Course C ON CS.courseID = C.courseID 
    WHERE E.studentID = :sid
");
$stmt->execute(['sid' => $studentId]);
$current = $stmt->fetchAll();

// Fetch Available Sections (simplified)
$stmt = $pdo->query("
    SELECT CS.CRN, C.courseID, C.courseName, U.firstName, U.lastName 
    FROM CourseSection CS 
    JOIN Course C ON CS.courseID = C.courseID 
    JOIN User U ON CS.facultyID = U.userID 
    LIMIT 20
");
$available = $stmt->fetchAll();
?>
<div class="container my-5">
    <a href="../index.php" class="btn btn-outline-secondary mb-3">&larr; Dashboard</a>
    <h2 class="fw-bold mb-4">Course Registration</h2>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-6 mb-5">
            <h4 class="fw-bold mb-3">Current Schedule</h4>
            <ul class="list-group shadow-sm">
                <?php foreach ($current as $c): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold"><?= htmlspecialchars($c['CRN']) ?></span><br>
                            <small class="text-muted"><?= htmlspecialchars($c['courseName']) ?></small>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="crn" value="<?= $c['CRN'] ?>">
                            <button type="submit" name="action" value="drop" class="btn btn-sm btn-outline-danger">Drop</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="col-lg-6">
            <h4 class="fw-bold mb-3">Available Classes</h4>
            <div class="card border-0 shadow-sm">
                <div class="list-group list-group-flush">
                    <?php foreach ($available as $a): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold"><?= htmlspecialchars($a['CRN']) ?></span> - <?= htmlspecialchars($a['courseName']) ?><br>
                                <small class="text-muted">Instructor: <?= htmlspecialchars($a['firstName'] . ' ' . $a['lastName']) ?></small>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="crn" value="<?= $a['CRN'] ?>">
                                <button type="submit" name="action" value="add" class="btn btn-sm btn-primary">Add</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once '../../../includes/footer.php'; ?>
