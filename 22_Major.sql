--
-- Table structure for table `Major`
--

CREATE TABLE `Major` (
  `majorID` varchar(8) NOT NULL,
  `majorName` varchar(255) DEFAULT NULL,
  `departmentID` varchar(8) NOT NULL,
  `creditsRequired` int(11) DEFAULT NULL,
  `degreeLevel` enum('Undergraduate','Masters','PhD') NOT NULL DEFAULT 'Undergraduate'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Major`
--

INSERT INTO `Major` (`majorID`, `majorName`, `departmentID`, `creditsRequired`, `degreeLevel`) VALUES
('ACCMS01', 'Accounting, M.S.', 'ACC', 36, 'Masters'),
('ACCUG01', 'Accounting, B.S.', 'ACC', 120, 'Undergraduate'),
('AMCMS01', 'Data Science, M.S.', 'AMC', 36, 'Masters'),
('AMCUG01', 'Applied Mathematics, B.S.', 'AMC', 120, 'Undergraduate'),
('AMCUG02', 'Mathematics, B.S.', 'AMC', 120, 'Undergraduate'),
('BIOMS01', 'Biology, M.S.', 'BIO', 36, 'Masters'),
('BIOPH01', 'Biology, Ph.D.', 'BIO', 60, 'PhD'),
('BIOUG01', 'Biology, B.S.', 'BIO', 120, 'Undergraduate'),
('CPHMS001', 'Chemistry, M.S.', 'CPH', 30, 'Masters'),
('CPHUG01', 'Chemistry, B.S.', 'CPH', 120, 'Undergraduate'),
('CPHUG02', 'Physics, B.S.', 'CPH', 120, 'Undergraduate'),
('EDUMS01', 'Education, M.Ed.', 'EDU', 36, 'Masters'),
('EDUUG01', 'Education, B.A.', 'EDU', 120, 'Undergraduate'),
('ENGMS001', 'English, M.A.', 'ENG', 30, 'Masters'),
('ENGUG01', 'English, B.A.', 'ENG', 120, 'Undergraduate'),
('HPLMS001', 'Exercise Science, M.S.', 'HPL', 30, 'Masters'),
('HPLUG01', 'Exercise Science, B.S.', 'HPL', 120, 'Undergraduate'),
('MCSMS01', 'Computer Science, M.S.', 'MCS', 36, 'Masters'),
('MCSPH01', 'Computer Science, Ph.D.', 'MCS', 60, 'PhD'),
('MCSUG01', 'Computer Science, B.S.', 'MCS', 120, 'Undergraduate'),
('MLGMS001', 'Modern Languages, M.A.', 'MLG', 30, 'Masters'),
('MLGUG01', 'Spanish Language and Culture, B.A.', 'MLG', 120, 'Undergraduate'),
('MLGUG02', 'French Language and Culture, B.A.', 'MLG', 120, 'Undergraduate'),
('MMFMS001', 'Film and Media Studies, M.A.', 'MMF', 30, 'Masters'),
('MMFUG01', 'Media Studies, B.A.', 'MMF', 120, 'Undergraduate'),
('MMFUG02', 'Film Production, B.F.A.', 'MMF', 120, 'Undergraduate'),
('PELMS001', 'Theatre Arts, M.F.A.', 'PEL', 30, 'Masters'),
('PELUG01', 'Theatre Arts, B.A.', 'PEL', 120, 'Undergraduate'),
('PHLMS001', 'Philosophy, M.A.', 'PHL', 30, 'Masters'),
('PHLUG01', 'Philosophy, B.A.', 'PHL', 120, 'Undergraduate'),
('PSYMS01', 'Psychology, M.S.', 'PSY', 36, 'Masters'),
('PSYPH01', 'Psychology, Ph.D.', 'PSY', 60, 'PhD'),
('PSYUG01', 'Psychology, B.A.', 'PSY', 120, 'Undergraduate'),
('SOCMS001', 'Sociology, M.A.', 'SOC', 30, 'Masters'),
('SOCUG01', 'Sociology, B.A.', 'SOC', 120, 'Undergraduate'),
('SPSMS01', 'Public Policy, M.P.P.', 'SPS', 36, 'Masters'),
('SPSUG01', 'Political Science, B.A.', 'SPS', 120, 'Undergraduate'),
('VIAMS001', 'Visual Arts, M.F.A.', 'VIA', 30, 'Masters'),
('VIAUG01', 'Visual Arts, B.F.A.', 'VIA', 120, 'Undergraduate');
