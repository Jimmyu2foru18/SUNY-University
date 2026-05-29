<?php
// portal/faculty/index.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../../config/database.php';
require_once '../../src/controllers/FacultyController.php';
require_once '../../includes/portal_header.php';

$auth = new FacultyController($pdo);
$facultyID = $_SESSION['user_id'];

// Fetch full profile
$stmt = $pdo->prepare("
    SELECT U.firstName, U.lastName, L.email, F.facultyType, 
           D.departmentName, F.office, F.specialty
    FROM Faculty F 
    JOIN User U ON F.facultyID = U.userID 
    JOIN Login L ON F.facultyID = L.userID
    LEFT JOIN FacultyDepartment FD ON F.facultyID = FD.facultyID
    LEFT JOIN Department D ON FD.departmentID = D.departmentID
    WHERE F.facultyID = :id
");
$stmt->execute(['id' => $facultyID]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

$pageTitle = "Faculty Dashboard";
?>

<div class="portal-container">
    <?php 
    $currentPage = 'dashboard';
    include '../../includes/portal-sidebar.php'; 
    ?>

    <main class="portal-main">
        <div class="portal-content">
            <h1>Faculty Dashboard</h1>
            <p class="subtitle">Welcome, Professor <?= htmlspecialchars($profile['lastName'] ?? 'N/A') ?>. Manage your courses and student interactions below.</p>

            <!-- Faculty Profile Card -->
            <div class="dashboard-card profile-overview">
                <div class="card-icon"><i class="fas fa-id-badge"></i></div>
                <div class="profile-details">
                    <h3>Teaching Profile</h3>
                    <div class="detail-grid">
                        <p><strong>Rank:</strong> <?= htmlspecialchars($profile['facultyType'] ?? 'N/A') ?></p>
                        <p><strong>Department(s):</strong> <?= htmlspecialchars($profile['departmentName'] ?? 'None') ?></p>
                        <p><strong>Office:</strong> <?= htmlspecialchars($profile['office'] ?? 'TBA') ?></p>
                        <p><strong>Specialty:</strong> <?= htmlspecialchars($profile['specialty'] ?? 'N/A') ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($profile['email'] ?? 'N/A') ?></p>
                        <a href="views/update_profile.php" class="btn btn-sm btn-outline-primary">Update Profile</a>
                    </div>
                </div>
            </div>

            <div class="dashboard-grid">
                <!-- Teaching Tools -->
                <a href="views/teaching_schedule.php" class="dashboard-card-link">
                    <div class="card-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                    <h4>My Teaching</h4>
                    <p>View sections and rosters</p>
                </a>
                <a href="views/grading.php" class="dashboard-card-link">
                    <div class="card-icon"><i class="fas fa-edit"></i></div>
                    <h4>Grading</h4>
                    <p>Enter and manage grades</p>
                </a>
                <a href="views/attendance.php" class="dashboard-card-link">
                    <div class="card-icon"><i class="fas fa-user-check"></i></div>
                    <h4>Attendance</h4>
                    <p>Track student participation</p>
                </a>
                <a href="views/teaching_schedule.php" class="dashboard-card-link">
                    <div class="card-icon"><i class="fas fa-calendar-alt"></i></div>
                    <h4>Schedule</h4>
                    <p>View teaching hours</p>
                </a>
                <a href="views/appointments.php" class="dashboard-card-link">
                    <div class="card-icon"><i class="fas fa-calendar-check"></i></div>
                    <h4>Appointments</h4>
                    <p>Manage student meetings</p>
                </a>
                <a href="views/advisees.php" class="dashboard-card-link">
                    <div class="card-icon"><i class="fas fa-user-graduate"></i></div>
                    <h4>My Advisees</h4>
                    <p>Support your students</p>
                </a>
            </div>
        </div>
    </main>
</div>

<?php require_once '../../includes/footer.php'; ?>
