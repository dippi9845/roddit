-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Gen 03, 2023 alle 16:23
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
-- Struttura della tabella `comment`
--

CREATE TABLE `comment` (
  `ID` bigint(20) UNSIGNED NOT NULL,
  `User` varchar(256) NOT NULL,
  `Text` varchar(1000) NOT NULL,
  `entityType` enum('Post','Commento') NOT NULL,
  `entityID` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `cookies`
--

CREATE TABLE `cookies` (
  `UserID` int(10) UNSIGNED NOT NULL,
  `Token` varchar(255) NOT NULL,
  `ExpireDate` date NOT NULL,
  `HashToken` varchar(256) NOT NULL
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
-- Struttura della tabella `notification`
--

CREATE TABLE `notification` (
  `UserID` bigint(20) UNSIGNED NOT NULL,
  `Title` varchar(256) NOT NULL,
  `Message` varchar(256) NOT NULL,
  `Inserimento` date NOT NULL DEFAULT current_timestamp()
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
  `PathToFile` varchar(500) DEFAULT NULL,
  `MediaType` enum('Imamgine','Video') DEFAULT NULL
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
  `Salt` varchar(255) NOT NULL,
  `ProfileImagePath` varchar(256) NOT NULL DEFAULT '/uploads/images/default_profile_picture.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `users`
--

INSERT INTO `users` (`ID`, `Nickname`, `Email`, `Password`, `Salt`, `ProfileImagePath`) VALUES
(6, 'Gianni', 'giannimorandi@sonoio.com', '$2y$10$swX0OetT5eHOjzvXiIZHROrlKmmIwAT9h1pXPDnU/1GUT5uFNtHWe', '63b0664cf1aa1', '/uploads/images/default_profile_picture.jpg');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `ricerca` (`entityID`,`entityType`),
  ADD KEY `User` (`User`);
ALTER TABLE `comment` ADD FULLTEXT KEY `Text` (`Text`);

--
-- Indici per le tabelle `cookies`
--
ALTER TABLE `cookies`
  ADD PRIMARY KEY (`Token`),
  ADD UNIQUE KEY `HashToken` (`HashToken`),
  ADD KEY `UserID` (`UserID`);

--
-- Indici per le tabelle `follow`
--
ALTER TABLE `follow`
  ADD UNIQUE KEY `Follower` (`Follower`,`Following`),
  ADD KEY `Following` (`Following`);

--
-- Indici per le tabelle `likes`
--
ALTER TABLE `likes`
  ADD UNIQUE KEY `User` (`User`,`Post`),
  ADD KEY `Post` (`Post`);

--
-- Indici per le tabelle `notification`
--
ALTER TABLE `notification`
  ADD KEY `UserID` (`UserID`,`Inserimento`) USING BTREE;

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
-- AUTO_INCREMENT per la tabella `comment`
--
ALTER TABLE `comment`
  MODIFY `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `post`
--
ALTER TABLE `post`
  MODIFY `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`User`) REFERENCES `users` (`Nickname`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `cookies`
--
ALTER TABLE `cookies`
  ADD CONSTRAINT `cookies_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `follow`
--
ALTER TABLE `follow`
  ADD CONSTRAINT `follow_ibfk_1` FOREIGN KEY (`Follower`) REFERENCES `users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `follow_ibfk_2` FOREIGN KEY (`Following`) REFERENCES `users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`Post`) REFERENCES `post` (`ID`),
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`User`) REFERENCES `users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

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
