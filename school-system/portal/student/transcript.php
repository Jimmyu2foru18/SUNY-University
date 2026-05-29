<?php
// portal/student/transcript.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../../config/database.php';
require_once '../../src/controllers/StudentController.php';
require_once '../../includes/portal_header.php';

$auth = new StudentController($pdo);
$studentID = $_SESSION['user_id'];

// 1. Fetch Profile and Major
$stmt = $pdo->prepare("
    SELECT U.firstName, U.lastName, U.userID, M.majorName
    FROM User U
    LEFT JOIN StudentMajor SM ON U.userID = SM.studentID
    LEFT JOIN Major M ON SM.majorID = M.majorID
    WHERE U.userID = :sid
");
$stmt->execute(['sid' => $studentID]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

// 2. Fetch Transcript Data
$stmt = $pdo->prepare("
    SELECT C.courseID, C.courseName, C.credits, E.grade, CS.semesterID
    FROM Enrollment E
    JOIN CourseSection CS ON E.CRN = CS.CRN
    JOIN Course C ON CS.courseID = C.courseID
    WHERE E.studentID = :sid
    ORDER BY CS.semesterID DESC, C.courseID ASC
");
$stmt->execute(['sid' => $studentID]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate GPA and Total Credits
$gradePoints = ['A' => 4.0, 'A-' => 3.7, 'B+' => 3.3, 'B' => 3.0, 'B-' => 2.7, 'C+' => 2.3, 'C' => 2.0, 'C-' => 1.7, 'D+' => 1.3, 'D' => 1.0, 'F' => 0.0];
$totalPoints = 0;
$totalCredits = 0;
$gradedCredits = 0;

foreach ($courses as $c) {
    if (isset($gradePoints[$c['grade']])) {
        $totalPoints += ($gradePoints[$c['grade']] * $c['credits']);
        $gradedCredits += $c['credits'];
    }
    $totalCredits += $c['credits'];
}
$gpa = $gradedCredits > 0 ? $totalPoints / $gradedCredits : 0;
?>

<div class="portal-container">
    <?php include '../../includes/portal-sidebar.php'; ?>
    <main class="portal-main">
        <div class="portal-content">
            <div class="card p-4 border-0 shadow-sm mb-4">
                <h1>Official Academic Transcript</h1>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> <?= htmlspecialchars(($profile['firstName'] ?? '') . ' ' . ($profile['lastName'] ?? '')) ?></p>
                        <p><strong>Student ID:</strong> <?= htmlspecialchars($profile['userID'] ?? '') ?></p>
                        <p><strong>Major:</strong> <?= htmlspecialchars($profile['majorName'] ?? 'Not Declared') ?></p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="border p-3 d-inline-block rounded">
                            <span class="d-block text-muted small text-uppercase">Cumulative GPA</span>
                            <span class="h3 fw-bold text-primary"><?= number_format($gpa, 2) ?></span>
                            <span class="d-block text-muted small">Total Credits: <?= $totalCredits ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>Semester</th><th>Course ID</th><th>Course Name</th><th>Credits</th><th>Grade</th></tr>
                        </thead>
                        <tbody>
                            <?php if (empty($courses)): ?>
                                <tr><td colspan="5" class="text-center">No transcript history found.</td></tr>
                            <?php else: ?>
                                <?php 
                                $currentSem = '';
                                foreach ($courses as $c): 
                                    if ($c['semesterID'] !== $currentSem):
                                        $currentSem = $c['semesterID'];
                                ?>
                                    <tr class="table-active">
                                        <td colspan="5"><strong>Semester: <?= htmlspecialchars($currentSem) ?></strong></td>
                                    </tr>
                                <?php endif; ?>
                                <tr>
                                    <td></td>
                                    <td><strong><?= htmlspecialchars($c['courseID']) ?></strong></td>
                                    <td><?= htmlspecialchars($c['courseName']) ?></td>
                                    <td><?= htmlspecialchars($c['credits']) ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($c['grade'] ?? 'In Progress') ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="mt-4">
                <button class="btn btn-primary" onclick="window.print()"><i class="fas fa-print"></i> Print Transcript</button>
            </div>
        </div>
    </main>
</div>

<?php require_once '../../includes/footer.php'; ?>
