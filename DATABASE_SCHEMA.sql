-- Portfolio Admin Authentication System - Database Schema
-- Created for MySQL 5.7+ with InnoDB storage engine
-- Character Set: UTF-8
-- Collation: utf8_general_ci

-- ============================================================================
-- DATABASE CREATION
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `portfolio_admin` 
CHARACTER SET utf8 
COLLATE utf8_general_ci;

USE `portfolio_admin`;

-- ============================================================================
-- TABLE: admin_users
-- Purpose: Stores admin account credentials and user information
-- ============================================================================

CREATE TABLE IF NOT EXISTS `admin_users` (
    `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes for performance
    INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Stores admin user credentials';

-- ============================================================================
-- TABLE: admin_sessions
-- Purpose: Tracks active user sessions for security and audit
-- ============================================================================

CREATE TABLE IF NOT EXISTS `admin_sessions` (
    `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `session_id` VARCHAR(255) NOT NULL UNIQUE,
    `user_id` INT(11) NOT NULL,
    `ip_address` VARCHAR(45),
    `user_agent` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `expires_at` TIMESTAMP,
    
    -- Foreign key relationship
    CONSTRAINT `fk_admin_sessions_user_id` 
        FOREIGN KEY (`user_id`) 
        REFERENCES `admin_users` (`id`) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    
    -- Indexes for performance
    INDEX `idx_session_id` (`session_id`),
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Stores active admin sessions';

-- ============================================================================
-- SAMPLE DATA: Default Admin User
-- ============================================================================

-- NOTE: Replace the password hash with actual bcrypt hash!
-- To generate: echo password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12]);
-- This generates: $2y$12$L9FGyVHKVUa.DuM8lDOu1.LIx8QqzRVe1Py8LwCPM6NQQPB5PG1oW

INSERT INTO `admin_users` (`email`, `password`, `name`, `created_at`, `updated_at`)
VALUES 
('admin@portfolio.com', '$2y$12$L9FGyVHKVUa.DuM8lDOu1.LIx8QqzRVe1Py8LwCPM6NQQPB5PG1oW', 'Admin', NOW(), NOW())
ON DUPLICATE KEY UPDATE `password` = `password`;

-- ============================================================================
-- VIEWS (Optional - for future use)
-- ============================================================================

-- View: Active Sessions
-- Purpose: See all currently active sessions
CREATE OR REPLACE VIEW `v_active_sessions` AS
SELECT 
    s.id,
    s.session_id,
    u.email,
    u.name,
    s.ip_address,
    s.user_agent,
    s.created_at,
    s.expires_at,
    IF(s.expires_at > NOW(), 'Active', 'Expired') AS status
FROM `admin_sessions` s
JOIN `admin_users` u ON s.user_id = u.id
ORDER BY s.created_at DESC;

-- ============================================================================
-- STORED PROCEDURES (Optional - for future use)
-- ============================================================================

-- Procedure: Create Session
-- Purpose: Atomic session creation with user verification
DELIMITER $$

CREATE PROCEDURE IF NOT EXISTS `sp_create_session`(
    IN p_user_id INT,
    IN p_session_id VARCHAR(255),
    IN p_ip_address VARCHAR(45),
    IN p_user_agent TEXT,
    IN p_lifetime INT,
    OUT p_success TINYINT
)
BEGIN
    DECLARE v_count INT;
    
    -- Check if user exists
    SELECT COUNT(*) INTO v_count FROM `admin_users` WHERE `id` = p_user_id;
    
    IF v_count = 0 THEN
        SET p_success = 0;
    ELSE
        -- Insert new session
        INSERT INTO `admin_sessions` 
        (`session_id`, `user_id`, `ip_address`, `user_agent`, `expires_at`)
        VALUES 
        (p_session_id, p_user_id, p_ip_address, p_user_agent, DATE_ADD(NOW(), INTERVAL p_lifetime SECOND));
        
        SET p_success = 1;
    END IF;
END$$

DELIMITER ;

-- ============================================================================
-- MAINTENANCE QUERIES
-- ============================================================================

-- Clean up expired sessions (run periodically via cron)
-- DELETE FROM `admin_sessions` WHERE `expires_at` < NOW();

-- Check session activity
-- SELECT u.email, COUNT(*) as session_count, MAX(s.created_at) as last_login
-- FROM `admin_sessions` s
-- JOIN `admin_users` u ON s.user_id = u.id
-- WHERE s.expires_at > NOW()
-- GROUP BY u.id;

-- Get all active sessions with user info
-- SELECT 
--     u.id, u.email, u.name, s.session_id, 
--     s.ip_address, s.created_at, s.expires_at
-- FROM `admin_sessions` s
-- JOIN `admin_users` u ON s.user_id = u.id
-- WHERE s.expires_at > NOW()
-- ORDER BY s.created_at DESC;

-- ============================================================================
-- NOTES
-- ============================================================================
/*
1. PASSWORD HASHING:
   - All passwords stored as Bcrypt hashes (cost factor 12)
   - Use password_hash('password', PASSWORD_BCRYPT, ['cost' => 12])
   - Verify with password_verify('password', $hash)

2. SESSION MANAGEMENT:
   - Sessions have 1-hour default lifetime (configurable in config.php)
   - Old sessions should be cleaned up regularly
   - Each new login invalidates previous sessions for same user

3. SECURITY:
   - IP addresses tracked for security analysis
   - User-agent tracked to detect unauthorized access from different devices
   - Sessions stored server-side, not client-side
   - Sensitive operations should verify both session and user ID

4. BACKUP RECOMMENDATIONS:
   - Back up database daily
   - Keep encrypted backups of production databases
   - Test restore procedures regularly

5. PERFORMANCE:
   - Consider archiving old sessions to separate table
   - Add indexes on frequently queried columns (done)
   - Monitor table sizes and optimize as needed

6. COMPLIANCE:
   - Consider GDPR implications for session/IP storage
   - Implement data retention policies
   - Log important actions (logins, changes, deletions)
*/

-- ============================================================================
-- VERSION HISTORY
-- ============================================================================
/*
Version 1.0 - Initial Release
  - Basic admin_users table with email/password
  - Session tracking with IP and user-agent
  - Foreign key relationship with CASCADE delete
  - Default admin user creation
  - Support views and stored procedures

Version 1.1 (Recommended) - Consider adding:
  - Last login timestamp
  - Login attempt tracking (for rate limiting)
  - Audit log table
  - User permissions/roles
  - Account status (active/inactive)
  - Password change history
*/

-- ============================================================================
