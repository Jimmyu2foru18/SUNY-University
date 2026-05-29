-- SQL Patch to create Appointment table (Without strict Foreign Key constraint)
CREATE TABLE IF NOT EXISTS `Appointment` (
  `appointmentID` int(11) NOT NULL AUTO_INCREMENT,
  `studentID` int(11) NOT NULL,
  `appointmentType` varchar(50) NOT NULL,
  `appointmentDate` date NOT NULL,
  `appointmentTime` time NOT NULL,
  `status` varchar(20) DEFAULT 'Scheduled',
  `message` text,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`appointmentID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
