<?php
// portal/statstaff/index.php
require_once '../../config/database.php';
require_once '../../src/controllers/BaseController.php';
require_once '../../includes/portal_header.php';

class StatStaffDashboard extends BaseController {
    public function __construct($pdo) {
        parent::__construct($pdo);
        $this->checkRole(['StatStaff']);
    }

    public function render() {
        // Fetch all tables dynamically from the database
        $stmt = $this->pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Map tables to specific PHP files
        $pageMap = [
            'Student' => 'students.php',
            'Faculty' => 'faculty.php',
            'Department' => 'departments.php',
            'CourseSection' => 'sections.php',
            'Enrollment' => 'enrollments.php',
            'Course' => 'catalog.php',
            'TimeSlot' => 'schedule.php',
            'Hold' => 'holds.php'
        ];
    ?>
    <div class="container my-5">
        <h1 class="fw-bold mb-2">Statistical Staff Dashboard</h1>
        <p class="text-muted mb-4">View-only Administrative Portal.</p>

        <div class="row">
            <?php foreach ($tables as $tableName): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0 p-3">
                        <div class="card-body">
                            <h5 class="fw-bold"><?= htmlspecialchars($tableName) ?></h5>
                            <?php if (isset($pageMap[$tableName])): ?>
                                <a href="views/<?= $pageMap[$tableName] ?>" class="btn btn-sm btn-outline-primary">View Data</a>
                            <?php else: ?>
                                <span class="text-muted small">No specific view page.</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    }
}

$dashboard = new StatStaffDashboard($pdo);
$dashboard->render();
require_once '../../includes/footer.php';
?>
