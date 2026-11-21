-- Run this ONCE, AFTER update-2026-11-settings.sql.
-- Archive instead of delete: movies/series, genres and admin accounts can be
-- archived from the admin side and restored from the Archive page.
--
-- How it works: the real tables are renamed to *_all and get an is_archived
-- column. Views with the old names show only the active rows, so every page
-- that reads tbl_movie_series or tbl_movie_series_genre (all the member pages)
-- automatically leaves archived ones out. Inserts and updates through the
-- views still work. The Archive page reads the *_all tables.
--
-- If you add a column to tbl_movie_series_all or tbl_movie_series_genre_all
-- later, run the matching CREATE OR REPLACE VIEW again so the view includes it.

USE db_finalproject;

-- Movies and series
ALTER TABLE `tbl_movie_series`
  ADD COLUMN IF NOT EXISTS `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `archived_at` datetime DEFAULT NULL;
RENAME TABLE `tbl_movie_series` TO `tbl_movie_series_all`;
CREATE OR REPLACE VIEW `tbl_movie_series` AS
  SELECT * FROM `tbl_movie_series_all` WHERE `is_archived` = 0;

-- Genres
ALTER TABLE `tbl_movie_series_genre`
  ADD COLUMN IF NOT EXISTS `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `archived_at` datetime DEFAULT NULL;
RENAME TABLE `tbl_movie_series_genre` TO `tbl_movie_series_genre_all`;
CREATE OR REPLACE VIEW `tbl_movie_series_genre` AS
  SELECT * FROM `tbl_movie_series_genre_all` WHERE `is_archived` = 0;

-- Admin accounts
ALTER TABLE `tbl_admin_acc`
  ADD COLUMN IF NOT EXISTS `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `archived_at` datetime DEFAULT NULL;

-- Members already had is_archived; record when they were archived too.
ALTER TABLE `tbl_signup_acc`
  ADD COLUMN IF NOT EXISTS `archived_at` datetime DEFAULT NULL;

-- "Terror" from the original data is "Horror" everywhere else (TMDB, the site's copy).
UPDATE `tbl_movie_series_genre_all` SET `genre_name` = 'Horror' WHERE `genre_name` = 'Terror';
