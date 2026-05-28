
--
-- Table structure for table `Building`
--

CREATE TABLE `Building` (
  `buildingID` varchar(8) NOT NULL DEFAULT '',
  `buildingName` varchar(255) DEFAULT NULL,
  `buildingUsage` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Building`
--

INSERT INTO `Building` (`buildingID`, `buildingName`, `buildingUsage`) VALUES
('ADMINBLD', 'Administration Building', 'Administrative offices and student services'),
('ARTSBLD', 'Arts & Media Building', 'Studios, lecture rooms, editing suites'),
('BUSICEN', 'Business Center', 'Lecture rooms, faculty offices, meeting rooms'),
('EDUHALL', 'Education Hall', 'Classrooms, faculty offices, advising spaces'),
('ENGCENT', 'Engineering Center', 'Lecture rooms, computer labs, maker spaces'),
('HEALTHSC', 'Health Sciences Building', 'Teaching labs, simulation rooms, lecture'),
('LIBRARY', 'Library & Learning Commons', 'Library, quiet study, lecture rooms'),
('SCIHALL', 'Science & Research Hall', 'Mostly labs with some lecture rooms');

-- --------------------------------------------------------
