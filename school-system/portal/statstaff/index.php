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
        $staffId = $_SESSION['user_id'];
        
        $stmt = $this->pdo->prepare("SELECT * FROM User WHERE userID = :id");
        $stmt->execute(['id' => $staffId]);
        $user = $stmt->fetch();
?>
    <div class="mt-4">
        <h1>Welcome, <?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) ?> (StatStaff)</h1>
        
        <h3>Academic Reports & Data</h3>
        <ul class="list-group">
            <li class="list-group-item"><a href="../admin/manage_table.php?table=Enrollment">View Enrollment Statistics</a></li>
            <li class="list-group-item"><a href="../admin/manage_table.php?table=StudentCourseSectionHistory">View Course History Reports</a></li>
            <li class="list-group-item"><a href="../admin/manage_table.php?table=Department">View Department Data</a></li>
            <li class="list-group-item"><a href="../admin/manage_table.php?table=FacultyHistory">View Faculty Workload History</a></li>
            <li class="list-group-item"><a href="../admin/manage_table.php?table=Course">View Course Catalog Data</a></li>
        </ul>
    </div>
<?php
    }
}

$dashboard = new StatStaffDashboard($pdo);
$dashboard->render();
require_once '../../includes/footer.php';
?>
