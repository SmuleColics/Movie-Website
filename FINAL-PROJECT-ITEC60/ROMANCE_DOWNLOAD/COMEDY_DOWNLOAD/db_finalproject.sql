-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 30, 2025 at 04:52 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_finalproject`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_admin_acc`
--

CREATE TABLE `tbl_admin_acc` (
  `admin_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `department` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobile` varchar(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `facebook_acc` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `gender` varchar(100) NOT NULL,
  `marital_status` varchar(100) NOT NULL,
  `nationality` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_admin_acc`
--

INSERT INTO `tbl_admin_acc` (`admin_id`, `employee_id`, `department`, `email`, `mobile`, `first_name`, `middle_name`, `last_name`, `facebook_acc`, `password`, `date_created`, `gender`, `marital_status`, `nationality`) VALUES
(1, 1001, '', 'admin@gmail.com', '09123456789', 'John Lenard', 'Benitez', 'Colico', 'Lenard Colico', '$2y$10$/uRCO06T0SEJmxwBn2gwj.UtUpDbKz7gPrz/r7cy2qjdeQyVOVvQC', '2025-05-14 23:26:03', '', '', ''),
(3, 1002, 'Boss', 'adminjen@gmail.com', '09123781235', 'Jennylene', 'Bonayon', 'Amolong', 'Secret', '$2y$10$AGipFI9oUgUxVmbHRJllk.BugCk7vPNVZQ6z2stl8plF5i41VGQuC', '2025-05-20 22:30:24', 'Female', 'Single', 'Filipino'),
(4, NULL, '', 'adminjaz@gmail.com', '09375612385', 'Jaz', 'Balingit', 'Hawan', 'Jaz Hawan', '$2y$10$IU2q2BbR8usLhhKs/uTuqOX25fR1Y3yXWlL7uDdtXCa9xnQNajmB6', '2025-05-29 19:26:30', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_movie_series`
--

CREATE TABLE `tbl_movie_series` (
  `movie_series_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `poster` varchar(255) DEFAULT NULL,
  `video` varchar(255) DEFAULT NULL,
  `modal_poster_title` varchar(255) DEFAULT NULL,
  `date_released` int(11) DEFAULT NULL,
  `age_rating` int(11) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `cast` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `views` int(11) DEFAULT 0,
  `date_posted` datetime NOT NULL DEFAULT current_timestamp(),
  `genre_id1` int(11) NOT NULL,
  `genre_id2` int(11) DEFAULT NULL,
  `genre_id3` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_movie_series`
--

INSERT INTO `tbl_movie_series` (`movie_series_id`, `title`, `duration`, `poster`, `video`, `modal_poster_title`, `date_released`, `age_rating`, `category`, `cast`, `description`, `views`, `date_posted`, `genre_id1`, `genre_id2`, `genre_id3`) VALUES
(1, 'My Future You', '1hr 49min', 'MyFutureYouPoster.webp', 'MyFutureYouVideo.mp4', 'MyFutureYouTitle.webp', 2025, 7, 'Movie', 'Francine Diaz, Seth Fedelin, Almira Muhlach', 'Lex meets Karen on a dating app — but he\'s in 2009 and she\'s in 2024. Through the time gap, he helps change her past, while reshaping his future.', 55, '2025-05-28 21:18:28', 14, 22, 8),
(2, 'Hello, Love, Again', '2h 2min', 'HelloLoveAgain.webp', 'HelloLoveAgainVideo.mp4', 'ChancesAreYouAndITitle.jpg', 2024, 13, 'Movie', 'Kathryn Bernardo, Alden Richards, Joross Gamboa', 'Driven apart by COVID-19, two exes continue their lives on opposite sides of the world — until a surprise reunion offers them a second chance.', 51, '2025-05-28 21:25:36', 22, 7, 25),
(3, 'Stranger Things', '1 Season', 'StrangerThingsPoster.webp', 'StrangerThingsVideo.mp4', 'StrangerThingsTitle.webp', 2022, 16, 'Series', 'Winoma Ryder, David Harbour, Millie Bobby Brown, Johny Sins', 'A group of young friends in a small town uncover supernatural mysteries and government experiments while searching for a missing boy.', 48, '2025-05-28 21:37:16', 23, 15, 5),
(4, 'Scarecrow', '1h 30min', 'EspantahoPoster.jpeg', 'EspantahoVideo.mp4', 'EspantahoTitle.webp', 2024, 13, 'Movie', 'Judy Ann Santos, Lorna Tolentino, Chanda Romero', 'A remote village is haunted by the spirit of a vengeful scarecrow who awakens every harvest season.', 47, '2025-05-28 21:39:04', 11, 16, 13),
(5, '1992', '1h 37min', '1992Poster.webp', '1992Video.mp4', '1992Title.webp', 2022, 16, 'Movie', 'Tyrese Gibson, Ray Liotta, Scott Eastwood', 'A retired detective reopens a chilling cold case from 1992 that reveals long-buried secrets and a conspiracy.', 46, '2025-05-28 21:41:03', 26, 1, 2),
(6, 'Afraid', '1h 24m', 'AfraidPoster.jpg', '1992Video.mp4', 'AfraidTitle.webp', 2024, 13, 'Movie', 'John Cho, Katherine Waterston, Havana Rose Liu', 'A single mother must protect her daughter from a terrifying force hiding inside their new home.', 45, '2025-05-28 21:43:36', 13, 11, 16),
(7, 'Ex ex Lovers', '1h 45min', 'ExExLoverPoster.webp', 'MyFutureYouVideo.mp4', 'ExExLoverTitle.webp', 2025, 13, 'Movie', 'Jolina Magdangal, Marvin Agustin, Loisa Andalio', 'Two former lovers are unexpectedly reunited at a wedding, sparking chaos, laughter, and unresolved feelings.', 44, '2025-05-28 21:47:03', 22, 4, 8),
(8, 'Girl From Nowhere', '2 Seasons', 'GirlFromNowherePoster.jpg', 'GirlFromNowhereVideo.mp4', 'GirlFromNowhereTitle.jpg', 2021, 16, 'Series', 'Kitty Chicha Amatayakul, Chanya McClory, Thanavate Siriwattanagul', 'A mysterious girl transfers from school to school, exposing lies, bullying, and the dark side of teenage life.', 45, '2025-05-28 21:48:12', 15, 7, 13),
(9, 'Incognito', '128 Episodes', 'Incognito.webp', 'IncognitoVideo.mp4', 'IncognitoTitle.webp', 2025, 16, 'Series', 'Richard Gutierrez, Daniel  Padiila, Baron Geisler', 'An elite secret agent fakes his death to go undercover and infiltrate a criminal empire from within.', 43, '2025-05-28 22:05:53', 1, 5, 26),
(10, 'Squid Game', '1 Season', 'SquidGame2.webp', 'DemonCityVideo.mp4', 'SquidGameTitle.webp', 2022, 13, 'Series', 'Piolo Pascual, Jericho Rosales, John Lloyd Cruz.', 'Hundreds of contestants in debt are lured into deadly children\'s games for a chance at a life-changing prize.', 41, '2025-05-28 22:09:51', 13, 26, 5),
(11, 'Romance In The House', '1 Season', 'RomanceInTheHousePoster.jpg', 'MyFutureYouVideo.mp4', 'RomanceInTheHouseTitle.jpg', 2023, 7, 'Series', 'Coco Martin, Alden Richards, Carlo Aquino.', 'Three unlikely bachelors move into a house only to discover it\'s owned by a matchmaker with plans for their love lives.', 3, '2025-05-28 23:26:14', 22, 7, 15),
(14, 'Anyone But You', '1h 42min', 'AnyoneButYouPoster.jpg', 'AnyoneButYouVideo.mp4', 'AnyoneButYouTitle.jpg', 2023, 13, 'Movie', 'Sydney Sweeney, Glen Powell, Alexandra Shipp', 'At a lavish destination wedding, two singles whose one date ended badly pretend to be a couple to pacify her interfering parents and make his ex jealous.', 1, '2025-05-29 19:17:50', 22, 4, 24),
(15, 'Chances Are, You and I', '2h 12min', 'ChancesAreYouAndIPoster.jpg', 'ChancesAreYouAndIVideo.mp4', 'ChancesAreYouAndITitle.jpg', 2024, 7, 'Movie', 'Kevin Miranda, Keli Balinger, Bae Jin-ho', 'Diagnosed with dangerous brain tumors, two unlikely friends head to Korea to confront grief and uncertainty — and maybe take a shot at love.', 0, '2025-05-29 19:21:23', 22, 4, 18);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_movie_series_episodes`
--

CREATE TABLE `tbl_movie_series_episodes` (
  `episode_id` int(11) NOT NULL,
  `season_id` int(11) NOT NULL,
  `episode_title` varchar(255) NOT NULL,
  `episode_description` text DEFAULT NULL,
  `episode_duration` varchar(50) DEFAULT NULL,
  `episode_video` varchar(255) DEFAULT NULL,
  `views` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_movie_series_episodes`
--

INSERT INTO `tbl_movie_series_episodes` (`episode_id`, `season_id`, `episode_title`, `episode_description`, `episode_duration`, `episode_video`, `views`) VALUES
(8, 8, 'Hidden', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', '53min', 'MyFutureYouVideo.mp4', 0),
(9, 9, 'Chapter One: The Vanishing of Will Byers', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', '49min', 'StrangerThingsVideo.mp4', 0),
(10, 10, 'The Ugly Truth', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', '53min', 'GirlFromNowhereVideo.mp4', 0),
(11, 11, 'Pregnant', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', '46min', 'GirlFromNowhereVideo.mp4', 0),
(12, 12, 'Incognito 1', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', '43min', 'IncognitoVideo.mp4', 1),
(13, 13, 'Hidden', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', '42min', 'DemonCityVideo.mp4', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_movie_series_genre`
--

CREATE TABLE `tbl_movie_series_genre` (
  `genre_id` int(11) NOT NULL,
  `genre_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_movie_series_genre`
--

INSERT INTO `tbl_movie_series_genre` (`genre_id`, `genre_name`) VALUES
(1, 'Action'),
(2, 'Adventure'),
(3, 'Animation'),
(4, 'Comedy'),
(5, 'Crime'),
(6, 'Documentary'),
(7, 'Drama'),
(8, 'Family'),
(9, 'Fantasy'),
(10, 'History'),
(11, 'Terror'),
(12, 'Music'),
(13, 'Mystery'),
(14, 'Science fiction'),
(15, 'Cinema TV'),
(16, 'Thriller'),
(17, 'War'),
(18, 'Western'),
(20, 'News'),
(21, 'Reality'),
(22, 'Romance'),
(23, 'Sci-Fi & Fantasy'),
(24, 'Soap'),
(25, 'Talk'),
(26, 'War & Politics'),
(27, 'Kids');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_movie_series_seasons`
--

CREATE TABLE `tbl_movie_series_seasons` (
  `season_id` int(11) NOT NULL,
  `movie_series_id` int(11) NOT NULL,
  `season_number` int(11) NOT NULL,
  `season_title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_movie_series_seasons`
--

INSERT INTO `tbl_movie_series_seasons` (`season_id`, `movie_series_id`, `season_number`, `season_title`) VALUES
(8, 11, 1, 'Season 1'),
(9, 3, 1, 'Season 1'),
(10, 8, 1, 'Season 1'),
(11, 8, 2, 'Season 2'),
(12, 9, 1, 'Season 1'),
(13, 10, 1, 'Season 1');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_payment`
--

CREATE TABLE `tbl_payment` (
  `payment_id` int(11) NOT NULL,
  `signup_id` int(11) NOT NULL,
  `payment_amount` int(2) NOT NULL DEFAULT 99,
  `reference_no` varchar(13) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','denied') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_payment`
--

INSERT INTO `tbl_payment` (`payment_id`, `signup_id`, `payment_amount`, `reference_no`, `date_created`, `status`) VALUES
(2, 2, 99, '2222222222222', '2025-05-09 20:12:27', 'approved'),
(3, 3, 99, '3333333333333', '2025-05-09 20:38:12', 'approved'),
(5, 5, 99, '1234567890987', '2025-05-14 14:29:31', 'pending'),
(6, 6, 99, '6123617284127', '2025-05-14 14:30:55', 'denied'),
(7, 7, 99, '7862376128736', '2025-05-29 10:30:48', 'pending'),
(9, 9, 99, '1111111111111', '2025-05-30 12:10:00', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_signup_acc`
--

CREATE TABLE `tbl_signup_acc` (
  `signup_id` int(11) NOT NULL,
  `signup_email` varchar(100) NOT NULL,
  `signup_password` varchar(255) NOT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_signup_acc`
--

INSERT INTO `tbl_signup_acc` (`signup_id`, `signup_email`, `signup_password`, `is_archived`) VALUES
(2, 'jamesmacalintal@gmail.com', '$2y$10$gyfSeXPbgD0tMOfZxZ8O9uQTeHbu7qN/hYFvJecQz/GTy0UjD1SfO', 0),
(3, 'james123@gmail.com', '$2y$10$y.86SAEHX3TTap.YZdElvOQ08KBZwSCn/uIFIOdB7R5snjIajlExy', 0),
(5, 'jennylene@gmail.com', '$2y$10$Amxcc6lo8n/aDnklCJ0koOfS8axx//cioSShAG.0F.OJNHB7TQMMy', 0),
(6, 'jen@gmail.com', '$2y$10$DoI01yggoSrF8TxrikZM6.i49zL1KtLl8vSDMJCODz3gXoPneA11m', 0),
(7, 'miku@gmail.com', '$2y$10$nh8XBwjLIROuOe2pJl1ReOFpxo/bl/pHu6tfqRkyC4J9FiIg3cuTe', 1),
(9, 'oxy467777@gmail.com', '$2y$10$P7EFQXPMTp0Y0DiU1dVD/.t5z2n.UWoNFpPo2ba/Ek5kdUOJ6z5KO', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_admin_acc`
--
ALTER TABLE `tbl_admin_acc`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `Uk_employee_id` (`employee_id`);

--
-- Indexes for table `tbl_movie_series`
--
ALTER TABLE `tbl_movie_series`
  ADD PRIMARY KEY (`movie_series_id`),
  ADD KEY `fk_genreId1` (`genre_id1`),
  ADD KEY `fk_genreId2` (`genre_id2`),
  ADD KEY `fk_genreId3` (`genre_id3`);

--
-- Indexes for table `tbl_movie_series_episodes`
--
ALTER TABLE `tbl_movie_series_episodes`
  ADD PRIMARY KEY (`episode_id`);

--
-- Indexes for table `tbl_movie_series_genre`
--
ALTER TABLE `tbl_movie_series_genre`
  ADD PRIMARY KEY (`genre_id`);

--
-- Indexes for table `tbl_movie_series_seasons`
--
ALTER TABLE `tbl_movie_series_seasons`
  ADD PRIMARY KEY (`season_id`);

--
-- Indexes for table `tbl_payment`
--
ALTER TABLE `tbl_payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `Fk_signupId` (`signup_id`);

--
-- Indexes for table `tbl_signup_acc`
--
ALTER TABLE `tbl_signup_acc`
  ADD PRIMARY KEY (`signup_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_admin_acc`
--
ALTER TABLE `tbl_admin_acc`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_movie_series`
--
ALTER TABLE `tbl_movie_series`
  MODIFY `movie_series_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tbl_movie_series_episodes`
--
ALTER TABLE `tbl_movie_series_episodes`
  MODIFY `episode_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tbl_movie_series_genre`
--
ALTER TABLE `tbl_movie_series_genre`
  MODIFY `genre_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `tbl_movie_series_seasons`
--
ALTER TABLE `tbl_movie_series_seasons`
  MODIFY `season_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tbl_payment`
--
ALTER TABLE `tbl_payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tbl_signup_acc`
--
ALTER TABLE `tbl_signup_acc`
  MODIFY `signup_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_movie_series`
--
ALTER TABLE `tbl_movie_series`
  ADD CONSTRAINT `fk_genreId1` FOREIGN KEY (`genre_id1`) REFERENCES `tbl_movie_series_genre` (`genre_id`),
  ADD CONSTRAINT `fk_genreId2` FOREIGN KEY (`genre_id2`) REFERENCES `tbl_movie_series_genre` (`genre_id`),
  ADD CONSTRAINT `fk_genreId3` FOREIGN KEY (`genre_id3`) REFERENCES `tbl_movie_series_genre` (`genre_id`);

--
-- Constraints for table `tbl_payment`
--
ALTER TABLE `tbl_payment`
  ADD CONSTRAINT `Fk_signupId` FOREIGN KEY (`signup_id`) REFERENCES `tbl_signup_acc` (`signup_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
