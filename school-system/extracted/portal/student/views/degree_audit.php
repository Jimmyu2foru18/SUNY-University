<?php
// portal/student/views/degree_audit.php
require_once '../../../config/database.php';
require_once '../../../src/controllers/BaseController.php';
require_once '../../../includes/portal_header.php';

$auth = new BaseController($pdo);
$auth->checkRole(['Student']);

$studentId = $_SESSION['user_id'];

// 1. Get Student Major
$stmt = $pdo->prepare("
    SELECT M.majorID, M.majorName 
    FROM StudentMajor SM 
    JOIN Major M ON SM.majorID = M.majorID 
    WHERE SM.studentID = :sid
");
$stmt->execute(['sid' => $studentId]);
$major = $stmt->fetch();

// 2. Get Requirements
$stmt = $pdo->prepare("
    SELECT C.courseID, C.courseName, MR.minimumGrade 
    FROM MajorRequirement MR 
    JOIN Course C ON MR.courseID = C.courseID 
    WHERE MR.majorID = :mid
");
$stmt->execute(['mid' => $major['majorID']]);
$requirements = $stmt->fetchAll();

// 3. Get Completed Courses
$stmt = $pdo->prepare("
    SELECT CS.courseID, E.grade 
    FROM Enrollment E 
    JOIN CourseSection CS ON E.CRN = CS.CRN 
    WHERE E.studentID = :sid AND E.grade IS NOT NULL
");
$stmt->execute(['sid' => $studentId]);
$completed = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

?>
<div class="container my-5">
    <a href="../index.php" class="btn btn-outline-secondary mb-3">&larr; Dashboard</a>
    <h2 class="fw-bold mb-2">Degree Audit</h2>
    <p class="text-muted mb-4">Program: <?= htmlspecialchars($major['majorName'] ?? 'Undeclared') ?></p>

    <div class="card shadow-sm border-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Course</th><th>Requirement</th><th>Status</th><th>Grade</th></tr>
            </thead>
            <tbody>
                <?php foreach ($requirements as $req): ?>
                    <?php 
                        $status = isset($completed[$req['courseID']]) ? 'Completed' : 'Pending';
                        $grade = $completed[$req['courseID']] ?? '-';
                        $isSuccess = ($status === 'Completed' && $grade <= $req['minimumGrade']);
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($req['courseID'] . ' - ' . $req['courseName']) ?></td>
                        <td>Min. Grade: <?= htmlspecialchars($req['minimumGrade']) ?></td>
                        <td>
                            <span class="badge <?= $status === 'Completed' ? 'bg-success' : 'bg-warning text-dark' ?>">
                                <?= $status ?>
                            </span>
                        </td>
                        <td><span class="fw-bold"><?= htmlspecialchars($grade) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once '../../../includes/footer.php'; ?>
