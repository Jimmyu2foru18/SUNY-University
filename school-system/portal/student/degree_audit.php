<?php
// portal/student/degree_audit.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../../config/database.php';
require_once '../../src/controllers/StudentController.php';
require_once '../../includes/portal_header.php';

$auth = new StudentController($pdo);
$studentID = $_SESSION['user_id'];

// 1. Fetch Profile and Major
$stmt = $pdo->prepare("
    SELECT U.firstName, U.lastName, M.majorID, M.majorName
    FROM User U
    JOIN Student S ON U.userID = S.studentID
    LEFT JOIN StudentMajor SM ON S.studentID = SM.studentID
    LEFT JOIN Major M ON SM.majorID = M.majorID
    WHERE U.userID = :sid
");
$stmt->execute(['sid' => $studentID]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

// 2. Fetch Requirements
$stmt = $pdo->prepare("
    SELECT MR.courseID, C.courseName, C.credits, MR.minimumGrade 
    FROM MajorRequirement MR 
    JOIN Course C ON MR.courseID = C.courseID 
    WHERE MR.majorID = :mid
");
$stmt->execute(['mid' => $profile['majorID']]);
$requirements = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Fetch Completed Courses & Calculate GPA
$stmt = $pdo->prepare("
    SELECT CS.courseID, E.grade
    FROM Enrollment E 
    JOIN CourseSection CS ON E.CRN = CS.CRN 
    WHERE E.studentID = :sid AND E.grade IS NOT NULL AND E.grade != 'I'
");
$stmt->execute(['sid' => $studentID]);
$completedData = $stmt->fetchAll(PDO::FETCH_ASSOC);

$completedCourses = [];
$gradeScale = ['A' => 4.0, 'A-' => 3.7, 'B+' => 3.3, 'B' => 3.0, 'B-' => 2.7, 'C+' => 2.3, 'C' => 2.0, 'C-' => 1.7, 'D+' => 1.3, 'D' => 1.0, 'F' => 0.0];
$totalPoints = 0;
$gradedCredits = 0;

// Fetch all course credits to calculate GPA correctly
$stmt = $pdo->query("SELECT courseID, credits FROM Course");
$courseCredits = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

foreach ($completedData as $row) {
    $completedCourses[$row['courseID']] = $row['grade'];
    $credits = $courseCredits[$row['courseID']] ?? 0;
    if (isset($gradeScale[$row['grade']])) {
        $totalPoints += ($gradeScale[$row['grade']] * $credits);
        $gradedCredits += $credits;
    }
}
$gpa = $gradedCredits > 0 ? $totalPoints / $gradedCredits : 0;

// Organize Requirements
$completedList = [];
$remainingList = [];

foreach ($requirements as $req) {
    $grade = $completedCourses[$req['courseID']] ?? null;
    $isSatisfied = ($grade && isset($gradeScale[$grade]) && $gradeScale[$grade] >= ($gradeScale[$req['minimumGrade']] ?? 1));
    
    if ($isSatisfied) {
        $completedList[] = ['courseID' => $req['courseID'], 'courseName' => $req['courseName'], 'grade' => $grade];
    } else {
        $remainingList[] = ['courseID' => $req['courseID'], 'courseName' => $req['courseName'], 'minGrade' => $req['minimumGrade']];
    }
}

$progress = count($requirements) > 0 ? (count($completedList) / count($requirements)) * 100 : 0;
?>

<div class="portal-container">
    <?php 
    $currentPage = 'degree-audit';
    include '../../includes/portal-sidebar.php'; 
    ?>
    <main class="portal-main">
        <div class="portal-content">
            <h1 class="fw-bold mb-4">Degree Progress Audit</h1>
            <p class="subtitle">Program: <strong><?= htmlspecialchars($profile['majorName'] ?? 'Undeclared') ?></strong></p>

            <div class="row mb-5">
                <div class="col-md-6">
                    <div class="card p-4 shadow-sm border-0 h-100">
                        <h4 class="fw-bold">Overall Progress</h4>
                        <div class="progress mb-2" style="height: 20px;">
                            <div class="progress-bar bg-success" style="width: <?= $progress ?>%"></div>
                        </div>
                        <span class="text-muted"><?= round($progress, 1) ?>% Requirements Met</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card p-4 shadow-sm border-0 h-100">
                        <h4 class="fw-bold">Major GPA</h4>
                        <span class="h2 fw-bold text-primary"><?= number_format($gpa, 2) ?></span>
                    </div>
                </div>
            </div>

            <h3 class="fw-bold mb-3 text-success"><i class="fas fa-check-circle"></i> Completed Requirements</h3>
            <div class="card border-0 shadow-sm mb-5">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Course ID</th><th>Course Name</th><th>Grade</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php foreach ($completedList as $c): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($c['courseID']) ?></strong></td>
                                <td><?= htmlspecialchars($c['courseName']) ?></td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($c['grade']) ?></span></td>
                                <td><span class="badge bg-success">Satisfied</span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <h3 class="fw-bold mb-3 text-warning"><i class="fas fa-exclamation-circle"></i> Remaining Requirements</h3>
            <div class="card border-0 shadow-sm">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Course ID</th><th>Course Name</th><th>Min. Grade</th></tr></thead>
                    <tbody>
                        <?php foreach ($remainingList as $r): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($r['courseID']) ?></strong></td>
                                <td><?= htmlspecialchars($r['courseName']) ?></td>
                                <td><?= htmlspecialchars($r['minGrade']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<?php require_once '../../includes/footer.php'; ?>
