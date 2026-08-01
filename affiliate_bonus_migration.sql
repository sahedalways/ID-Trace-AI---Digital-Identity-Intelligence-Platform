-- Affiliate Bonus System Migration
-- Run this SQL to add customizable bonus support

-- 1. Create global affiliate settings table
CREATE TABLE IF NOT EXISTS `affiliate_settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Insert global defaults (flat $32, recursion mode)
INSERT IGNORE INTO `affiliate_settings` (`setting_key`, `setting_value`) VALUES
('global_bonus_amount', '32.00'),
('global_bonus_type', 'recursion');

-- 3. Add per-affiliate bonus columns to affiliates table
ALTER TABLE `affiliates`
  ADD COLUMN `referral_bonus` DECIMAL(10,2) DEFAULT NULL AFTER `balance`,
  ADD COLUMN `bonus_type` ENUM('recursion','fixed') DEFAULT NULL AFTER `referral_bonus`,
  ADD COLUMN `use_global_settings` TINYINT(1) DEFAULT 1 AFTER `bonus_type`;
