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
        
        // Fetch full profile (User, Login, Faculty, Department, Office)
        $stmt = $this->pdo->prepare("
            SELECT U.firstName, U.lastName, L.email, F.facultyType, 
                   D.deptName, O.roomID as officeRoom
            FROM Faculty F 
            JOIN User U ON F.facultyID = U.userID 
            JOIN Login L ON F.facultyID = L.userID
            LEFT JOIN FacultyDepartment FD ON F.facultyID = FD.facultyID
            LEFT JOIN Department D ON FD.deptID = D.deptID
            LEFT JOIN Office O ON F.facultyID = O.facultyID
            WHERE F.facultyID = :id
        ");
        $stmt->execute(['id' => $facultyId]);
        $profile = $stmt->fetch();

        // Fetch My Teaching
        $stmt = $this->pdo->prepare("
            SELECT C.courseName, CS.CRN, R.roomName
            FROM CourseSection CS 
            JOIN Course C ON CS.courseID = C.courseID
            JOIN Room R ON CS.roomID = R.roomID
            WHERE CS.facultyID = :facultyId
        ");
        $stmt->execute(['facultyId' => $facultyId]);
        $classes = $stmt->fetchAll();

        // Fetch Advisees
        $stmt = $this->pdo->prepare("
            SELECT U.firstName, U.lastName, S.studentID
            FROM AdvisorAdvisee AA
            JOIN Student S ON AA.studentID = S.studentID
            JOIN User U ON S.studentID = U.userID
            WHERE AA.facultyID = :facultyId
        ");
        $stmt->execute(['facultyId' => $facultyId]);
        $advisees = $stmt->fetchAll();
?>
    <div class="container my-5">
        <h1 class="fw-bold mb-2">Faculty Dashboard</h1>
        <p class="text-muted mb-4">Welcome, Professor <?= htmlspecialchars($profile['lastName']) ?>. Manage your courses and student interactions below.</p>
        
        <div class="card p-4 mb-5 shadow-sm border-0">
            <h3 class="fw-bold mb-3">Teaching Profile</h3>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Name:</strong> <?= htmlspecialchars($profile['firstName'] . ' ' . $profile['lastName']) ?></p>
                    <p><strong>Rank:</strong> <?= htmlspecialchars($profile['facultyType']) ?></p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="views/update_profile.php" class="btn btn-outline-primary">Update Profile</a>
                </div>
            </div>
        </div>

        <h3 class="fw-bold mb-3">Teaching Actions</h3>
        <div class="d-flex gap-2 mb-5">
            <a href="views/teaching_schedule.php" class="btn btn-primary">My Schedule</a>
            <a href="views/appointments.php" class="btn btn-outline-primary">Appointments</a>
            <a href="views/advisees.php" class="btn btn-outline-primary">My Advisees</a>
        </div>
<?php
    }
}

$dashboard = new FacultyDashboard($pdo);
$dashboard->render();
require_once '../../includes/footer.php';
?>
