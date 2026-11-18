-- Run this AFTER update-2026-11-remember-me.sql.
-- Saves each member's 3 favorite genres on their account (comma-separated
-- genre ids), so the first-time genre picker is asked once per account,
-- not once per browser.

USE db_finalproject;

ALTER TABLE `tbl_signup_acc`
  ADD COLUMN IF NOT EXISTS `favorite_genres` varchar(100) DEFAULT NULL AFTER `mobile_number`;
