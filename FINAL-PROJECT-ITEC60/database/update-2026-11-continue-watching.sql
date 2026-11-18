-- Run this AFTER update-2026-11-classics.sql.
-- "Continue Watching": remembers where each member stopped in each title
-- (for a series: which episode and where in it).

USE db_finalproject;

CREATE TABLE IF NOT EXISTS `tbl_watch_progress` (
  `signup_id` int(11) NOT NULL,
  `movie_series_id` int(11) NOT NULL,
  `episode_id` int(11) DEFAULT NULL,
  `position_sec` int(11) NOT NULL DEFAULT 0,
  `duration_sec` int(11) NOT NULL DEFAULT 0,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`signup_id`, `movie_series_id`),
  KEY `idx_recent` (`signup_id`, `updated_at`),
  CONSTRAINT `fk_progress_title` FOREIGN KEY (`movie_series_id`) REFERENCES `tbl_movie_series` (`movie_series_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Per-episode positions, so going back to an earlier episode also resumes.
CREATE TABLE IF NOT EXISTS `tbl_episode_progress` (
  `signup_id` int(11) NOT NULL,
  `episode_id` int(11) NOT NULL,
  `position_sec` int(11) NOT NULL DEFAULT 0,
  `duration_sec` int(11) NOT NULL DEFAULT 0,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`signup_id`, `episode_id`),
  CONSTRAINT `fk_progress_episode` FOREIGN KEY (`episode_id`) REFERENCES `tbl_movie_series_episodes` (`episode_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
