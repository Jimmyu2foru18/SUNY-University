<?php
// portal/admin/index.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../../config/database.php';
require_once '../../src/controllers/AdminController.php';
require_once '../../includes/portal_header.php';

$auth = new AdminController($pdo);
// Fetch adminType safely
$stmt = $pdo->prepare("SELECT adminType FROM Admin WHERE adminID = :id");
$stmt->execute(['id' => $_SESSION['user_id']]);
$adminType = (int)$stmt->fetchColumn();
$isSuperAdmin = ($adminType === 2);
$admLabel = $isSuperAdmin ? 'Manage' : 'View';

$currentPage = 'dashboard';
?>

<div class="portal-container">
    <?php include '../../includes/portal-sidebar.php'; ?>
    <main class="portal-main">
        <div class="portal-content">
            <h1>Admin Dashboard</h1>
            <p class="subtitle">System Administration Portal. Current Level: <strong><?= $isSuperAdmin ? 'Super Admin' : 'Staff Admin'; ?></strong></p>

            <div class="row row-cols-1 row-cols-md-3 g-4 mt-3">
                <!-- User Management -->
                <div class="col"><a href="management/students.php" class="card h-100 shadow-sm border-0 p-3 text-decoration-none">
                    <div class="card-body">
                        <h5 class="fw-bold"><?= $admLabel ?> Students</h5>
                        <p class="text-muted small">Manage student accounts</p>
                    </div>
                </a></div>
                <div class="col"><a href="management/faculty.php" class="card h-100 shadow-sm border-0 p-3 text-decoration-none">
                    <div class="card-body">
                        <h5 class="fw-bold"><?= $admLabel ?> Faculty</h5>
                        <p class="text-muted small">Manage faculty accounts</p>
                    </div>
                </a></div>
                
                <!-- Academic Management -->
                <div class="col"><a href="management/departments.php" class="card h-100 shadow-sm border-0 p-3 text-decoration-none">
                    <div class="card-body">
                        <h5 class="fw-bold"><?= $admLabel ?> Departments</h5>
                        <p class="text-muted small">Academic organization</p>
                    </div>
                </a></div>
                <div class="col"><a href="management/sections.php" class="card h-100 shadow-sm border-0 p-3 text-decoration-none">
                    <div class="card-body">
                        <h5 class="fw-bold"><?= $admLabel ?> Sections</h5>
                        <p class="text-muted small">Manage course offerings</p>
                    </div>
                </a></div>
                <div class="col"><a href="management/enrollments.php" class="card h-100 shadow-sm border-0 p-3 text-decoration-none">
                    <div class="card-body">
                        <h5 class="fw-bold"><?= $admLabel ?> Enrollments</h5>
                        <p class="text-muted small">Roster management</p>
                    </div>
                </a></div>
                <div class="col"><a href="management/programs.php" class="card h-100 shadow-sm border-0 p-3 text-decoration-none">
                    <div class="card-body">
                        <h5 class="fw-bold"><?= $admLabel ?> Programs</h5>
                        <p class="text-muted small">Manage Majors</p>
                    </div>
                </a></div>
                
                <!-- System & Records -->
                <div class="col"><a href="management/catalog.php" class="card h-100 shadow-sm border-0 p-3 text-decoration-none">
                    <div class="card-body">
                        <h5 class="fw-bold">Course Catalog</h5>
                        <p class="text-muted small">Master course list</p>
                    </div>
                </a></div>
                <div class="col"><a href="management/holds.php" class="card h-100 shadow-sm border-0 p-3 text-decoration-none">
                    <div class="card-body">
                        <h5 class="fw-bold"><?= $admLabel ?> Holds</h5>
                        <p class="text-muted small">Academic restrictions</p>
                    </div>
                </a></div>

                <?php if ($isSuperAdmin): ?>
                    <div class="col"><a href="management/admins.php" class="card h-100 shadow-sm border-0 p-3 text-decoration-none">
                        <div class="card-body">
                            <h5 class="fw-bold">Manage Admins</h5>
                            <p class="text-muted small">Staff account control</p>
                        </div>
                    </a></div>
                    <div class="col"><a href="management/account_security.php" class="card h-100 shadow-sm border-0 p-3 text-decoration-none">
                        <div class="card-body">
                            <h5 class="fw-bold">Lockouts</h5>
                            <p class="text-muted small">Unlock user accounts</p>
                        </div>
                    </a></div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php require_once '../../includes/footer.php'; ?>
