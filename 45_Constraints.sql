--
-- Constraints for dumped tables
--

--
-- Constraints for table `Admin`
--
ALTER TABLE `Admin`
  ADD CONSTRAINT `fk_admin_user` FOREIGN KEY (`adminID`) REFERENCES `User` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `AdvisorAdvisee`
--
ALTER TABLE `AdvisorAdvisee`
  ADD CONSTRAINT `fk_advisoradvisee_faculty` FOREIGN KEY (`facultyID`) REFERENCES `Faculty` (`facultyID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_advisoradvisee_student` FOREIGN KEY (`studentID`) REFERENCES `Student` (`studentID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Attendance`
--
ALTER TABLE `Attendance`
  ADD CONSTRAINT `fk_attendance_section` FOREIGN KEY (`CRN`) REFERENCES `CourseSection` (`CRN`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_attendance_student` FOREIGN KEY (`studentID`) REFERENCES `Student` (`studentID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_attendance_timeslot` FOREIGN KEY (`timeSlotID`) REFERENCES `TimeSlot` (`timeSlotID`) ON UPDATE CASCADE;

--
-- Constraints for table `Course`
--
ALTER TABLE `Course`
  ADD CONSTRAINT `fk_course_department` FOREIGN KEY (`departmentID`) REFERENCES `Department` (`departmentID`) ON UPDATE CASCADE;

--
-- Constraints for table `CoursePrerequisite`
--
ALTER TABLE `CoursePrerequisite`
  ADD CONSTRAINT `fk_courseprereq_course` FOREIGN KEY (`courseID`) REFERENCES `Course` (`courseID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_courseprereq_prereq` FOREIGN KEY (`prerequisiteID`) REFERENCES `Course` (`courseID`) ON UPDATE CASCADE;

--
-- Constraints for table `CourseSection`
--
ALTER TABLE `CourseSection`
  ADD CONSTRAINT `fk_section_course` FOREIGN KEY (`courseID`) REFERENCES `Course` (`courseID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_section_faculty` FOREIGN KEY (`facultyID`) REFERENCES `Faculty` (`facultyID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_section_room` FOREIGN KEY (`roomID`) REFERENCES `Room` (`roomID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_section_semester` FOREIGN KEY (`semesterID`) REFERENCES `Semester` (`semesterID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_section_timeslot` FOREIGN KEY (`timeSlotID`) REFERENCES `TimeSlot` (`timeSlotID`) ON UPDATE CASCADE;

--
-- Constraints for table `Department`
--
ALTER TABLE `Department`
  ADD CONSTRAINT `fk_department_chair` FOREIGN KEY (`chairID`) REFERENCES `Faculty` (`facultyID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_department_room` FOREIGN KEY (`roomID`) REFERENCES `Room` (`roomID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_facultyID` FOREIGN KEY (`departmentAssistantID`) REFERENCES `Faculty` (`facultyID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Enrollment`
--
ALTER TABLE `Enrollment`
  ADD CONSTRAINT `fk_enrollment_section` FOREIGN KEY (`CRN`) REFERENCES `CourseSection` (`CRN`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_enrollment_student` FOREIGN KEY (`studentID`) REFERENCES `Student` (`studentID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Faculty`
--
ALTER TABLE `Faculty`
  ADD CONSTRAINT `fk_faculty_user` FOREIGN KEY (`facultyID`) REFERENCES `User` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `FacultyDepartment`
--
ALTER TABLE `FacultyDepartment`
  ADD CONSTRAINT `fk_facultydepartment_department` FOREIGN KEY (`departmentID`) REFERENCES `Department` (`departmentID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_facultydepartment_faculty` FOREIGN KEY (`facultyID`) REFERENCES `Faculty` (`facultyID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `FacultyHistory`
--
ALTER TABLE `FacultyHistory`
  ADD CONSTRAINT `fk_facultyhistory_course` FOREIGN KEY (`courseID`) REFERENCES `Course` (`courseID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_facultyhistory_crn` FOREIGN KEY (`CRN`) REFERENCES `CourseSection` (`CRN`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_facultyhistory_faculty` FOREIGN KEY (`facultyID`) REFERENCES `Faculty` (`facultyID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_facultyhistory_semester` FOREIGN KEY (`semesterID`) REFERENCES `Semester` (`semesterID`) ON UPDATE CASCADE;

--
-- Constraints for table `FullTimeFaculty`
--
ALTER TABLE `FullTimeFaculty`
  ADD CONSTRAINT `fk_ftfaculty_faculty` FOREIGN KEY (`fullTimeFacultyID`) REFERENCES `Faculty` (`facultyID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `FullTimeGraduate`
--
ALTER TABLE `FullTimeGraduate`
  ADD CONSTRAINT `fk_ftgrad_graduate` FOREIGN KEY (`fullTimeGraduateID`) REFERENCES `Graduate` (`graduateID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `FullTimeUndergraduate`
--
ALTER TABLE `FullTimeUndergraduate`
  ADD CONSTRAINT `fk_ftug_undergraduate` FOREIGN KEY (`fullTimeUndergraduateID`) REFERENCES `Undergraduate` (`undergraduateID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Graduate`
--
ALTER TABLE `Graduate`
  ADD CONSTRAINT `fk_graduate_department` FOREIGN KEY (`departmentID`) REFERENCES `Department` (`departmentID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_graduate_student` FOREIGN KEY (`graduateID`) REFERENCES `Student` (`studentID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Lab`
--
ALTER TABLE `Lab`
  ADD CONSTRAINT `fk_lab_room` FOREIGN KEY (`labID`) REFERENCES `Room` (`roomID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Lecture`
--
ALTER TABLE `Lecture`
  ADD CONSTRAINT `fk_lecture_room` FOREIGN KEY (`lectureID`) REFERENCES `Room` (`roomID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Login`
--
ALTER TABLE `Login`
  ADD CONSTRAINT `fk_login_user` FOREIGN KEY (`userID`) REFERENCES `User` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Major`
--
ALTER TABLE `Major`
  ADD CONSTRAINT `fk_major_department` FOREIGN KEY (`departmentID`) REFERENCES `Department` (`departmentID`) ON UPDATE CASCADE;

--
-- Constraints for table `MajorRequirement`
--
ALTER TABLE `MajorRequirement`
  ADD CONSTRAINT `fk_majorreq_course` FOREIGN KEY (`courseID`) REFERENCES `Course` (`courseID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_majorreq_major` FOREIGN KEY (`majorID`) REFERENCES `Major` (`majorID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Minor`
--
ALTER TABLE `Minor`
  ADD CONSTRAINT `fk_minor_department` FOREIGN KEY (`departmentID`) REFERENCES `Department` (`departmentID`) ON UPDATE CASCADE;

--
-- Constraints for table `MinorRequirement`
--
ALTER TABLE `MinorRequirement`
  ADD CONSTRAINT `fk_minreq_course` FOREIGN KEY (`courseID`) REFERENCES `Course` (`courseID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_minreq_minor` FOREIGN KEY (`minorID`) REFERENCES `Minor` (`minorID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Office`
--
ALTER TABLE `Office`
  ADD CONSTRAINT `fk_office_room` FOREIGN KEY (`officeID`) REFERENCES `Room` (`roomID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `PartTimeFaculty`
--
ALTER TABLE `PartTimeFaculty`
  ADD CONSTRAINT `fk_ptfaculty_faculty` FOREIGN KEY (`partTimeFacultyID`) REFERENCES `Faculty` (`facultyID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `PartTimeGraduate`
--
ALTER TABLE `PartTimeGraduate`
  ADD CONSTRAINT `fk_ptgrad_graduate` FOREIGN KEY (`partTimeGraduateID`) REFERENCES `Graduate` (`graduateID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `PartTimeUndergraduate`
--
ALTER TABLE `PartTimeUndergraduate`
  ADD CONSTRAINT `fk_ptug_undergraduate` FOREIGN KEY (`partTimeUndergraduateID`) REFERENCES `Undergraduate` (`undergraduateID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Room`
--
ALTER TABLE `Room`
  ADD CONSTRAINT `fk_room_building` FOREIGN KEY (`buildingID`) REFERENCES `Building` (`buildingID`) ON UPDATE CASCADE;

--
-- Constraints for table `StatStaff`
--
ALTER TABLE `StatStaff`
  ADD CONSTRAINT `fk_statstaff_user` FOREIGN KEY (`statStaffID`) REFERENCES `User` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Student`
--
ALTER TABLE `Student`
  ADD CONSTRAINT `fk_student_user` FOREIGN KEY (`studentID`) REFERENCES `User` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `StudentCourseSectionHistory`
--
ALTER TABLE `StudentCourseSectionHistory`
  ADD CONSTRAINT `fk_sch_course` FOREIGN KEY (`courseID`) REFERENCES `Course` (`courseID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sch_crn` FOREIGN KEY (`CRN`) REFERENCES `CourseSection` (`CRN`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sch_semester` FOREIGN KEY (`semesterID`) REFERENCES `Semester` (`semesterID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sch_student` FOREIGN KEY (`studentID`) REFERENCES `Student` (`studentID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `StudentHold`
--
ALTER TABLE `StudentHold`
  ADD CONSTRAINT `fk_studenthold_hold` FOREIGN KEY (`holdID`) REFERENCES `Hold` (`holdID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_studenthold_student` FOREIGN KEY (`studentID`) REFERENCES `Student` (`studentID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `StudentMajor`
--
ALTER TABLE `StudentMajor`
  ADD CONSTRAINT `fk_studentmajor_major` FOREIGN KEY (`majorID`) REFERENCES `Major` (`majorID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_studentmajor_student` FOREIGN KEY (`studentID`) REFERENCES `Student` (`studentID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `StudentMinor`
--
ALTER TABLE `StudentMinor`
  ADD CONSTRAINT `fk_studentminor_minor` FOREIGN KEY (`minorID`) REFERENCES `Minor` (`minorID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_studentminor_student` FOREIGN KEY (`studentID`) REFERENCES `Student` (`studentID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `TimeSlotDay`
--
ALTER TABLE `TimeSlotDay`
  ADD CONSTRAINT `fk_timeslotday_days` FOREIGN KEY (`daysID`) REFERENCES `Days` (`daysID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_timeslotday_timeslot` FOREIGN KEY (`timeSlotID`) REFERENCES `TimeSlot` (`timeSlotID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `TimeSlotPeriod`
--
ALTER TABLE `TimeSlotPeriod`
  ADD CONSTRAINT `fk_timeslotperiod_period` FOREIGN KEY (`periodID`) REFERENCES `Period` (`periodID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_timeslotperiod_timeslot` FOREIGN KEY (`timeSlotID`) REFERENCES `TimeSlot` (`timeSlotID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Undergraduate`
--
ALTER TABLE `Undergraduate`
  ADD CONSTRAINT `fk_undergraduate_student` FOREIGN KEY (`undergraduateID`) REFERENCES `Student` (`studentID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;
