-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Dec 15, 2023 at 05:54 PM
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

-- --------------------------------------------------------

--
-- Table structure for table `application`
--

CREATE TABLE `application` (
  `systemName` text,
  `system` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `application`
--

INSERT INTO `application` (`systemName`, `system`) VALUES
('START', 1),
('GradTracker', 2),
('JTracker', 3),
('AMS', 4),
('FacultyIntegration', 5),
('SASNWebsite', 6),
('Kronos', 7);

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
(1000, 'staff');

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

INSERT INTO `forms` (`formID`, `userFirstName`, `userLastName`, `userEmail`, `entryDate`, `effDate`, `userNetID`, `schoolDeptID`, `userJustification`, `submitterUserID`, `requestID`, `userEmployeeID`, `action`) VALUES
(1000, 'Brandon', 'Bautista', 'bsb@email.com', '2023-12-15 00:00:00', '2023-12-16', 'bsb120', 1002, 'hello', 1000, 1000, 1000, 0);

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
(1000, 'submit');

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
(1000, 'newEmp');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `roleID` int(11) NOT NULL,
  `formID` int(11) DEFAULT NULL,
  `system` int(1) DEFAULT NULL,
  `rolesNames` text,
  `justification` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`roleID`, `formID`, `system`, `rolesNames`, `justification`) VALUES
(1004, 1000, 1, 'admin', 'hello'),
(1005, 1000, 2, 'gt_certifier', 'hi');

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
(1002, 'SASN', 'AAAS');

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
(1000, 'bsb120', 'Brandon', 'Bautista', 'bsb@email.com');

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
  ADD KEY `fk_roles_forms` (`formID`);

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
  MODIFY `accessID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employeetypes`
--
ALTER TABLE `employeetypes`
  MODIFY `empTypeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1001;

--
-- AUTO_INCREMENT for table `forms`
--
ALTER TABLE `forms`
  MODIFY `formID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1001;

--
-- AUTO_INCREMENT for table `formupdatelog`
--
ALTER TABLE `formupdatelog`
  MODIFY `updateID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `formupdatelogtypes`
--
ALTER TABLE `formupdatelogtypes`
  MODIFY `updateTypeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1001;

--
-- AUTO_INCREMENT for table `requesttypes`
--
ALTER TABLE `requesttypes`
  MODIFY `requestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1001;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `roleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1006;

--
-- AUTO_INCREMENT for table `schoolanddepartment`
--
ALTER TABLE `schoolanddepartment`
  MODIFY `schoolDeptID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1003;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1001;

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
-- Constraints for table `supervisorInfo`
--
-- ALTER TABLE `supervisorInfo`
--   ADD CONSTRAINT `fk_supervisorInfo_forms` FOREIGN KEY (`formID`) REFERENCES `forms` (`formID`);

--
-- Constraints for table `roles`
--
ALTER TABLE `roles`
  ADD CONSTRAINT `fk_roles_forms` FOREIGN KEY (`formID`) REFERENCES `forms` (`formID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

