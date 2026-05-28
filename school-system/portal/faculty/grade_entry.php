<?php
// portal/faculty/grade_entry.php
require_once '../../config/database.php';
require_once '../../src/controllers/BaseController.php';
require_once '../../includes/portal_header.php';

class GradeEntry extends BaseController {
    public function __construct($pdo) {
        parent::__construct($pdo);
        $this->checkRole(['Faculty']);
    }

    public function render() {
        $facultyId = $_SESSION['user_id'];

        // Handle Grade Submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_grades'])) {
            foreach ($_POST['grades'] as $studentId => $grade) {
                $stmt = $this->pdo->prepare("UPDATE Enrollment SET grade = :grade WHERE studentID = :studentId AND CRN = :crn");
                $stmt->execute(['grade' => $grade, 'studentId' => $studentId, 'crn' => $_POST['crn']]);
            }
            $message = "Grades updated successfully.";
        }

        // List sections
        if (!isset($_GET['crn'])) {
            $stmt = $this->pdo->prepare("SELECT CS.CRN, C.courseName FROM CourseSection CS JOIN Course C ON CS.courseID = C.courseID WHERE CS.facultyID = :id");
            $stmt->execute(['id' => $facultyId]);
            $sections = $stmt->fetchAll();
?>
            <div class="mt-4">
                <h3>Select a Section</h3>
                <ul class="list-group">
                    <?php foreach ($sections as $s): ?>
                        <li class="list-group-item"><a href="grade_entry.php?crn=<?= htmlspecialchars($s['CRN']) ?>"><?= htmlspecialchars($s['courseName']) ?> (<?= htmlspecialchars($s['CRN']) ?>)</a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
<?php
        } else {
            // List students in section
            $crn = $_GET['crn'];
            $stmt = $this->pdo->prepare("
                SELECT U.firstName, U.lastName, E.studentID, E.grade 
                FROM Enrollment E 
                JOIN Student S ON E.studentID = S.studentID 
                JOIN User U ON S.studentID = U.userID 
                WHERE E.CRN = :crn
            ");
            $stmt->execute(['crn' => $crn]);
            $students = $stmt->fetchAll();
?>
            <div class="mt-4">
                <a href="grade_entry.php" class="btn btn-secondary mb-3">Back to Sections</a>
                <h3>Grading for CRN: <?= htmlspecialchars($crn) ?></h3>
                <?php if (isset($message)) echo "<div class='alert alert-success'>$message</div>"; ?>
                <form method="POST">
                    <input type="hidden" name="crn" value="<?= htmlspecialchars($crn) ?>">
                    <table class="table">
                        <tr><th>Student</th><th>Grade</th></tr>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?= htmlspecialchars($student['firstName'] . ' ' . $student['lastName']) ?></td>
                                <td><input type="text" name="grades[<?= $student['studentID'] ?>]" class="form-control" value="<?= htmlspecialchars($student['grade']) ?>"></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                    <button type="submit" name="submit_grades" class="btn btn-primary">Submit Grades</button>
                </form>
            </div>
<?php
        }
    }
}

$page = new GradeEntry($pdo);
$page->render();
require_once '../../includes/footer.php';
?>
