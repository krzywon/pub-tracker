-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 02, 2017 at 08:57 PM
-- Server version: 10.1.21-MariaDB
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `publications_new`
--

-- --------------------------------------------------------

--
-- Table structure for table `authorpublink`
--

CREATE TABLE `authorpublink` (
  `authorIndex` int(11) NOT NULL,
  `authorNumber` int(11) NOT NULL,
  `publicationIndex` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `authors`
--

CREATE TABLE `authors` (
  `author_id` int(11) NOT NULL,
  `lastname` text NOT NULL,
  `firstname` text NOT NULL,
  `middlename` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `journalinformation`
--

CREATE TABLE `journalinformation` (
  `id` int(11) NOT NULL,
  `journalName` int(11) NOT NULL,
  `journalAbbreviation` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sanspublications`
--

CREATE TABLE `sanspublications` (
  `pub_id` int(11) NOT NULL,
  `year` text NOT NULL,
  `volume` text NOT NULL,
  `issue` text NOT NULL,
  `firstpage` text NOT NULL,
  `journal` int(11) NOT NULL,
  `numberOfAuthors` int(11) NOT NULL,
  `title` text NOT NULL,
  `nistauthor` int(11) NOT NULL,
  `pdf` int(11) NOT NULL,
  `ng1sans` int(11) NOT NULL,
  `ngb10msans` int(11) NOT NULL,
  `ngb30msans` int(11) NOT NULL,
  `ng3sans` int(11) NOT NULL,
  `ng7sans` int(11) NOT NULL,
  `bt5usans` int(11) NOT NULL,
  `igor` int(11) NOT NULL,
  `doi` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `authors`
--
ALTER TABLE `authors`
  ADD UNIQUE KEY `id` (`author_id`);

--
-- Indexes for table `journalinformation`
--
ALTER TABLE `journalinformation`
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `sanspublications`
--
ALTER TABLE `sanspublications`
  ADD UNIQUE KEY `pub_id` (`pub_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
