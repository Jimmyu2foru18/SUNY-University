--
-- Table structure for table `Minor`
--

CREATE TABLE `Minor` (
  `minorID` varchar(8) NOT NULL,
  `minorName` varchar(255) NOT NULL,
  `departmentID` varchar(8) NOT NULL,
  `creditsRequired` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Minor`
--

INSERT INTO `Minor` (`minorID`, `minorName`, `departmentID`, `creditsRequired`) VALUES
('ACCMN01', 'Accounting', 'ACC', 18),
('AMCMN01', 'Mathematics', 'AMC', 18),
('BIOMN01', 'Biology', 'BIO', 18),
('ENGMN01', 'English', 'ENG', 18),
('MCSMN01', 'Computer Science', 'MCS', 18),
('MLGMN01', 'Modern Languages', 'MLG', 18),
('MMFMN01', 'Media Studies', 'MMF', 18),
('PHLMN01', 'Philosophy', 'PHL', 18),
('PSYMN01', 'Psychology', 'PSY', 18),
('SOCMN01', 'Sociology', 'SOC', 18),
('SPSMN01', 'Political Science', 'SPS', 18),
('VIAMN01', 'Visual Arts', 'VIA', 18);
