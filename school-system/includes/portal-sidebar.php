<?php
// includes/portal-sidebar.php
$role = $_SESSION['user_type'] ?? 'student';
$currentPage = $currentPage ?? '';
?>
<aside class="portal-sidebar">
    <nav class="sidebar-nav">
        <a href="/portal/<?= strtolower($role) ?>/index.php" class="sidebar-link <?= ($currentPage === 'dashboard') ? 'active' : '' ?>">
            <i class="fas fa-home"></i> Dashboard
        </a>
        
        <?php if ($role === 'Admin' || $role === 'StatStaff'): ?>
            <div class="sidebar-section-title">Management</div>
            <a href="/portal/admin/management/students.php" class="sidebar-link <?= ($currentPage === 'students') ? 'active' : '' ?>">Students</a>
            <a href="/portal/admin/management/approve_major_changes.php" class="sidebar-link <?= ($currentPage === 'approvals') ? 'active' : '' ?>">Approve Major Changes</a>
            <a href="/portal/admin/management/faculty.php" class="sidebar-link <?= ($currentPage === 'faculty') ? 'active' : '' ?>">Faculty</a>
            <a href="/portal/admin/management/departments.php" class="sidebar-link <?= ($currentPage === 'departments') ? 'active' : '' ?>">Departments</a>
            <a href="/portal/admin/management/catalog.php" class="sidebar-link <?= ($currentPage === 'catalog') ? 'active' : '' ?>">Courses</a>
            <a href="/portal/admin/management/programs.php" class="sidebar-link <?= ($currentPage === 'programs') ? 'active' : '' ?>">Programs</a>
            <a href="/portal/admin/management/holds.php" class="sidebar-link <?= ($currentPage === 'holds') ? 'active' : '' ?>">Holds</a>
        <?php endif; ?>

        <?php if ($role === 'Student'): ?>
            <div class="sidebar-section-title">Academic</div>
            <a href="/portal/student/registration.php" class="sidebar-link <?= ($currentPage === 'registration') ? 'active' : '' ?>">Registration</a>
            <a href="/portal/student/transcript.php" class="sidebar-link <?= ($currentPage === 'transcript') ? 'active' : '' ?>">Transcript</a>
            <a href="/portal/student/degree_audit.php" class="sidebar-link <?= ($currentPage === 'degree-audit') ? 'active' : '' ?>">Degree Audit</a>
            <a href="/portal/student/request_major_change.php" class="sidebar-link <?= ($currentPage === 'major-request') ? 'active' : '' ?>">Change Major</a>
            <a href="/portal/student/advising.php" class="sidebar-link <?= ($currentPage === 'advising') ? 'active' : '' ?>">Academic Advising</a>
        <?php endif; ?>

        <?php if ($role === 'Faculty'): ?>
            <div class="sidebar-section-title">Teaching</div>
            <a href="/portal/faculty/views/teaching_schedule.php" class="sidebar-link <?= ($currentPage == 'schedule') ? 'active' : '' ?>">Schedule</a>
            <a href="/portal/faculty/views/grading.php" class="sidebar-link <?= ($currentPage == 'grading') ? 'active' : '' ?>">Grading</a>
            <a href="/portal/faculty/views/attendance.php" class="sidebar-link <?= ($currentPage == 'attendance') ? 'active' : '' ?>">Attendance</a>
            <a href="/portal/faculty/views/appointments.php" class="sidebar-link <?= ($currentPage == 'appointments') ? 'active' : '' ?>">Appointments</a>
            <a href="/portal/faculty/views/advisees.php" class="sidebar-link <?= ($currentPage == 'advisees') ? 'active' : '' ?>">Advisees</a>
        <?php endif; ?>
        
        <div class="sidebar-section-title">Account</div>
        <a href="/portal/profile.php" class="sidebar-link <?= ($currentPage === 'profile') ? 'active' : '' ?>">My Profile</a>
        <a href="/public/logout.php" class="sidebar-link">Logout</a>
    </nav>
</aside>
