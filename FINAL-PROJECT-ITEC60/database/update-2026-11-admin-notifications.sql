-- Run this AFTER update-2026-11-favorite-genres.sql.
-- Tracks which "new member" notifications each admin has read,
-- so the bell can show red unread dots and "Mark all as read".

USE db_finalproject;

CREATE TABLE IF NOT EXISTS `tbl_admin_notif_reads` (
  `admin_id` int(11) NOT NULL,
  `signup_id` int(11) NOT NULL,
  `read_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`admin_id`, `signup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
