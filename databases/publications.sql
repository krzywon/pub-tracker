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
-- Database: `publications`
--

-- --------------------------------------------------------

--
-- Table structure for table `authorpublink`
--

CREATE TABLE `publication_author` (
  `publication_id` int(4) NOT NULL,
  `author_number` int(3) NOT NULL,
  `author_id` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `authors`
--

CREATE TABLE `authors` (
  `firstname` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `middlename` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `author_id` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `journalinformation`
--

CREATE TABLE `journalinformation` (
  `index` int(3) NOT NULL,
  `journalName` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `journalAbbreviation` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sampleenvironment`
--

CREATE TABLE `sampleenvironment` (
  `pub_id` int(4) NOT NULL,
  `closed_cycle_refrigerator` tinyint(1) NOT NULL DEFAULT '0',
  `electromagnet` tinyint(1) NOT NULL DEFAULT '0',
  `humidity_cell` tinyint(1) NOT NULL DEFAULT '0',
  `other` tinyint(1) NOT NULL DEFAULT '0',
  `polarization` tinyint(1) NOT NULL DEFAULT '0',
  `rheometer` tinyint(1) NOT NULL DEFAULT '0',
  `sample_changer` tinyint(1) NOT NULL DEFAULT '0',
  `shear_cell_12plane` tinyint(1) NOT NULL DEFAULT '0',
  `shear_cell_boulder` tinyint(1) NOT NULL DEFAULT '0',
  `shear_cell_plateplate` tinyint(1) NOT NULL DEFAULT '0',
  `superconducting_magnet` tinyint(1) NOT NULL DEFAULT '0',
  `user_equipment` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sanspublications`
--

CREATE TABLE `sanspublications` (
  `pub_id` int(4) NOT NULL,
  `year` int(4) NOT NULL DEFAULT '2008',
  `volume` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `issue` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `firstpage` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `journal` int(3) NOT NULL,
  `numberOfAuthors` int(11) NOT NULL,
  `nistauthor` tinyint(1) NOT NULL DEFAULT '0',
  `pdf` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `ng1sans` tinyint(1) NOT NULL DEFAULT '0',
  `ngb10msans` tinyint(1) NOT NULL DEFAULT '0',
  `ngb30msans` tinyint(1) NOT NULL DEFAULT '0',
  `ng3sans` tinyint(1) NOT NULL DEFAULT '0',
  `ng7sans` tinyint(1) NOT NULL DEFAULT '0',
  `bt5usans` tinyint(1) NOT NULL DEFAULT '0',
  `igor` tinyint(1) NOT NULL DEFAULT '0',
  `doi` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`author_id`);

--
-- Indexes for table `journalinformation`
--
ALTER TABLE `journalinformation`
  ADD PRIMARY KEY (`index`),
  ADD UNIQUE KEY `Pub_name` (`journalName`,`journalAbbreviation`);

--
-- Indexes for table `sanspublications`
--
ALTER TABLE `sanspublications`
  ADD PRIMARY KEY (`pub_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `authors`
--
ALTER TABLE `authors`
  MODIFY `author_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3090;
--
-- AUTO_INCREMENT for table `journalinformation`
--
ALTER TABLE `journalinformation`
  MODIFY `index` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=222;
--
-- AUTO_INCREMENT for table `sanspublications`
--
ALTER TABLE `sanspublications`
  MODIFY `pub_id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1370;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
