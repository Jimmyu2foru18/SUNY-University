<?php
// portal/faculty/views/teaching_profile.php
require_once '../../../config/database.php';
require_once '../../../src/controllers/BaseController.php';
require_once '../../../includes/portal_header.php';

$auth = new BaseController($pdo);
$auth->checkRole(['Faculty']);

$facultyId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT U.firstName, U.lastName, F.facultyType, D.deptName, O.roomID as office, L.email 
                       FROM Faculty F 
                       JOIN User U ON F.facultyID = U.userID 
                       JOIN Login L ON F.facultyID = L.userID
                       LEFT JOIN FacultyDepartment FD ON F.facultyID = FD.facultyID
                       LEFT JOIN Department D ON FD.deptID = D.deptID
                       LEFT JOIN Office O ON F.facultyID = O.facultyID
                       WHERE F.facultyID = :id");
$stmt->execute(['id' => $facultyId]);
$profile = $stmt->fetch();
?>
<div class="container my-5">
    <a href="../index.php" class="btn btn-outline-secondary mb-3">&larr; Dashboard</a>
    <h2 class="fw-bold mb-4">Teaching Profile</h2>
    <div class="card p-4 shadow-sm border-0">
        <p><strong>Name:</strong> <?= htmlspecialchars($profile['firstName'] . ' ' . $profile['lastName']) ?></p>
        <p><strong>Rank:</strong> <?= htmlspecialchars($profile['facultyType']) ?></p>
        <p><strong>Department:</strong> <?= htmlspecialchars($profile['deptName'] ?? 'N/A') ?></p>
        <p><strong>Office:</strong> <?= htmlspecialchars($profile['office'] ?? 'N/A') ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($profile['email']) ?></p>
    </div>
</div>
<?php require_once '../../../includes/footer.php'; ?>
