--
-- Table structure for table `Department`
--

CREATE TABLE `Department` (
  `departmentID` varchar(8) NOT NULL,
  `departmentName` varchar(255) DEFAULT NULL,
  `roomID` varchar(16) NOT NULL,
  `chairID` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phoneNumber` varchar(20) DEFAULT NULL,
  `departmentAssistantID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Department`
--

INSERT INTO `Department` (`departmentID`, `departmentName`, `roomID`, `chairID`, `email`, `phoneNumber`, `departmentAssistantID`) VALUES
('ACC', 'Accounting', 'BUSICEN201', 60099748, 'acc@SUNYUniversity.edu', '516-649-4965', 60097963),
('AMC', 'Applied Mathematics & Computing', 'ENGCENT201', 60099765, 'amc@SUNYUniversity.edu', '516-739-7539', 60103352),
('BIO', 'Biology', 'SCIHALL201', 60104440, 'bio@SUNYUniversity.edu', '516-697-2709', 60104593),
('CPH', 'Chemistry & Physics', 'SCIHALL202', 60104151, 'cph@SUNYUniversity.edu', '516-467-5465', 60103760),
('EDU', 'Education', 'EDUHALL201', 60098643, 'edu@SUNYUniversity.edu', '516-553-5014', 60098626),
('ENG', 'English', 'LIBRARY201', 60102808, 'eng@SUNYUniversity.edu', '516-602-1670', 60102859),
('HPL', 'Health & Physical Learning', 'HEALTHSC201', 60102485, 'hpl@SUNYUniversity.edu', '516-696-5379', 60102451),
('MCS', 'Mathematics & Computer Science', 'ENGCENT202', 60103896, 'mcs@SUNYUniversity.edu', '516-612-3257', 60103539),
('MLG', 'Modern Languages', 'LIBRARY202', 60100394, 'mlg@SUNYUniversity.edu', '516-249-6451', 60100377),
('MMF', 'Media, Music & Film', 'ARTSBLD201', 60099884, 'mmf@SUNYUniversity.edu', '516-604-5897', 60098847),
('PEL', 'Performing & Literary Arts', 'ARTSBLD202', 60101159, 'pel@SUNYUniversity.edu', '516-264-2754', 60100921),
('PHL', 'Philosophy', 'ADMINBLD201', 60100700, 'phl@SUNYUniversity.edu', '516-356-9695', 60097861),
('PSY', 'Psychology', 'HEALTHSC202', 60101754, 'psy@SUNYUniversity.edu', '516-618-8281', 60102179),
('SOC', 'Sociology', 'ADMINBLD202', 60101363, 'soc@SUNYUniversity.edu', '516-899-3515', 60101397),
('SPS', 'Social & Political Sciences', 'ADMINBLD203', 60100615, 'sps@SUNYUniversity.edu', '516-573-1697', 60097912),
('VIA', 'Visual & Interdisciplinary Arts', 'ARTSBLD203', 60100020, 'via@SUNYUniversity.edu', '516-565-6258', 60100122);
