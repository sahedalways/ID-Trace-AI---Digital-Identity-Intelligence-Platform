-- Payment Fingerprint Migration
-- Run this SQL to capture browser, IP, country and device used at payment time.
-- These power the payment info columns in the admin-clients and affiliate-clients tables.

ALTER TABLE `transactions`
  ADD COLUMN `ip_address` VARCHAR(45) DEFAULT NULL AFTER `zip`,
  ADD COLUMN `device` VARCHAR(100) DEFAULT NULL AFTER `ip_address`,
  ADD COLUMN `browser` VARCHAR(100) DEFAULT NULL AFTER `device`,
  ADD COLUMN `user_agent` TEXT AFTER `browser`;
