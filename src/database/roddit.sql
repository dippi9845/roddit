-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Dic 07, 2022 alle 16:57
-- Versione del server: 10.4.20-MariaDB
-- Versione PHP: 7.3.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `roddit`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `comments`
--

CREATE TABLE `comments` (
  `User` varchar(256) NOT NULL,
  `Text` varchar(1000) NOT NULL,
  `Post` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `cookies`
--

CREATE TABLE `cookies` (
  `UserID` int(10) UNSIGNED NOT NULL,
  `Token` varchar(255) NOT NULL,
  `ExpireDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `follow`
--

CREATE TABLE `follow` (
  `Follower` int(10) UNSIGNED NOT NULL,
  `Following` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `likes`
--

CREATE TABLE `likes` (
  `User` int(10) UNSIGNED NOT NULL,
  `Post` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `post`
--

CREATE TABLE `post` (
  `ID` bigint(20) UNSIGNED NOT NULL,
  `Creator` varchar(256) NOT NULL,
  `Text` varchar(10000) NOT NULL,
  `Title` varchar(500) NOT NULL,
  `Likes` int(11) NOT NULL DEFAULT 0,
  `Comments` int(11) NOT NULL DEFAULT 0,
  `Media` longblob DEFAULT NULL,
  `MediaType` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

CREATE TABLE `users` (
  `ID` int(10) UNSIGNED NOT NULL,
  `Nickname` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Salt` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `comments`
--
ALTER TABLE `comments`
  ADD KEY `User` (`User`),
  ADD KEY `Post` (`Post`);
ALTER TABLE `comments` ADD FULLTEXT KEY `Text` (`Text`);

--
-- Indici per le tabelle `cookies`
--
ALTER TABLE `cookies`
  ADD PRIMARY KEY (`Token`);

--
-- Indici per le tabelle `follow`
--
ALTER TABLE `follow`
  ADD UNIQUE KEY `Follower` (`Follower`,`Following`);

--
-- Indici per le tabelle `likes`
--
ALTER TABLE `likes`
  ADD UNIQUE KEY `User` (`User`,`Post`);

--
-- Indici per le tabelle `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Creator` (`Creator`);
ALTER TABLE `post` ADD FULLTEXT KEY `Text` (`Text`,`Title`);

--
-- Indici per le tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Nickname` (`Nickname`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `post`
--
ALTER TABLE `post`
  MODIFY `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

DELIMITER $$
--
-- Eventi
--
CREATE DEFINER=`root`@`localhost` EVENT `Cleaning` ON SCHEDULE EVERY 1 DAY STARTS '2022-12-07 16:54:33' ON COMPLETION NOT PRESERVE ENABLE DO DELETE FROM cookies WHERE cookies.ExpireDate < CURRENT_DATE()$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
