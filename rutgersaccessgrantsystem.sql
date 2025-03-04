-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Dec 11, 2023 at 10:56 PM
-- Server version: 5.7.39
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rutgersaccessgrantsystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `access`
--

CREATE TABLE `access` (
  `accessID` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `roleID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `access`
--

INSERT INTO `access` (`accessID`, `userID`, `roleID`) VALUES
(1000, NULL, NULL),
(1001, 1000, 1000),
(1002, 1002, 1006),
(1003, 1003, 1007),
(1004, 1004, 1008),
(1005, 1005, 1009),
(1006, 1006, 1010),
(1007, 1007, 1011),
(1008, 1008, 1012);

-- --------------------------------------------------------

--
-- Table structure for table `application`
--

CREATE TABLE `application` (
  `systemID` int(11) NOT NULL,
  `systemName` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `application`
--

INSERT INTO `application` (`systemID`, `systemName`) VALUES
(1000, 'START '),
(1001, 'START '),
(1002, 'START '),
(1003, 'START '),
(1004, 'START '),
(1005, 'START '),
(1006, 'START '),
(1007, 'START '),
(1008, 'START ');

-- --------------------------------------------------------

--
-- Table structure for table `employeetypes`
--

CREATE TABLE `employeetypes` (
  `empTypeID` int(11) NOT NULL,
  `empType` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `employeetypes`
--

INSERT INTO `employeetypes` (`empTypeID`, `empType`) VALUES
(1000, 'staff'),
(1001, 'staff'),
(1002, 'staff'),
(1003, 'staff'),
(1004, 'staff'),
(1005, 'staff'),
(1006, 'staff'),
(1007, 'staff');

-- --------------------------------------------------------

--
-- Table structure for table `forms`
--

CREATE TABLE `forms` (
  `formID` int(11) NOT NULL,
  `userFirstName` text NOT NULL,
  `userLastName` text NOT NULL,
  `userEmail` text NOT NULL,
  `entryDate` datetime NOT NULL,
  `effDate` text NOT NULL,
  `userNetID` varchar(50) NOT NULL,
  `systemID` int(11) DEFAULT NULL,
  `roleID` int(11) DEFAULT NULL,
  `schoolDeptID` int(11) DEFAULT NULL,
  `userJustification` text,
  `submitterUserID` int(11) DEFAULT NULL,
  `requestID` int(11) DEFAULT NULL,
  `userEmployeeID` int(11) DEFAULT NULL,
  `action` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `forms`
--

INSERT INTO `forms` (`formID`, `userFirstName`, `userLastName`, `userEmail`, `entryDate`, `effDate`, `userNetID`, `systemID`, `roleID`, `schoolDeptID`, `userJustification`, `submitterUserID`, `requestID`, `userEmployeeID`, `action`) VALUES
(1000, 'Sam', 'Lee', 'sl@email.com', '2023-11-21 00:00:00', '2023-11-22', 'sl123', 1000, 1000, 1000, 'comment\r\n', 1000, 1000, 1000, 0),
(1001, 'Sheriza', 'Mohamed', 'sm123@email.com', '2023-11-21 00:00:00', '2023-11-22', 'sm123', 1001, 1002, 1001, 'sheriza comment', 1001, 1001, 1001, 0),
(1002, 'Brandon', 'Bautista', 'bsb@email.com', '2023-11-21 00:00:00', '2023-11-22', 'bsb120', 1002, 1006, 1002, 'brandon', 1002, 1002, 1002, 0),
(1003, 'Sam', 'Lee', 'sl@email.com', '2023-11-21 00:00:00', '2023-11-22', 'sl123', 1003, 1007, 1003, 'sam comment 2', 1003, 1003, 1003, 0),
(1004, 'William', 'Arevalo', 'wa@email', '2023-12-07 00:00:00', '2023-12-08', 'wa123', 1004, 1008, 1004, '', 1004, 1004, 1003, 0),
(1005, 'Laura', 'Negrin', 'ln@email', '2023-12-07 00:00:00', '2023-12-08', 'ln123', 1005, 1009, 1005, '', 1005, 1005, 1004, 0),
(1006, 'Sam', 'Lee', 'sl@email.com', '2023-12-07 00:00:00', '2023-12-08', 'sl123', 1006, 1010, 1006, '', 1006, 1006, 1005, 0),
(1007, 'Sam', 'Lee', 'sl@email.com', '2023-12-11 00:00:00', '2023-12-12', 'sl123', 1007, 1011, 1007, '', 1007, 1007, 1006, 0),
(1008, 'John', 'Lee', 'sl@email.com', '2023-12-11 00:00:00', '2023-12-12', 'sl123', 1008, 1012, 1008, '', 1008, 1008, 1007, 0);

-- --------------------------------------------------------

--
-- Table structure for table `formupdatelog`
--

CREATE TABLE `formupdatelog` (
  `updateID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `formID` int(11) NOT NULL,
  `updateTypeID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `formupdatelogtypes`
--

CREATE TABLE `formupdatelogtypes` (
  `updateTypeID` int(11) NOT NULL,
  `actionName` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `formupdatelogtypes`
--

INSERT INTO `formupdatelogtypes` (`updateTypeID`, `actionName`) VALUES
(1000, 'submit'),
(1001, 'submit'),
(1002, 'submit'),
(1003, 'submit'),
(1004, 'submit'),
(1005, 'submit'),
(1006, 'submit'),
(1007, 'submit'),
(1008, 'submit');

-- --------------------------------------------------------

--
-- Table structure for table `requesttypes`
--

CREATE TABLE `requesttypes` (
  `requestID` int(11) NOT NULL,
  `requestName` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `requesttypes`
--

INSERT INTO `requesttypes` (`requestID`, `requestName`) VALUES
(1000, 'newEmp'),
(1001, 'newEmp'),
(1002, 'newEmp'),
(1003, 'samePosNewRole'),
(1004, 'newEmp'),
(1005, 'newEmp'),
(1006, 'newEmp'),
(1007, 'newEmp'),
(1008, 'newEmp');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `roleID` int(11) NOT NULL,
  `systemID` int(11) DEFAULT NULL,
  `rolesNames` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`roleID`, `systemID`, `rolesNames`) VALUES
(1000, 1000, 'admin'),
(1001, 1000, 'admin'),
(1002, 1001, 'admin'),
(1004, 1000, 'admin'),
(1005, 1001, 'admin'),
(1006, 1002, 'admin'),
(1007, 1002, 'supervisor'),
(1008, 1003, 'admin'),
(1009, 1004, 'admin'),
(1010, 1005, 'admin'),
(1011, 1006, 'admin'),
(1012, 1007, 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `schoolanddepartment`
--

CREATE TABLE `schoolanddepartment` (
  `schoolDeptID` int(11) NOT NULL,
  `schoolName` tinytext,
  `debtName` tinytext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `schoolanddepartment`
--

INSERT INTO `schoolanddepartment` (`schoolDeptID`, `schoolName`, `debtName`) VALUES
(1000, 'SASN', 'AAAS'),
(1001, 'SASN', 'AAAS'),
(1002, 'SASN', 'AAAS'),
(1003, 'SASN', 'AAAS'),
(1004, 'SASN', 'AAAS'),
(1005, 'SASN', 'AAAS'),
(1006, 'SASN', 'AAAS'),
(1007, 'SASN', 'AAAS'),
(1008, 'SASN', 'AAAS');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
  `netID` varchar(45) DEFAULT NULL,
  `firstName` tinytext,
  `lastName` tinytext,
  `email` tinytext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `netID`, `firstName`, `lastName`, `email`) VALUES
(1000, 'sl123', 'Sam', 'Lee', 'sl@email.com'),
(1001, 'sm123', 'Sheriza', 'Mohamed', 'sm123@email.com'),
(1002, 'bsb120', 'Brandon', 'Bautista', 'bsb@email.com'),
(1003, 'sl123', 'Sam', 'Lee', 'sl@email.com'),
(1004, 'wa123', 'William', 'Arevalo', 'wa@email'),
(1005, 'ln123', 'Laura', 'Negrin', 'ln@email'),
(1006, 'sl123', 'Sam', 'Lee', 'sl@email.com'),
(1007, 'sl123', 'Sam', 'Lee', 'sl@email.com'),
(1008, 'sl123', 'John', 'Lee', 'sl@email.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `access`
--
ALTER TABLE `access`
  ADD PRIMARY KEY (`accessID`),
  ADD KEY `fk_access_roles` (`roleID`),
  ADD KEY `fk_userID` (`userID`);

--
-- Indexes for table `application`
--
ALTER TABLE `application`
  ADD PRIMARY KEY (`systemID`);

--
-- Indexes for table `employeetypes`
--
ALTER TABLE `employeetypes`
  ADD PRIMARY KEY (`empTypeID`);

--
-- Indexes for table `forms`
--
ALTER TABLE `forms`
  ADD PRIMARY KEY (`formID`),
  ADD KEY `fk_forms_request` (`requestID`),
  ADD KEY `fk_forms_roles` (`roleID`),
  ADD KEY `fk_forms_systemID` (`systemID`),
  ADD KEY `fk_forms_userEmployID` (`userEmployeeID`),
  ADD KEY `fk_schoolDeptId` (`schoolDeptID`),
  ADD KEY `fk_submitterUserID` (`submitterUserID`);

--
-- Indexes for table `formupdatelog`
--
ALTER TABLE `formupdatelog`
  ADD PRIMARY KEY (`updateID`),
  ADD KEY `fk_userID_formupdatelog` (`userID`),
  ADD KEY `fk_formID_formupdatelog` (`formID`),
  ADD KEY `fk_updateTypeID` (`updateTypeID`);

--
-- Indexes for table `formupdatelogtypes`
--
ALTER TABLE `formupdatelogtypes`
  ADD PRIMARY KEY (`updateTypeID`);

--
-- Indexes for table `requesttypes`
--
ALTER TABLE `requesttypes`
  ADD PRIMARY KEY (`requestID`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`roleID`),
  ADD KEY `fk_roles_systemID` (`systemID`);

--
-- Indexes for table `schoolanddepartment`
--
ALTER TABLE `schoolanddepartment`
  ADD PRIMARY KEY (`schoolDeptID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `access`
--
ALTER TABLE `access`
  MODIFY `accessID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1009;

--
-- AUTO_INCREMENT for table `application`
--
ALTER TABLE `application`
  MODIFY `systemID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1009;

--
-- AUTO_INCREMENT for table `employeetypes`
--
ALTER TABLE `employeetypes`
  MODIFY `empTypeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1008;

--
-- AUTO_INCREMENT for table `forms`
--
ALTER TABLE `forms`
  MODIFY `formID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1009;

--
-- AUTO_INCREMENT for table `formupdatelog`
--
ALTER TABLE `formupdatelog`
  MODIFY `updateID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `formupdatelogtypes`
--
ALTER TABLE `formupdatelogtypes`
  MODIFY `updateTypeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1009;

--
-- AUTO_INCREMENT for table `requesttypes`
--
ALTER TABLE `requesttypes`
  MODIFY `requestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1009;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `roleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1013;

--
-- AUTO_INCREMENT for table `schoolanddepartment`
--
ALTER TABLE `schoolanddepartment`
  MODIFY `schoolDeptID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1009;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1009;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `access`
--
ALTER TABLE `access`
  ADD CONSTRAINT `fk_access_roles` FOREIGN KEY (`roleID`) REFERENCES `Roles` (`roleID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_userID` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON UPDATE CASCADE;

--
-- Constraints for table `forms`
--
ALTER TABLE `forms`
  ADD CONSTRAINT `fk_forms_request` FOREIGN KEY (`requestID`) REFERENCES `requesttypes` (`requestID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_forms_roles` FOREIGN KEY (`roleID`) REFERENCES `roles` (`roleID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_forms_systemID` FOREIGN KEY (`systemID`) REFERENCES `application` (`systemID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_forms_userEmployID` FOREIGN KEY (`userEmployeeID`) REFERENCES `employeetypes` (`empTypeID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_schoolDeptId` FOREIGN KEY (`schoolDeptID`) REFERENCES `schoolAndDepartment` (`schoolDeptID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_submitterUserID` FOREIGN KEY (`submitterUserID`) REFERENCES `users` (`userID`) ON UPDATE CASCADE;

--
-- Constraints for table `formupdatelog`
--
ALTER TABLE `formupdatelog`
  ADD CONSTRAINT `fk_formID_formupdatelog` FOREIGN KEY (`formID`) REFERENCES `forms` (`formID`),
  ADD CONSTRAINT `fk_updateTypeID` FOREIGN KEY (`updateTypeID`) REFERENCES `formupdatelogtypes` (`updateTypeID`),
  ADD CONSTRAINT `fk_userID_formupdatelog` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`);

--
-- Constraints for table `roles`
--
ALTER TABLE `roles`
  ADD CONSTRAINT `fk_roles_systemID` FOREIGN KEY (`systemID`) REFERENCES `application` (`systemID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
