-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 10. Okt 2015 um 17:00
-- Server Version: 5.6.20-log
-- PHP-Version: 5.5.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `message_svc`
--
CREATE USER 'message_svc'@'localhost' IDENTIFIED BY 'DXmbfucUSVcUCv8X';
GRANT USAGE ON *.* TO 'message_svc'@'localhost' IDENTIFIED BY 'DXmbfucUSVcUCv8X' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;
CREATE DATABASE IF NOT EXISTS `message_svc`;
GRANT ALL PRIVILEGES ON `message_svc`.* TO 'message_svc'@'localhost';

USE `message_svc`;




-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `message`
--

CREATE TABLE IF NOT EXISTS `message` (
`entryId` int(11) NOT NULL,
  `messageId` varchar(64) DEFAULT NULL,
  `statusCd` int(11) DEFAULT NULL,
  `errorText` varchar(512) DEFAULT NULL,
  `channel` varchar(64) NOT NULL,
  `provider` varchar(64) NOT NULL,
  `subject` varchar(512) NOT NULL,
  `sender` varchar(512) NOT NULL,
  `recipient` varchar(512) NOT NULL,
  `body` varchar(4096) NOT NULL,
  `bodyTemplate` varchar(4096) DEFAULT NULL,
  `bodyData` varchar(4096) DEFAULT NULL,
  `createDttm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` varchar(256) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


--
-- Indexes for table `message`
--
ALTER TABLE `message`
 ADD PRIMARY KEY (`entryId`);

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
MODIFY `entryId` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;

