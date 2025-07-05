-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Sob 05. čec 2025, 12:34
-- Verze serveru: 10.4.32-MariaDB
-- Verze PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `escape_game`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `location_lat` decimal(10,8) DEFAULT NULL,
  `location_lng` decimal(11,8) DEFAULT NULL,
  `question_text` text NOT NULL,
  `correct_answer` varchar(255) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `questions`
--

INSERT INTO `questions` (`id`, `location_lat`, `location_lng`, `question_text`, `correct_answer`, `image_path`) VALUES
(1, 50.09297860, 14.40108390, 'Jak se jmenoval Herkulův parťák?', 'Hefaistos', 'assets/otazky/prague_castle.jpg'),
(2, 50.09189970, 14.40337440, 'Kolik poschodí má Bílá věž?', '5', NULL),
(3, 50.08992080, 14.40653140, 'Jsou zde původní sochy u kašny s Venuší a Amorem?', 'ne', NULL),
(4, 50.09007200, 14.39852500, 'Jak se jmenuje renesanční letohrádek v Královské zahradě Pražského hradu?', 'Letohrádek královny Anny', NULL),
(5, 50.09078500, 14.39881300, 'Který český král nechal postavit Prašný most vedoucí k Pražskému hradu?', 'Vladislav II.', NULL),
(6, 50.09396400, 14.41743600, 'Jak se jmenuje bývalý monument, na jehož místě dnes stojí Metronom na Letné?', 'Stalinův pomník', NULL),
(7, 50.09651200, 14.41419800, 'Jak se jmenuje historická budova s výstavními sály v parku Letná?', 'Národní technické muzeum', NULL),
(8, 50.09626700, 14.41678200, 'Jaký druh sportovní plochy se nachází v parku Letná u Hanavského pavilonu?', 'Hokejbalové hřiště', NULL),
(9, 50.09685900, 14.41312500, 'Jak se jmenuje známý pavilon na Letné s výhledem na Vltavu?', 'Hanavský pavilon', NULL),
(10, 50.08390890, 14.39904830, 'Jak vysoký je tento strom (m)?', '24', NULL),
(11, 50.08347390, 14.39626310, 'Datum založení zrcadlového bludiště?', '1891', NULL),
(12, 50.08297720, 14.40222610, 'Jméno sochaře co tuto sochu vytvořil?', 'Jan Simota', NULL),
(13, 50.08827610, 14.40246500, 'Jaký rok byl sloup dokončen?', '1715', NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`) VALUES
(4, 'root', '$2y$10$aD/geai90HwzH81nwrFZh.Zghh6GEp74kGhj8VMaE4Qvsh8EZCufu', '2025-06-18 16:41:13');

-- --------------------------------------------------------

--
-- Struktura tabulky `user_progress`
--

CREATE TABLE `user_progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `answered_correctly` tinyint(1) DEFAULT 0,
  `completion_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexy pro exportované tabulky
--

--
-- Indexy pro tabulku `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pro tabulku `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexy pro tabulku `user_progress`
--
ALTER TABLE `user_progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pro tabulku `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pro tabulku `user_progress`
--
ALTER TABLE `user_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `user_progress`
--
ALTER TABLE `user_progress`
  ADD CONSTRAINT `user_progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
