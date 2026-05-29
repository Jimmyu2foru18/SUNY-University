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
        
        // Fetch personal info, status, and major
        $stmt = $this->pdo->prepare("
            SELECT U.*, L.email, S.studentType, M.majorName
            FROM User U 
            JOIN Login L ON U.userID = L.userID 
            JOIN Student S ON U.userID = S.studentID
            LEFT JOIN StudentMajor SM ON S.studentID = SM.studentID
            LEFT JOIN Major M ON SM.majorID = M.majorID
            WHERE U.userID = :id
        ");
        $stmt->execute(['id' => $studentId]);
        $user = $stmt->fetch();

        // Fetch Schedule (real data from Enrollment -> CourseSection -> Course/Faculty)
        $stmt = $this->pdo->prepare("
            SELECT C.courseID, C.courseName, CS.CRN, U.firstName as instrFirst, U.lastName as instrLast, R.roomName, E.grade 
            FROM Enrollment E 
            JOIN CourseSection CS ON E.CRN = CS.CRN 
            JOIN Course C ON CS.courseID = C.courseID
            JOIN Faculty F ON CS.facultyID = F.facultyID
            JOIN User U ON F.facultyID = U.userID
            JOIN Room R ON CS.roomID = R.roomID
            WHERE E.studentID = :studentId
        ");
        $stmt->execute(['studentId' => $studentId]);
        $schedule = $stmt->fetchAll();
?>
    <div class="container my-5">
        <h1 class="fw-bold mb-2">Student Dashboard</h1>
        <p class="text-muted mb-4">Welcome back, <?= htmlspecialchars($user['firstName']) ?>! Manage your academic journey below.</p>
        
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card p-4 shadow-sm border-0 h-100">
                    <h5 class="fw-bold">Academic Profile</h5>
                    <p class="text-muted small">Update your personal and contact info.</p>
                    <a href="views/update_profile.php" class="btn btn-sm btn-primary">Update Profile</a>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card p-4 shadow-sm border-0 h-100">
                    <h5 class="fw-bold">My Schedule</h5>
                    <p class="text-muted small">View your 2026SP course schedule.</p>
                    <a href="views/my_schedule.php" class="btn btn-sm btn-primary">View Schedule</a>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card p-4 shadow-sm border-0 h-100">
                    <h5 class="fw-bold">Transcript</h5>
                    <p class="text-muted small">View your grades and history.</p>
                    <a href="views/transcript.php" class="btn btn-sm btn-primary">View Transcript</a>
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <a href="views/catalog.php" class="btn btn-outline-primary">Course Catalog</a>
            <a href="views/registration.php" class="btn btn-outline-primary">Registration</a>
            <a href="views/degree_audit.php" class="btn btn-outline-primary">Degree Audit</a>
            <a href="views/appointments.php" class="btn btn-outline-primary">My Advisor</a>
        </div>
    </div>
<?php
    }
}

$dashboard = new StudentDashboard($pdo);
$dashboard->render();
require_once '../../includes/footer.php';
?>
