<?php
/**
 * Centralized Business Logic for Bridgeport University Portal
 * Standardized to match the final database schema and requirements.
 */

/**
 * Get current user profile data
 */
function getCurrentUser($pdo, $userID, $userType) {
    try {
        $stmt = $pdo->prepare("SELECT U.*, L.email, L.userType FROM User U JOIN Login L ON U.userID = L.userID WHERE U.userID = ? LIMIT 1");
        $stmt->execute([$userID]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) return null;
        return $user;
    } catch (Exception $e) {
        error_log("Error in getCurrentUser: " . $e->getMessage());
        return null;
    }
}

/**
 * Get filtered course sections
 */
function getFilteredCourseSections($pdo, $semesterID, $filters = []) {
    $query = "
        SELECT CS.CRN, CS.courseID, C.courseName, U.firstName as instructorFirst, U.lastName as instructorLast, 
               CS.roomID as buildingName, CS.sectionNumber as roomNumber, CS.capacity,
               COUNT(E.CRN) as enrolledCount
        FROM CourseSection CS
        JOIN Course C ON CS.courseID = C.courseID
        JOIN User U ON CS.facultyID = U.userID
        LEFT JOIN Enrollment E ON CS.CRN = E.CRN
        WHERE CS.semesterID = :semester
    ";
    $params = ['semester' => $semesterID];
    
    if (!empty($filters['facultyID'])) {
        $query .= " AND CS.facultyID = :fid";
        $params['fid'] = $filters['facultyID'];
    }
    
    $query .= " GROUP BY CS.CRN";
    
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Get student profile
 */
function getStudentProfile($pdo, $studentID) {
    try {
        $stmt = $pdo->prepare("
            SELECT U.userID, U.firstName, U.lastName, S.studentType, S.year, L.email 
            FROM User U 
            JOIN Login L ON U.userID = L.userID
            JOIN Student S ON U.userID = S.studentID 
            WHERE U.userID = ? LIMIT 1
        ");
        $stmt->execute([$studentID]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Get active holds for a student
 */
function getStudentHolds($pdo, $studentID) {
    try {
        $stmt = $pdo->prepare("
            SELECT H.holdType, H.holdDescription 
            FROM StudentHold SH
            JOIN Hold H ON SH.holdID = H.holdID
            WHERE SH.studentID = ?
        ");
        $stmt->execute([$studentID]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Get enrollments for a student in a specific semester
 */
function getStudentEnrollments($pdo, $studentID, $semesterID) {
    try {
        $stmt = $pdo->prepare("
            SELECT E.CRN, C.courseID, C.courseName, U.firstName as instructorFirst, U.lastName as instructorLast, CS.roomID, E.grade
            FROM Enrollment E
            JOIN CourseSection CS ON E.CRN = CS.CRN
            JOIN Course C ON CS.courseID = C.courseID
            JOIN User U ON CS.facultyID = U.userID
            WHERE E.studentID = ? AND CS.semesterID = ?
        ");
        $stmt->execute([$studentID, $semesterID]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Get available semesters
 */
function getAvailableSemesters($pdo) {
    try {
        $stmt = $pdo->query("SELECT DISTINCT semesterID FROM CourseSection ORDER BY semesterID DESC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Get all faculty members
 */
function getAllFaculty($pdo) {
    try {
        $stmt = $pdo->query("
            SELECT U.userID, U.firstName, U.lastName, F.rank_, F.facultyType 
            FROM User U
            JOIN Faculty F ON U.userID = F.facultyID
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Get all departments
 */
function getAllDepartments($pdo) {
    try {
        $stmt = $pdo->query("SELECT departmentID, departmentName FROM Department ORDER BY departmentName ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Get student roster for a specific section
 */
function getSectionRoster($pdo, $crn) {
    try {
        $stmt = $pdo->prepare("
            SELECT U.firstName, U.lastName, S.studentID, E.grade 
            FROM Enrollment E 
            JOIN Student S ON E.studentID = S.studentID 
            JOIN User U ON S.studentID = U.userID 
            WHERE E.CRN = ?
        ");
        $stmt->execute([$crn]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Get course details
 */
function getCourseDetails($pdo, $crn) {
    try {
        $stmt = $pdo->prepare("
            SELECT C.courseID, C.courseName, C.credits, C.courseDescription,
                   (SELECT GROUP_CONCAT(prerequisiteID) FROM CoursePrerequisite WHERE courseID = C.courseID) as prerequisites
            FROM Course C
            JOIN CourseSection CS ON C.courseID = CS.courseID
            WHERE CS.CRN = ?
        ");
        $stmt->execute([$crn]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return null;
    }
}
?>
