-- Run this AFTER update-2026-11-admin-notifications.sql.
-- Tracks which "new arrival" notifications each member has read,
-- so the member bell shows red unread dots and "Mark all as read" like the admin bell.

USE db_finalproject;

CREATE TABLE IF NOT EXISTS `tbl_member_notif_reads` (
  `signup_id` int(11) NOT NULL,
  `movie_series_id` int(11) NOT NULL,
  `read_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`signup_id`, `movie_series_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
