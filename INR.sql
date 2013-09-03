-- phpMyAdmin SQL Dump
-- version 3.5.8.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 04, 2013 at 02:01 AM
-- Server version: 5.5.32-0ubuntu0.13.04.1
-- PHP Version: 5.4.9-4ubuntu2.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `INR`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `getPendingQueries`(IN `train_no` VARCHAR(10))
    READS SQL DATA
    SQL SECURITY INVOKER
select q.TrainNo, TravelDate, LookupDate, Class, 
SourceCode, DestinationCode 
from PendingQueries as q 
inner join TrainInfo as t on q.TrainNo = t.TrainNo
where q.TrainNo = train_no and status = 'New'$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `AvailabilityInfo`
--

CREATE TABLE IF NOT EXISTS `AvailabilityInfo` (
  `RecordNo` bigint(20) NOT NULL AUTO_INCREMENT,
  `TrainNo` varchar(10) NOT NULL,
  `TravelDate` date NOT NULL,
  `LookupDate` date NOT NULL,
  `Class` varchar(5) NOT NULL,
  `Availability` varchar(20) NOT NULL,
  `GrossAvType` varchar(5) NOT NULL,
  `GrossAvCount` int(11) NOT NULL,
  `NetAvType` varchar(5) NOT NULL,
  `NetAvCount` int(11) NOT NULL,
  `Bookings` int(11) NOT NULL,
  `Cancellations` int(11) NOT NULL,
  `ChangedOn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecordNo`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=121 ;

-- --------------------------------------------------------

--
-- Table structure for table `PendingQueries`
--

CREATE TABLE IF NOT EXISTS `PendingQueries` (
  `TrainNo` varchar(10) NOT NULL,
  `TravelDate` date NOT NULL,
  `LookupDate` date NOT NULL,
  `Class` varchar(5) NOT NULL,
  `Status` varchar(10) NOT NULL,
  `ChangedOn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`TrainNo`,`TravelDate`,`LookupDate`,`Class`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `TrainInfo`
--

CREATE TABLE IF NOT EXISTS `TrainInfo` (
  `TrainNo` varchar(10) NOT NULL,
  `SourceCode` varchar(5) NOT NULL,
  `DestinationCode` varchar(5) NOT NULL,
  `RunsOn` varchar(20) NOT NULL,
  `ChangedOn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`TrainNo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `TrainQuota`
--

CREATE TABLE IF NOT EXISTS `TrainQuota` (
  `TrainNo` varchar(10) NOT NULL,
  `Class` varchar(5) NOT NULL,
  `RACQuota` int(11) NOT NULL,
  `ChangedOn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`TrainNo`,`Class`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
