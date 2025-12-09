-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 04, 2025 at 07:24 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `projet_web`
--

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `user_id` int NOT NULL,
  `game_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`user_id`, `game_id`) VALUES
(1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `friendships`
--

CREATE TABLE `friendships` (
  `id` int NOT NULL,
  `requester_id` int NOT NULL,
  `requested_id` int NOT NULL,
  `status` enum('pending','accepted','declined') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `friendships`
--

INSERT INTO `friendships` (`id`, `requester_id`, `requested_id`, `status`, `created_at`, `updated_at`) VALUES
(2, 1, 2, 'accepted', '2025-11-14 13:28:29', '2025-11-14 13:28:50');

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `id` int NOT NULL,
  `title` varchar(100) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `cover_path` varchar(255) NOT NULL,
  `console` varchar(50) NOT NULL DEFAULT 'nds',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`id`, `title`, `file_path`, `cover_path`, `console`, `created_at`) VALUES
(1, 'Super Mario Bros', '/roms/nds/MarioBros.nds', '/roms/cover/SuperMario.png', 'nds', '2025-11-01 13:36:51'),
(2, 'Pokémon Noir', '/roms/nds/PokemonNoir.nds', '/roms/cover/PokemonNoir.png', 'nds', '2025-11-01 13:36:51'),
(3, 'Inazuma Eleven 2', '/roms/nds/InazumaEleven2.nds', '/roms/cover/InazumaEleven2.png', 'nds', '2025-11-13 13:03:37'),
(4, 'Mario Kart DS', '/roms/nds/MarioKartDS.nds', '/roms/cover/MarioKartDS.png', 'nds', '2025-11-13 13:09:37'),
(5, 'Professeur Layton et la boîte de Pandore', '/roms/nds/ProfLay.nds', '/roms/cover/ProfLay.png', 'nds', '2025-11-13 13:19:36'),
(6, 'Pokemon Donjon Mystère : Explorateurs du Temps', '/roms/nds/PokemonDonjonMystere.nds', '/roms/cover/PokemonDonjonMystere.png', 'nds', '2025-11-13 13:33:09'),
(7, 'Legend of Zelda The - Phantom Hourglass', '/roms/nds/ZeldaPhantom.nds', '/roms/cover/ZeldaPhantom.png', 'nds', '2025-11-13 13:44:15'),
(8, 'Super Mario 64 DS', '/roms/nds/SuperMario64.nds', '/roms/cover/SuperMario64.png', 'nds', '2025-11-13 13:49:48'),
(9, 'Pokemon SoulSilver', '/roms/nds/PokemonSS.nds', '/roms/cover/PokemonSS.png', 'nds', '2025-11-13 14:19:42'),
(10, 'Mario et Luigi : Au centre de Bowser', '/roms/nds/MarioetLuigicentrebowser.nds', '/roms/cover/MarioetLuigicentrebowser.png', 'nds', '2025-11-13 14:26:36'),
(11, 'Pokemon Platine', '/roms/nds/PokemonPlatine.nds', '/roms/cover/PokemonPlatine.png', 'nds', '2025-11-13 14:34:15'),
(12, 'Metroid Prime: Hunters', '/roms/nds/MetroidPrime.nds', '/roms/cover/MetroidPrime.png', 'nds', '2025-11-13 14:41:23'),
(13, 'Sonic Colors', '/roms/nds/SonicColors.nds', '/roms/cover/SonicColors.png', 'nds', '2025-11-13 14:49:13'),
(14, 'Chrono Trigger', '/roms/nds/ChronoTrigger.nds', '/roms/cover/ChronoTrigger.png', 'nds', '2025-11-13 14:53:15'),
(15, 'Yoshi\'s Island DS', '/roms/nds/Yoshi.nds', '/roms/cover/Yoshi.png', 'nds', '2025-11-13 15:02:28');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int NOT NULL,
  `sender_id` int NOT NULL,
  `receiver_id` int NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `created_at`) VALUES
(1, 1, 2, 'cc', '2025-11-14 14:06:01'),
(2, 1, 2, 'cc', '2025-11-14 14:15:40'),
(3, 1, 2, 'e', '2025-11-14 14:15:46'),
(4, 1, 2, 'cc', '2025-11-14 14:21:00'),
(5, 2, 1, 'cc', '2025-11-25 08:29:13'),
(6, 2, 1, 'o', '2025-11-25 08:34:39'),
(7, 2, 1, 'cc', '2025-11-25 08:38:43'),
(8, 2, 1, 'cc', '2025-11-25 09:09:46'),
(9, 2, 1, 'cc', '2025-11-25 10:29:15'),
(10, 2, 1, 'salut', '2025-11-25 11:19:43'),
(11, 2, 1, 'fzeoifrezughziuhg', '2025-11-25 11:20:10'),
(12, 2, 1, 'll', '2025-12-04 19:53:46');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_admin`, `created_at`) VALUES
(1, 'ADMIN', 'admin@arcadia.local', '$2y$10$IhZw6.gZvfpjoOon8QHaFeBJblf3PCum8weJh83DkxHv1DkI4fysa', 1, '2025-10-23 16:36:06'),
(2, 'Zamdane', 'hfachtali24@gmail.com', '$2y$10$IqCd/KMli9D/b3SgJTN1v.JYXCPtPgaSLwm33Z6H8CoaOVwU2pl5S', 0, '2025-10-24 14:25:10'),
(3, 'Jade', 'Jade@gmail.com', '$2y$10$f13GP5oP562Lg7tpo0mQnOjOHJ5GwY7KkOXPP7lwh5X8vKtXOxxQC', 0, '2025-11-07 12:46:05');

-- --------------------------------------------------------

--
-- Table structure for table `user_game_times`
--

CREATE TABLE `user_game_times` (
  `user_id` int NOT NULL,
  `game_id` int NOT NULL,
  `total_seconds` int UNSIGNED NOT NULL DEFAULT '0',
  `last_played_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_game_times`
--

INSERT INTO `user_game_times` (`user_id`, `game_id`, `total_seconds`, `last_played_at`) VALUES
(1, 11, 24, '2025-11-14 15:59:38'),
(2, 1, 26, '2025-11-24 10:42:33'),
(2, 2, 36, '2025-12-04 19:53:21'),
(2, 3, 997, '2025-11-27 14:55:36'),
(2, 8, 10, '2025-11-27 15:31:16'),
(2, 9, 40, '2025-11-14 13:31:52'),
(2, 10, 10, '2025-11-25 11:19:25'),
(2, 13, 82, '2025-11-27 15:30:22'),
(2, 14, 117, '2025-12-04 19:52:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`user_id`,`game_id`);

--
-- Indexes for table `friendships`
--
ALTER TABLE `friendships`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_friendship_pair` (`requester_id`,`requested_id`),
  ADD KEY `fk_friendships_requested` (`requested_id`);

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_game_times`
--
ALTER TABLE `user_game_times`
  ADD PRIMARY KEY (`user_id`,`game_id`),
  ADD KEY `fk_ugt_game` (`game_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `friendships`
--
ALTER TABLE `friendships`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `friendships`
--
ALTER TABLE `friendships`
  ADD CONSTRAINT `fk_friendships_requested` FOREIGN KEY (`requested_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_friendships_requester` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_game_times`
--
ALTER TABLE `user_game_times`
  ADD CONSTRAINT `fk_ugt_game` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ugt_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
