--
-- Table structure for table `Admin`
--

CREATE TABLE `Admin` (
  `adminID` int(11) NOT NULL,
  `adminType` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Admin`
--

INSERT INTO `Admin` (`adminID`, `adminType`) VALUES
(60104678, 1),
(60104695, 1),
(60104712, 2);
