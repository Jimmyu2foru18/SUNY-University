--
-- Indexes for dumped tables
--

--
-- Indexes for table `Admin`
--
ALTER TABLE `Admin`
  ADD KEY `adminID` (`adminID`);

--
-- Indexes for table `AdvisorAdvisee`
--
ALTER TABLE `AdvisorAdvisee`
  ADD KEY `idx_advisoradvisee_student` (`studentID`),
  ADD KEY `idx_advisoradvisee_faculty` (`facultyID`),
  ADD KEY `idx_advisoradvisee_date` (`appointmentDate`),
  ADD KEY `idx_advisoradvisee_status` (`status`);

--
-- Indexes for table `Attendance`
--
ALTER TABLE `Attendance`
  ADD KEY `idx_attendance_crn` (`CRN`),
  ADD KEY `idx_attendance_timeslot` (`timeSlotID`);

--
-- Indexes for table `Building`
--
-- Primary Key already exists.

--
-- Indexes for table `Course`
--
ALTER TABLE `Course`
  ADD KEY `idx_course_department` (`departmentID`);

--
-- Indexes for table `CoursePrerequisite`
--
ALTER TABLE `CoursePrerequisite`
  ADD KEY `idx_prerequisiteID` (`prerequisiteID`);

--
-- Indexes for table `CourseSection`
--
ALTER TABLE `CourseSection`
  ADD KEY `idx_section_course` (`courseID`),
  ADD KEY `idx_section_faculty` (`facultyID`),
  ADD KEY `idx_section_room` (`roomID`),
  ADD KEY `idx_section_timeslot` (`timeSlotID`),
  ADD KEY `idx_section_semester` (`semesterID`);

--
-- Indexes for table `Days`
--
-- Primary Key already exists.

--
-- Indexes for table `Department`
--
ALTER TABLE `Department`
  ADD UNIQUE KEY `uq_department_room` (`roomID`),
  ADD UNIQUE KEY `uq_department_chair` (`chairID`),
  ADD KEY `fk_facultyID` (`departmentAssistantID`);

--
-- Indexes for table `Enrollment`
--
ALTER TABLE `Enrollment`
  ADD KEY `idx_enrollment_crn` (`CRN`);

--
-- Indexes for table `Faculty`
--
-- Primary Key already exists.

--
-- Indexes for table `FacultyDepartment`
--
ALTER TABLE `FacultyDepartment`
  ADD KEY `idx_facultydepartment_department` (`departmentID`);

--
-- Indexes for table `FacultyHistory`
--
ALTER TABLE `FacultyHistory`
  ADD KEY `idx_facultyhistory_crn` (`CRN`),
  ADD KEY `idx_facultyhistory_course` (`courseID`),
  ADD KEY `idx_facultyhistory_semester` (`semesterID`);

--
-- Indexes for table `FullTimeFaculty`
--
-- Primary Key already exists.

--
-- Indexes for table `FullTimeGraduate`
--
-- Primary Key already exists.

--
-- Indexes for table `FullTimeUndergraduate`
--
-- Primary Key already exists.

--
-- Indexes for table `Graduate`
--
ALTER TABLE `Graduate`
  ADD KEY `idx_graduate_department` (`departmentID`);

--
-- Indexes for table `Hold`
--
-- Primary Key already exists.

--
-- Indexes for table `Lab`
--
-- Primary Key already exists.

--
-- Indexes for table `Lecture`
--
-- Primary Key already exists.

--
-- Indexes for table `Login`
--
ALTER TABLE `Login`
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `Major`
--
ALTER TABLE `Major`
  ADD KEY `idx_major_department` (`departmentID`);

--
-- Indexes for table `MajorRequirement`
--
ALTER TABLE `MajorRequirement`
  ADD KEY `idx_majorreq_course` (`courseID`);

--
-- Indexes for table `Minor`
--
ALTER TABLE `Minor`
  ADD KEY `idx_minor_department` (`departmentID`);

--
-- Indexes for table `MinorRequirement`
--
ALTER TABLE `MinorRequirement`
  ADD KEY `idx_minreq_course` (`courseID`);

--
-- Indexes for table `Office`
--
-- Primary Key already exists.

--
-- Indexes for table `PartTimeFaculty`
--
-- Primary Key already exists.

--
-- Indexes for table `PartTimeGraduate`
--
-- Primary Key already exists.

--
-- Indexes for table `PartTimeUndergraduate`
--
-- Primary Key already exists.

--
-- Indexes for table `Period`
--
-- Primary Key already exists.

--
-- Indexes for table `Room`
--
ALTER TABLE `Room`
  ADD UNIQUE KEY `uq_building_roomNumber` (`buildingID`,`roomNumber`),
  ADD KEY `idx_room_building` (`buildingID`);

--
-- Indexes for table `Semester`
--
-- Primary Key already exists.

--
-- Indexes for table `StatStaff`
--
-- Primary Key already exists.

--
-- Indexes for table `Student`
--
-- Primary Key already exists.

--
-- Indexes for table `StudentCourseSectionHistory`
--
ALTER TABLE `StudentCourseSectionHistory`
  ADD KEY `idx_sch_crn` (`CRN`),
  ADD KEY `idx_sch_course` (`courseID`),
  ADD KEY `idx_semester` (`semesterID`);

--
-- Indexes for table `StudentHold`
--
ALTER TABLE `StudentHold`
  ADD KEY `idx_studenthold_hold` (`holdID`);

--
-- Indexes for table `StudentMajor`
--
ALTER TABLE `StudentMajor`
  ADD KEY `idx_studentmajor_major` (`majorID`);

--
-- Indexes for table `StudentMinor`
--
ALTER TABLE `StudentMinor`
  ADD KEY `idx_studentminor_minor` (`minorID`);

--
-- Indexes for table `TimeSlot`
--
-- Primary Key already exists.

--
-- Indexes for table `TimeSlotDay`
--
ALTER TABLE `TimeSlotDay`
  ADD KEY `idx_timeslotday_days` (`daysID`);

--
-- Indexes for table `TimeSlotPeriod`
--
ALTER TABLE `TimeSlotPeriod`
  ADD KEY `idx_timeslotperiod_period` (`periodID`);

--
-- Indexes for table `Undergraduate`
--
-- Primary Key already exists.

--
-- Indexes for table `User`
--
-- Primary Key already exists.
