<?php
// portal/student/views/academic_profile.php
require_once '../../../config/database.php';
require_once '../../../src/controllers/BaseController.php';
require_once '../../../includes/portal_header.php';

$auth = new BaseController($pdo);
$auth->checkRole(['Student']);

$studentId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT U.firstName, U.lastName, S.studentType, M.majorName, L.email
                       FROM Student S
                       JOIN User U ON S.studentID = U.userID
                       JOIN Login L ON U.userID = L.userID
                       LEFT JOIN StudentMajor SM ON S.studentID = SM.studentID
                       LEFT JOIN Major M ON SM.majorID = M.majorID
                       WHERE S.studentID = :id");
$stmt->execute(['id' => $studentId]);
$profile = $stmt->fetch();
?>
<div class="container my-5">
    <a href="../index.php" class="btn btn-outline-secondary mb-3">&larr; Dashboard</a>
    <h2 class="fw-bold mb-4">Academic Profile</h2>
    <div class="card p-4 shadow-sm border-0">
        <p><strong>Name:</strong> <?= htmlspecialchars($profile['firstName'] . ' ' . $profile['lastName']) ?></p>
        <p><strong>ID:</strong> <?= htmlspecialchars($studentId) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($profile['studentType']) ?></p>
        <p><strong>Major:</strong> <?= htmlspecialchars($profile['majorName'] ?? 'Undeclared') ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($profile['email']) ?></p>
    </div>
</div>
<?php require_once '../../../includes/footer.php'; ?>
