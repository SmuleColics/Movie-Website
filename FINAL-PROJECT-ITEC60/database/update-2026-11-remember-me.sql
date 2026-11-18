-- Run this AFTER update-2026-11-free-membership.sql.
-- "Remember me" on the member sign in page.
-- The browser keeps a random token; only its SHA-256 hash is stored here,
-- so a leaked database cannot be used to sign in.

USE db_finalproject;

CREATE TABLE IF NOT EXISTS `tbl_remember_tokens` (
  `token_id` int(11) NOT NULL AUTO_INCREMENT,
  `signup_id` int(11) NOT NULL,
  `selector` char(24) NOT NULL,
  `validator_hash` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`token_id`),
  UNIQUE KEY `uk_selector` (`selector`),
  KEY `idx_signup` (`signup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
