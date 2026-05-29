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
        // Define specific modules as requested
        $modules = [
            ['name' => 'View Students', 'table' => 'Student'],
            ['name' => 'View Faculty', 'table' => 'Faculty'],
            ['name' => 'View Departments', 'table' => 'Department'],
            ['name' => 'View Sections', 'table' => 'CourseSection'],
            ['name' => 'View Enrollments', 'table' => 'Enrollment'],
            ['name' => 'Course Catalog', 'table' => 'Course'],
            ['name' => 'Master Schedule', 'table' => 'TimeSlot'],
            ['name' => 'View Holds', 'table' => 'Hold']
        ];

        // Map tables to specific view pages
        $pageMap = [
            'Student' => 'students.php',
            'Faculty' => 'faculty.php',
            'Department' => 'departments.php',
            'CourseSection' => 'sections.php',
            'Enrollment' => 'enrollments.php',
            'Course' => 'catalog.php',
            'TimeSlot' => 'master_schedule.php',
            'Hold' => 'holds.php'
        ];
    ?>
    <div class="container my-5">
        <h1 class="fw-bold mb-2">Statistical Staff Dashboard</h1>
        <p class="text-muted mb-4">View-only Administrative Portal.</p>

        <div class="row">
            <?php foreach ($modules as $module): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0 p-3">
                        <div class="card-body">
                            <h5 class="fw-bold"><?= htmlspecialchars($module['name']) ?></h5>
                            <a href="views/<?= htmlspecialchars($pageMap[$module['table']]) ?>" class="btn btn-sm btn-outline-primary">View Data</a>
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
