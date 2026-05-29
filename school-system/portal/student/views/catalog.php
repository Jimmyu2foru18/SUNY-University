<?php
// portal/student/views/catalog.php
require_once '../../../config/database.php';
require_once '../../../src/controllers/BaseController.php';
require_once '../../../includes/portal_header.php';

$auth = new BaseController($pdo);
$auth->checkRole(['Student']);

$stmt = $pdo->query("
    SELECT C.courseID, C.courseName, C.courseDescription, C.credits, D.deptName 
    FROM Course C 
    JOIN Department D ON C.departmentID = D.deptID 
    ORDER BY D.deptName, C.courseID
");
$courses = $stmt->fetchAll();
?>
<div class="container my-5">
    <a href="../index.php" class="btn btn-outline-secondary mb-3">&larr; Dashboard</a>
    <h2 class="fw-bold mb-4">University Course Catalog</h2>
    <div class="row">
        <?php foreach ($courses as $c): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="fw-bold mb-0"><?= htmlspecialchars($c['courseID']) ?></h5>
                            <span class="badge bg-primary-subtle text-primary"><?= htmlspecialchars($c['credits']) ?> Credits</span>
                        </div>
                        <h6 class="text-muted mb-3"><?= htmlspecialchars($c['courseName']) ?></h6>
                        <p class="small text-muted mb-0"><?= htmlspecialchars($c['courseDescription']) ?></p>
                        <hr class="my-3 opacity-10">
                        <span class="small fw-bold text-uppercase" style="font-size: 0.7rem; color: var(--secondary-color);"><?= htmlspecialchars($c['deptName']) ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php require_once '../../../includes/footer.php'; ?>
