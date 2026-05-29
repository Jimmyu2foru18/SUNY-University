-- Database Optimization Patch for Bridgeport University Portal
-- This file adds indexes to speed up table joins and searches.

-- Speed up lookups for students in enrollment
CREATE INDEX idx_enrollment_student ON Enrollment(studentID);
CREATE INDEX idx_enrollment_crn ON Enrollment(CRN);

-- Speed up lookups for course sections
CREATE INDEX idx_cs_courseID ON CourseSection(courseID);
CREATE INDEX idx_cs_semester ON CourseSection(semesterID);
CREATE INDEX idx_cs_faculty ON CourseSection(facultyID);

-- Speed up user lookups
CREATE INDEX idx_user_type ON User(userType);
