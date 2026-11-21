-- Run this AFTER update-2026-11-continue-watching.sql.
-- Site settings the admin can change in Settings (shown on printed reports,
-- the footer and elsewhere), and each member's own preferences.

USE db_finalproject;

CREATE TABLE IF NOT EXISTS `tbl_settings` (
  `setting_key` varchar(64) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `tbl_settings` (`setting_key`, `setting_value`) VALUES
  ('site_name', 'CineVault'),
  ('site_tagline', 'Classic movies and TV, free and in full.'),
  ('contact_email', ''),
  ('contact_phone', ''),
  ('contact_address', 'Philippines');

CREATE TABLE IF NOT EXISTS `tbl_member_settings` (
  `signup_id` int(11) NOT NULL,
  `autoplay_previews` tinyint(1) NOT NULL DEFAULT 1,
  `resume_playback` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`signup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
