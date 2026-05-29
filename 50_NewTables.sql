-- 
-- Create missing tables for dynamic content
--

CREATE TABLE IF NOT EXISTS `Research` (
  `researchID` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `facultyID` int(11) NOT NULL,
  PRIMARY KEY (`researchID`),
  FOREIGN KEY (`facultyID`) REFERENCES `Faculty`(`facultyID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `Research` (`title`, `description`, `facultyID`) VALUES
('Advanced AI Ethics', 'Exploring ethical frameworks for autonomous systems.', 60097810),
('Sustainable Energy Solutions', 'Developing new materials for high-efficiency solar panels.', 60097827);

CREATE TABLE IF NOT EXISTS `News` (
  `newsID` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `category` varchar(50),
  `date` date,
  `summary` text,
  PRIMARY KEY (`newsID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `News` (`title`, `category`, `date`, `summary`) VALUES
('SUNY Innovation Rank', 'Academics', '2026-05-15', 'Ranked in Top 50 for Innovation.'),
('New AI Center', 'Research', '2026-05-10', 'Opening new AI Research Center.');

CREATE TABLE IF NOT EXISTS `Events` (
  `eventID` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `eventDate` date,
  `startTime` time,
  `location` varchar(255),
  `category` varchar(50),
  PRIMARY KEY (`eventID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `Events` (`title`, `eventDate`, `startTime`, `location`, `category`) VALUES
('Fall Orientation', '2026-08-24', '09:00:00', 'Main Quad', 'Orientation');

CREATE TABLE IF NOT EXISTS `ContactMessage` (
  `messageID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100),
  `email` varchar(100),
  `subject` varchar(255),
  `message` text,
  `submittedAt` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`messageID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
