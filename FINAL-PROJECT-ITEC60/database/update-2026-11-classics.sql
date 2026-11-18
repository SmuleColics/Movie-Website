-- Run this AFTER update-2026-11-tmdb.sql.
-- CineVault becomes a free classic-film site: every title plays in full.
--  * removes the old sample titles (they only had short clips, not full movies)
--  * adds a wide "backdrop" image and a "feature_slot" so the carousels and
--    banners can feature any title (no more special poster file names)
-- Then run database/seed-free-movies.php to fill the library.

USE db_finalproject;

ALTER TABLE `tbl_movie_series`
  ADD COLUMN IF NOT EXISTS `backdrop` varchar(255) DEFAULT NULL AFTER `modal_poster_title`,
  ADD COLUMN IF NOT EXISTS `feature_slot` varchar(20) DEFAULT NULL AFTER `backdrop`;

-- Old sample titles: anything whose video is a local clip rather than a full
-- stream. (Uploaded full movies added later keep working; this only runs once.)
DELETE e FROM tbl_movie_series_episodes e
  JOIN tbl_movie_series_seasons s ON s.season_id = e.season_id
  JOIN tbl_movie_series m ON m.movie_series_id = s.movie_series_id
  WHERE m.tmdb_id IS NULL;
DELETE s FROM tbl_movie_series_seasons s
  JOIN tbl_movie_series m ON m.movie_series_id = s.movie_series_id
  WHERE m.tmdb_id IS NULL;
DELETE r FROM tbl_member_notif_reads r
  JOIN tbl_movie_series m ON m.movie_series_id = r.movie_series_id
  WHERE m.tmdb_id IS NULL;
DELETE FROM tbl_movie_series WHERE tmdb_id IS NULL;
