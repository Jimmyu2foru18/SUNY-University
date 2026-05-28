--
-- Table structure for table `Semester`
--

CREATE TABLE `Semester` (
  `semesterID` varchar(8) NOT NULL,
  `semesterName` varchar(15) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `beginDate` date DEFAULT NULL,
  `endDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Semester`
--

INSERT INTO `Semester` (`semesterID`, `semesterName`, `year`, `beginDate`, `endDate`) VALUES
('2021FA', 'Fall', 2021, '2021-08-25', '2021-12-12'),
('2022FA', 'Fall', 2022, '2022-08-25', '2022-12-12'),
('2022SP', 'Spring', 2022, '2022-01-24', '2022-05-15'),
('2023FA', 'Fall', 2023, '2023-08-25', '2023-12-12'),
('2023SP', 'Spring', 2023, '2023-01-24', '2023-05-15'),
('2024FA', 'Fall', 2024, '2024-08-25', '2024-12-12'),
('2024SP', 'Spring', 2024, '2024-01-24', '2024-05-15'),
('2025FA', 'Fall', 2025, '2025-08-25', '2025-12-12'),
('2025SP', 'Spring', 2025, '2025-01-24', '2025-05-15'),
('2026SP', 'Spring', 2026, '2026-01-24', '2026-05-15');
