--
-- Table structure for table `Period`
--

CREATE TABLE `Period` (
  `periodID` int(11) NOT NULL,
  `startTime` time DEFAULT NULL,
  `endTime` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Period`
--

INSERT INTO `Period` (`periodID`, `startTime`, `endTime`) VALUES
(2, '09:50:00', '11:30:00'),
(3, '11:40:00', '13:20:00'),
(4, '13:30:00', '14:30:00'),
(5, '14:40:00', '16:20:00'),
(6, '16:30:00', '18:10:00'),
(7, '18:20:00', '20:00:00'),
(8, '20:10:00', '21:50:00'),
(9, '08:00:00', '11:00:00'),
(10, '14:40:00', '17:40:00'),
(11, '17:50:00', '20:50:00');
