-- Run this AFTER update-2026-11.sql.
-- CineVault is now free: sign up creates a working account right away.
-- No GCash payment and no admin approval. Old payment rows are kept as history.

USE db_finalproject;

ALTER TABLE `tbl_signup_acc`
  ADD COLUMN IF NOT EXISTS `date_created` datetime NOT NULL DEFAULT current_timestamp() AFTER `is_archived`;

-- Existing members: use their old payment date as their join date.
UPDATE `tbl_signup_acc` s
JOIN (SELECT signup_id, MIN(date_created) AS first_paid FROM tbl_payment GROUP BY signup_id) p
  ON p.signup_id = s.signup_id
SET s.date_created = p.first_paid;

-- Accounts that were still waiting for payment approval become members now.
INSERT INTO `tbl_signup_acc` (first_name, last_name, mobile_number, signup_email, signup_password, date_created)
SELECT p.first_name, p.last_name, p.mobile_number, p.signup_email, p.signup_password, p.date_created
FROM `tbl_signup_pending` p
WHERE NOT EXISTS (SELECT 1 FROM tbl_signup_acc s WHERE s.signup_email = p.signup_email);

DELETE FROM `tbl_signup_pending`;
