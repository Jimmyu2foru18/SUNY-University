--
-- Table structure for table `Hold`
--

CREATE TABLE `Hold` (
  `holdID` int(11) NOT NULL,
  `holdType` enum('Financial','Health','Academic','Disciplinary') NOT NULL,
  `holdDescription` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Hold`
--

INSERT INTO `Hold` (`holdID`, `holdType`, `holdDescription`) VALUES
(0, 'Financial', 'outstanding balance'),
(1, 'Financial', 'Minor balance or library fine; account review required.'),
(2, 'Health', 'Missing immunization or health clearance documentation.'),
(3, 'Academic', 'Advising required before registration; contact advisor.'),
(4, 'Financial', 'Outstanding tuition balance; payment plan required.'),
(5, 'Academic', 'Academic probation review required; meeting with chair.'),
(6, 'Disciplinary', 'Conduct meeting required; disciplinary review pending.'),
(7, 'Disciplinary', 'Registration blocked pending dean review; urgent action.'),
(8, 'Disciplinary', 'Immediate removal/suspension review; highest severity hold.');
