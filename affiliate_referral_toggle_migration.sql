-- Affiliate Referral Toggle Migration
-- Run this SQL to add per-affiliate referral enable/disable control.
-- When disabled, the affiliate dashboard hides the promotional link section
-- and go.php tracking links stop recording clicks.

ALTER TABLE `affiliates`
  ADD COLUMN `referral_enabled` TINYINT(1) NOT NULL DEFAULT 1 AFTER `status`;
