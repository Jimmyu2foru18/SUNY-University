--
-- Table structure for table `TimeSlotPeriod`
--

CREATE TABLE `TimeSlotPeriod` (
  `timeSlotID` int(11) NOT NULL,
  `periodID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `TimeSlotPeriod`
--

INSERT INTO `TimeSlotPeriod` (`timeSlotID`, `periodID`) VALUES
(1, 1),
(2, 1),
(3, 2),
(4, 2),
(5, 3),
(6, 3),
(7, 5),
(8, 5),
(9, 6),
(10, 6),
(11, 7),
(12, 7),
(13, 8),
(14, 8),
(15, 9),
(16, 9),
(17, 9),
(18, 9),
(19, 9),
(20, 10),
(21, 10),
(22, 10),
(23, 10),
(24, 10),
(25, 11),
(26, 11),
(27, 11),
(28, 11),
(29, 11);
