--
-- Table structure for table `StatStaff`
--

CREATE TABLE `StatStaff` (
  `statStaffID` int(11) NOT NULL,
  `viewLevel` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `StatStaff`
--

INSERT INTO `StatStaff` (`statStaffID`, `viewLevel`) VALUES
(60104610, 2),
(60104627, 3),
(60104644, 2),
(60104661, 1);
