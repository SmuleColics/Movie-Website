-- Run this ONCE, AFTER update-2026-11-archive.sql.
-- Archive without database views, because free hosts such as InfinityFree
-- don't allow CREATE VIEW.
--
-- How it works now: tbl_movie_series and tbl_movie_series_genre are real
-- tables again and hold only the active rows, so every page that reads them
-- still leaves archived ones out. Archiving moves a row into the matching
-- *_archived table; restoring moves it back (see cv_archive_row() and
-- cv_restore_row() in includes/bootstrap.php).
--
-- If you add a column to tbl_movie_series or tbl_movie_series_genre later,
-- add it to the matching *_archived table too, in the same position.

USE db_finalproject;

DROP VIEW IF EXISTS `tbl_movie_series`;
DROP VIEW IF EXISTS `tbl_movie_series_genre`;
RENAME TABLE `tbl_movie_series_all` TO `tbl_movie_series`,
             `tbl_movie_series_genre_all` TO `tbl_movie_series_genre`;

-- Same columns and indexes, no foreign keys.
CREATE TABLE `tbl_movie_series_archived` LIKE `tbl_movie_series`;
CREATE TABLE `tbl_movie_series_genre_archived` LIKE `tbl_movie_series_genre`;

-- Watch progress stays while a title is archived, so it is back on restore.
ALTER TABLE `tbl_watch_progress` DROP FOREIGN KEY `fk_progress_title`;

-- Move what is archived now. Titles first, since they point at genres.
INSERT INTO `tbl_movie_series_archived` SELECT * FROM `tbl_movie_series` WHERE `is_archived` = 1;
DELETE FROM `tbl_movie_series` WHERE `is_archived` = 1;
INSERT INTO `tbl_movie_series_genre_archived` SELECT * FROM `tbl_movie_series_genre` WHERE `is_archived` = 1;
DELETE FROM `tbl_movie_series_genre` WHERE `is_archived` = 1;
