-- Disease Tracker Database Schema
-- Create database
CREATE DATABASE IF NOT EXISTS disease_tracker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE disease_tracker;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Disease types table
CREATE TABLE IF NOT EXISTS disease_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    color_code VARCHAR(7) DEFAULT '#FF0000',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pre-populate with three diseases
INSERT INTO disease_types (name, description, color_code) VALUES
('dengue', 'Dengue fever transmitted by mosquitoes', '#d32f2f'),
('leptospirosis', 'Bacterial infection from contaminated water', '#f57c00'),
('malaria', 'Parasitic disease transmitted by mosquitoes', '#fbc02d')
ON DUPLICATE KEY UPDATE name=name;

-- Case reports table
CREATE TABLE IF NOT EXISTS case_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    disease_type_id INT NOT NULL,
    address TEXT NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (disease_type_id) REFERENCES disease_types(id),
    INDEX idx_user_id (user_id),
    INDEX idx_disease_type (disease_type_id),
    INDEX idx_created_at (created_at),
    INDEX idx_coordinates (latitude, longitude)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
