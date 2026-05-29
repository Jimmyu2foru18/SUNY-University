<?php
// portal/faculty/roster.php
require_once '../../config/database.php';
require_once '../../src/controllers/BaseController.php';
require_once '../../includes/portal_header.php';

class Roster extends BaseController {
    public function __construct($pdo) {
        parent::__construct($pdo);
        $this->checkRole(['Faculty']);
    }

    public function render() {
        if (!isset($_GET['crn'])) {
            echo "No CRN specified."; return;
        }
        $crn = $_GET['crn'];
        
        // Ensure faculty owns this section
        $stmt = $this->pdo->prepare("SELECT * FROM CourseSection WHERE CRN = :crn AND facultyID = :fId");
        $stmt->execute(['crn' => $crn, 'fId' => $_SESSION['user_id']]);
        if (!$stmt->fetch()) { die("Access Denied."); }

        $stmt = $this->pdo->prepare("
            SELECT U.firstName, U.lastName, U.email
            FROM Enrollment E
            JOIN Student S ON E.studentID = S.studentID
            JOIN User U ON S.studentID = U.userID
            WHERE E.CRN = :crn
        ");
        $stmt->execute(['crn' => $crn]);
        $students = $stmt->fetchAll();
?>
    <div class="mt-4">
        <h2>Roster for CRN: <?= htmlspecialchars($crn) ?></h2>
        <table class="table">
            <tr><th>Name</th><th>Email</th></tr>
            <?php foreach ($students as $s): ?>
            <tr><td><?= htmlspecialchars($s['firstName'] . ' ' . $s['lastName']) ?></td><td><?= htmlspecialchars($s['email']) ?></td></tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php
    }
}
$page = new Roster($pdo);
$page->render();
require_once '../../includes/footer.php';
?>
