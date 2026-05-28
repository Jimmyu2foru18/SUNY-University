--
-- Table structure for table `TimeSlotDay`
--

CREATE TABLE `TimeSlotDay` (
  `timeSlotID` int(11) NOT NULL,
  `daysID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `TimeSlotDay`
--

INSERT INTO `TimeSlotDay` (`timeSlotID`, `daysID`) VALUES
(1, 1),
(3, 1),
(5, 1),
(7, 1),
(9, 1),
(11, 1),
(13, 1),
(15, 1),
(20, 1),
(25, 1),
(2, 2),
(4, 2),
(6, 2),
(8, 2),
(10, 2),
(12, 2),
(14, 2),
(16, 2),
(21, 2),
(26, 2),
(1, 3),
(3, 3),
(5, 3),
(7, 3),
(9, 3),
(11, 3),
(13, 3),
(17, 3),
(22, 3),
(27, 3),
(2, 4),
(4, 4),
(6, 4),
(8, 4),
(10, 4),
(12, 4),
(14, 4),
(18, 4),
(23, 4),
(28, 4),
(19, 5),
(24, 5),
(29, 5);
