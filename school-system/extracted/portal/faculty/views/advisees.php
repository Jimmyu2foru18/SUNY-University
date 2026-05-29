<?php
// portal/faculty/views/advisees.php
require_once '../../../config/database.php';
require_once '../../../src/controllers/BaseController.php';
require_once '../../../includes/portal_header.php';

$auth = new BaseController($pdo);
$auth->checkRole(['Faculty']);

$stmt = $pdo->prepare("
    SELECT U.firstName, U.lastName, S.studentID, L.email
    FROM AdvisorAdvisee AA
    JOIN Student S ON AA.studentID = S.studentID
    JOIN User U ON S.studentID = U.userID
    JOIN Login L ON S.studentID = L.userID
    WHERE AA.facultyID = :facultyId
");
$stmt->execute(['facultyId' => $_SESSION['user_id']]);
$advisees = $stmt->fetchAll();
?>
<div class="container my-5">
    <a href="../index.php" class="btn btn-outline-secondary mb-3">&larr; Dashboard</a>
    <h2 class="fw-bold mb-4">My Advisees</h2>
    <div class="card border-0 shadow-sm">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Student Name</th><th>Student ID</th><th>Email</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($advisees as $student): ?>
                    <tr>
                        <td><?= htmlspecialchars($student['firstName'] . ' ' . $student['lastName']) ?></td>
                        <td><?= htmlspecialchars($student['studentID']) ?></td>
                        <td><a href="mailto:<?= htmlspecialchars($student['email']) ?>" class="text-decoration-none"><?= htmlspecialchars($student['email']) ?></a></td>
                        <td><button class="btn btn-sm btn-outline-primary">Schedule Meeting</button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once '../../../includes/footer.php'; ?>
