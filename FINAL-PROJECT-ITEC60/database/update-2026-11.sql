-- Run this AFTER importing db_finalproject.sql.
-- The old dump is older than the code: it lacks the pending sign-up table,
-- the name/mobile columns and the password reset columns.

USE db_finalproject;

CREATE TABLE IF NOT EXISTS `tbl_signup_pending` (
  `pending_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `mobile_number` varchar(11) NOT NULL,
  `signup_email` varchar(100) NOT NULL,
  `signup_password` varchar(255) NOT NULL,
  `reference_no` varchar(13) DEFAULT NULL,
  `payment_amount` int(11) NOT NULL DEFAULT 99,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`pending_id`),
  UNIQUE KEY `uk_pending_email` (`signup_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `tbl_signup_acc`
  ADD COLUMN IF NOT EXISTS `first_name` varchar(100) NOT NULL DEFAULT '' AFTER `signup_id`,
  ADD COLUMN IF NOT EXISTS `last_name` varchar(100) NOT NULL DEFAULT '' AFTER `first_name`,
  ADD COLUMN IF NOT EXISTS `mobile_number` varchar(11) NOT NULL DEFAULT '' AFTER `last_name`,
  ADD COLUMN IF NOT EXISTS `reset_code` varchar(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `reset_expires` datetime DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `reset_attempts` tinyint(4) NOT NULL DEFAULT 0;

-- One account per email.
ALTER TABLE `tbl_signup_acc` ADD UNIQUE KEY IF NOT EXISTS `uk_signup_email` (`signup_email`);
ALTER TABLE `tbl_admin_acc` ADD UNIQUE KEY IF NOT EXISTS `uk_admin_email` (`email`);

-- Brute force guard for sign-in forms (counts failed tries per email + IP).
CREATE TABLE IF NOT EXISTS `tbl_login_attempts` (
  `attempt_id` int(11) NOT NULL AUTO_INCREMENT,
  `scope` varchar(20) NOT NULL,
  `identifier` varchar(150) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempted_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`attempt_id`),
  KEY `idx_lookup` (`scope`, `identifier`, `attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
