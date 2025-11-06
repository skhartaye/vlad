-- Disease Tracker Database Schema for PostgreSQL (Neon)
-- Note: Database is already created in Neon, so we don't need CREATE DATABASE

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create index for users
CREATE INDEX IF NOT EXISTS idx_username ON users(username);
CREATE INDEX IF NOT EXISTS idx_email ON users(email);

-- Create trigger for updated_at on users
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

CREATE TRIGGER update_users_updated_at BEFORE UPDATE ON users
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- Disease types table
CREATE TABLE IF NOT EXISTS disease_types (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    color_code VARCHAR(7) DEFAULT '#FF0000',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Pre-populate with three diseases (using ON CONFLICT for PostgreSQL)
INSERT INTO disease_types (name, description, color_code) VALUES
('dengue', 'Dengue fever transmitted by mosquitoes', '#d32f2f'),
('leptospirosis', 'Bacterial infection from contaminated water', '#f57c00'),
('malaria', 'Parasitic disease transmitted by mosquitoes', '#fbc02d')
ON CONFLICT (name) DO NOTHING;

-- Case reports table
CREATE TABLE IF NOT EXISTS case_reports (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL,
    disease_type_id INTEGER NOT NULL,
    address TEXT NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (disease_type_id) REFERENCES disease_types(id)
);

-- Create indexes for case_reports
CREATE INDEX IF NOT EXISTS idx_user_id ON case_reports(user_id);
CREATE INDEX IF NOT EXISTS idx_disease_type ON case_reports(disease_type_id);
CREATE INDEX IF NOT EXISTS idx_created_at ON case_reports(created_at);
CREATE INDEX IF NOT EXISTS idx_coordinates ON case_reports(latitude, longitude);

-- Create trigger for updated_at on case_reports
CREATE TRIGGER update_case_reports_updated_at BEFORE UPDATE ON case_reports
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
