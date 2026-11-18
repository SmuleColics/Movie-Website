-- Run this AFTER update-2026-11-member-notifications.sql.
-- Remembers which TMDB title each movie/series came from, so the admin
-- "Search TMDB" import can warn when a title is already in the library.

USE db_finalproject;

ALTER TABLE `tbl_movie_series`
  ADD COLUMN IF NOT EXISTS `tmdb_id` int(11) DEFAULT NULL AFTER `movie_series_id`,
  ADD COLUMN IF NOT EXISTS `tmdb_type` varchar(5) DEFAULT NULL AFTER `tmdb_id`;

ALTER TABLE `tbl_movie_series`
  ADD UNIQUE KEY IF NOT EXISTS `uk_tmdb` (`tmdb_type`, `tmdb_id`);
