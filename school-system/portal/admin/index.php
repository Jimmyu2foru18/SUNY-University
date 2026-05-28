<?php
// portal/admin/index.php
require_once '../../config/database.php';
require_once '../../src/controllers/BaseController.php';
require_once '../../includes/portal_header.php';

class AdminDashboard extends BaseController {
    public function __construct($pdo) {
        parent::__construct($pdo);
        $this->checkRole(['Admin']);
    }

    public function render() {
?>
    <h1>Admin Dashboard</h1>
    <h3>Manage Tables</h3>
    <ul>
        <li><a href="manage_table.php?table=Admin">Admin</a></li>
        <li><a href="manage_table.php?table=AdvisorAdvisee">AdvisorAdvisee</a></li>
        <li><a href="manage_table.php?table=Attendance">Attendance</a></li>
        <li><a href="manage_table.php?table=Building">Building</a></li>
        <li><a href="manage_table.php?table=Course">Course</a></li>
        <li><a href="manage_table.php?table=CoursePrerequisite">CoursePrerequisite</a></li>
        <li><a href="manage_table.php?table=CourseSection">CourseSection</a></li>
        <li><a href="manage_table.php?table=Days">Days</a></li>
        <li><a href="manage_table.php?table=Department">Department</a></li>
        <li><a href="manage_table.php?table=Enrollment">Enrollment</a></li>
        <li><a href="manage_table.php?table=Faculty">Faculty</a></li>
        <li><a href="manage_table.php?table=FacultyDepartment">FacultyDepartment</a></li>
        <li><a href="manage_table.php?table=FacultyHistory">FacultyHistory</a></li>
        <li><a href="manage_table.php?table=FullTimeFaculty">FullTimeFaculty</a></li>
        <li><a href="manage_table.php?table=FullTimeGraduate">FullTimeGraduate</a></li>
        <li><a href="manage_table.php?table=FullTimeUndergraduate">FullTimeUndergraduate</a></li>
        <li><a href="manage_table.php?table=Graduate">Graduate</a></li>
        <li><a href="manage_table.php?table=Hold">Hold</a></li>
        <li><a href="manage_table.php?table=Lab">Lab</a></li>
        <li><a href="manage_table.php?table=Lecture">Lecture</a></li>
        <li><a href="manage_table.php?table=Login">Login</a></li>
        <li><a href="manage_table.php?table=Major">Major</a></li>
        <li><a href="manage_table.php?table=MajorRequirement">MajorRequirement</a></li>
        <li><a href="manage_table.php?table=Minor">Minor</a></li>
        <li><a href="manage_table.php?table=MinorRequirement">MinorRequirement</a></li>
        <li><a href="manage_table.php?table=Office">Office</a></li>
        <li><a href="manage_table.php?table=PartTimeFaculty">PartTimeFaculty</a></li>
        <li><a href="manage_table.php?table=PartTimeGraduate">PartTimeGraduate</a></li>
        <li><a href="manage_table.php?table=PartTimeUndergraduate">PartTimeUndergraduate</a></li>
        <li><a href="manage_table.php?table=Period">Period</a></li>
        <li><a href="manage_table.php?table=Room">Room</a></li>
        <li><a href="manage_table.php?table=Semester">Semester</a></li>
        <li><a href="manage_table.php?table=StatStaff">StatStaff</a></li>
        <li><a href="manage_table.php?table=Student">Student</a></li>
        <li><a href="manage_table.php?table=StudentCourseSectionHistory">StudentCourseSectionHistory</a></li>
        <li><a href="manage_table.php?table=StudentHold">StudentHold</a></li>
        <li><a href="manage_table.php?table=StudentMajor">StudentMajor</a></li>
        <li><a href="manage_table.php?table=StudentMinor">StudentMinor</a></li>
        <li><a href="manage_table.php?table=TimeSlot">TimeSlot</a></li>
        <li><a href="manage_table.php?table=TimeSlotDay">TimeSlotDay</a></li>
        <li><a href="manage_table.php?table=TimeSlotPeriod">TimeSlotPeriod</a></li>
        <li><a href="manage_table.php?table=Undergraduate">Undergraduate</a></li>
        <li><a href="manage_table.php?table=User">User</a></li>
    </ul>
<?php
    }
}

$dashboard = new AdminDashboard($pdo);
$dashboard->render();
require_once '../../includes/footer.php';
?>
