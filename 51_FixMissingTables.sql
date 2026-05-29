-- 
-- Patch: Create missing content tables
--

CREATE TABLE IF NOT EXISTS `About` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section` varchar(50) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `About` (`section`, `content`) VALUES
('Mission', 'To provide transformative educational experiences that prepare students for leadership and service in a global society.'),
('Vision', 'To be a premier global university recognized for our impact on society.');

CREATE TABLE IF NOT EXISTS `AdmissionsProcess` (
  `stepNumber` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`stepNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `AdmissionsProcess` (`stepNumber`, `title`, `description`) VALUES
(1, 'Choose Your Program', 'Explore our diverse academic offerings.'),
(2, 'Prepare Your Documents', 'Transcripts and recommendations.'),
(3, 'Submit Your Application', 'Use our online portal.');

CREATE TABLE IF NOT EXISTS `SiteInfo` (
  `keyName` varchar(50) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`keyName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `SiteInfo` (`keyName`, `value`) VALUES
('address', '123 University Ave, Albany, NY 12203'),
('phone', '(518) 555-0199'),
('email', 'info@suny.edu'),
('hours', 'Monday - Friday: 9 AM - 5 PM');
