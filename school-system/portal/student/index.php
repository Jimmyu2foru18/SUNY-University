<?php
// portal/student/index.php
require_once '../../config/database.php';
require_once '../../src/controllers/BaseController.php';
require_once '../../includes/portal_header.php';

class StudentDashboard extends BaseController {
    public function __construct($pdo) {
        parent::__construct($pdo);
        $this->checkRole(['Student']);
    }

    public function render() {
        $studentId = $_SESSION['user_id'];
        
        // Fetch personal info
        $stmt = $this->pdo->prepare("SELECT * FROM User WHERE userID = :id");
        $stmt->execute(['id' => $studentId]);
        $user = $stmt->fetch();

        // Fetch Courses
        $stmt = $this->pdo->prepare("
            SELECT C.courseName, CS.CRN, E.grade 
            FROM Enrollment E 
            JOIN CourseSection CS ON E.CRN = CS.CRN 
            JOIN Course C ON CS.courseID = C.courseID 
            WHERE E.studentID = :studentId
        ");
        $stmt->execute(['studentId' => $studentId]);
        $courses = $stmt->fetchAll();
?>
    <div class="mt-4">
        <h1>Welcome, <?= htmlspecialchars($user['firstName']) ?></h1>
        <a href="transcript.php" class="btn btn-info mb-3">View My Transcript</a>
        
        <h3>My Information</h3>
        <p>Name: <?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) ?></p>
        <p>Email: <?= htmlspecialchars($user['email'] ?? 'N/A') ?></p>
        
        <h3>My Courses</h3>
        <table class="table">
            <tr>
                <th>Course Name</th>
                <th>CRN</th>
                <th>Grade</th>
            </tr>
            <?php foreach ($courses as $course): ?>
                <tr>
                    <td><?= htmlspecialchars($course['courseName']) ?></td>
                    <td><?= htmlspecialchars($course['CRN']) ?></td>
                    <td><?= htmlspecialchars($course['grade']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php
    }
}

$dashboard = new StudentDashboard($pdo);
$dashboard->render();
require_once '../../includes/footer.php';
?>
