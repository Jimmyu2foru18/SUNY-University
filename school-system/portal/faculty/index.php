<?php
// portal/faculty/index.php
require_once '../../config/database.php';
require_once '../../src/controllers/BaseController.php';
require_once '../../includes/portal_header.php';

class FacultyDashboard extends BaseController {
    public function __construct($pdo) {
        parent::__construct($pdo);
        $this->checkRole(['Faculty']);
    }

    public function render() {
        $facultyId = $_SESSION['user_id'];
        
        // Fetch personal info
        $stmt = $this->pdo->prepare("SELECT * FROM User WHERE userID = :id");
        $stmt->execute(['id' => $facultyId]);
        $user = $stmt->fetch();

        // Fetch Assigned Classes
        $stmt = $this->pdo->prepare("
            SELECT C.courseName, CS.CRN 
            FROM CourseSection CS 
            JOIN Course C ON CS.courseID = C.courseID 
            WHERE CS.facultyID = :facultyId
        ");
        $stmt->execute(['facultyId' => $facultyId]);
        $classes = $stmt->fetchAll();
?>
    <div class="mt-4">
        <h1>Welcome, Professor <?= htmlspecialchars($user['lastName']) ?></h1>
        
        <h3>My Information</h3>
        <p>Name: <?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) ?></p>
        
        <h3>My Assigned Classes</h3>
        <table class="table">
            <tr>
                <th>Course Name</th>
                <th>CRN</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($classes as $class): ?>
                <tr>
                    <td><?= htmlspecialchars($class['courseName']) ?></td>
                    <td><?= htmlspecialchars($class['CRN']) ?></td>
                    <td>
                        <a href="grade_entry.php?crn=<?= htmlspecialchars($class['CRN']) ?>" class="btn btn-sm btn-primary">Enter Grades</a>
                        <a href="roster.php?crn=<?= htmlspecialchars($class['CRN']) ?>" class="btn btn-sm btn-secondary">View Roster</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php
    }
}

$dashboard = new FacultyDashboard($pdo);
$dashboard->render();
require_once '../../includes/footer.php';
?>
