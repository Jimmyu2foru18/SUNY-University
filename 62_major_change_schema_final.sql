-- SQL Patch to create MajorChangeRequest table
-- This version removes foreign key constraints to ensure compatibility with shared hosting
CREATE TABLE IF NOT EXISTS `MajorChangeRequest` (
  `requestID` int(11) NOT NULL AUTO_INCREMENT,
  `studentID` int(11) NOT NULL,
  `oldMajorID` varchar(20) DEFAULT NULL,
  `newMajorID` varchar(20) NOT NULL,
  `status` enum('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`requestID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
