-- Initial Database Setup for HoneStay Application
-- This script runs automatically when MySQL container starts for the first time

-- Set timezone
SET time_zone = '+07:00';

-- Create additional databases if needed
-- CREATE DATABASE IF NOT EXISTS honestay_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create additional users if needed
-- CREATE USER IF NOT EXISTS 'app_user'@'%' IDENTIFIED BY 'app_password';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON honestay.* TO 'app_user'@'%';

-- Create some initial tables (example)
USE pot_manage_db;

-- Users table example
CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add indexes for better performance
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_created_at ON users(created_at);

-- Insert default admin user (password: password - remember to change in production!)
INSERT IGNORE INTO users (name, email, password) VALUES 
('Admin User', 'admin@honestay.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Flush privileges to ensure all changes take effect
FLUSH PRIVILEGES;
