<?php
// portal/student/transcript.php
require_once '../../config/database.php';
require_once '../../src/controllers/BaseController.php';
require_once '../../includes/portal_header.php';

class Transcript extends BaseController {
    public function __construct($pdo) {
        parent::__construct($pdo);
        $this->checkRole(['Student']);
    }

    public function render() {
        $studentId = $_SESSION['user_id'];
        $stmt = $this->pdo->prepare("
            SELECT C.courseName, C.credits, E.grade, CS.semesterID
            FROM Enrollment E
            JOIN CourseSection CS ON E.CRN = CS.CRN
            JOIN Course C ON CS.courseID = C.courseID
            WHERE E.studentID = :studentId
        ");
        $stmt->execute(['studentId' => $studentId]);
        $courses = $stmt->fetchAll();
?>
    <div class="mt-4">
        <h2>Academic Transcript</h2>
        <table class="table">
            <tr><th>Course</th><th>Credits</th><th>Grade</th><th>Semester</th></tr>
            <?php foreach ($courses as $c): ?>
            <tr><td><?= $c['courseName'] ?></td><td><?= $c['credits'] ?></td><td><?= $c['grade'] ?></td><td><?= $c['semesterID'] ?></td></tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php
    }
}
$page = new Transcript($pdo);
$page->render();
require_once '../../includes/footer.php';
?>
