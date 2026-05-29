<?php
// portal/student/views/my_advisor.php
require_once '../../../config/database.php';
require_once '../../../src/controllers/BaseController.php';
require_once '../../../includes/portal_header.php';

$auth = new BaseController($pdo);
$auth->checkRole(['Student']);

$studentId = $_SESSION['user_id'];

// Fetch Advisor Info
$stmt = $pdo->prepare("
    SELECT U.firstName, U.lastName, L.email, F.facultyType, D.deptName, O.roomID as office
    FROM AdvisorAdvisee AA
    JOIN Faculty F ON AA.facultyID = F.facultyID
    JOIN User U ON F.facultyID = U.userID
    JOIN Login L ON F.facultyID = L.userID
    LEFT JOIN FacultyDepartment FD ON F.facultyID = FD.facultyID
    LEFT JOIN Department D ON FD.deptID = D.deptID
    LEFT JOIN Office O ON F.facultyID = O.facultyID
    WHERE AA.studentID = :sid
");
$stmt->execute(['sid' => $studentId]);
$advisor = $stmt->fetch();
?>
<div class="container my-5">
    <a href="../index.php" class="btn btn-outline-secondary mb-3">&larr; Dashboard</a>
    <h2 class="fw-bold mb-4">My Academic Advisor</h2>

    <?php if ($advisor): ?>
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="row g-0">
                <div class="col-md-4 bg-primary text-white d-flex align-items-center justify-content-center py-5">
                    <div class="text-center">
                        <div class="bg-white text-primary rounded-circle d-inline-flex align-items-center justify-content-center fw-bold mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                            <?= substr($advisor['firstName'], 0, 1) . substr($advisor['lastName'], 0, 1) ?>
                        </div>
                        <h4 class="fw-bold mb-0"><?= htmlspecialchars($advisor['firstName'] . ' ' . $advisor['lastName']) ?></h4>
                        <p class="small mb-0 opacity-75"><?= htmlspecialchars($advisor['facultyType']) ?></p>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card-body p-4 p-lg-5">
                        <h5 class="fw-bold mb-4">Contact Information</h5>
                        <div class="mb-3">
                            <span class="text-muted small text-uppercase fw-bold d-block">Department</span>
                            <span class="fw-medium"><?= htmlspecialchars($advisor['deptName'] ?? 'N/A') ?></span>
                        </div>
                        <div class="mb-3">
                            <span class="text-muted small text-uppercase fw-bold d-block">Office Location</span>
                            <span class="fw-medium"><?= htmlspecialchars($advisor['office'] ?? 'TBA') ?></span>
                        </div>
                        <div class="mb-4">
                            <span class="text-muted small text-uppercase fw-bold d-block">University Email</span>
                            <a href="mailto:<?= htmlspecialchars($advisor['email']) ?>" class="text-decoration-none fw-bold" style="color: var(--secondary-color);"><?= htmlspecialchars($advisor['email']) ?></a>
                        </div>
                        <hr class="my-4 opacity-10">
                        <div class="d-grid d-md-block">
                            <a href="appointments.php" class="btn btn-primary px-4 me-md-2">Schedule Meeting</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info" role="alert">
            <h4 class="alert-heading">No Advisor Assigned</h4>
            <p>Our records show that you currently do not have an academic advisor assigned. Please contact the Registrar's Office or your Department Chair to have an advisor assigned to your account.</p>
        </div>
    <?php endif; ?>
</div>
<?php require_once '../../../includes/footer.php'; ?>
