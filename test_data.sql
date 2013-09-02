-- phpMyAdmin SQL Dump
-- version 3.5.8.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 03, 2013 at 01:39 AM
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

--
-- Dumping data for table `TrainInfo`
--

INSERT INTO `TrainInfo` (`TrainNo`, `SourceCode`, `DestinationCode`, `RunsOn`, `ChangedOn`) VALUES
('12627', 'SBC', 'NDLS', '1,2,3,4,5,6,7', '2013-09-02 19:15:19');

--
-- Dumping data for table `TrainQuota`
--

INSERT INTO `TrainQuota` (`TrainNo`, `Class`, `RACQuota`, `ChangedOn`) VALUES
('12627', '3A', 12, '2013-09-02 19:15:47'),
('12627', 'SL', 60, '2013-09-02 19:15:47');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
