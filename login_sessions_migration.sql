-- Login Session Fingerprint Migration
-- Run this SQL to add per-login fingerprint tracking (IP, device, browser, user agent)
-- This table powers the "Sign-in Activity" box on the admin client detail page.

CREATE TABLE IF NOT EXISTS `login_sessions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `uid` INT NOT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `device` VARCHAR(100) DEFAULT NULL,
  `browser` VARCHAR(100) DEFAULT NULL,
  `user_agent` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_uid` (`uid`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
