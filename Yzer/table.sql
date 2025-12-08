-- 1. Create the Database (matches your db.php)
CREATE DATABASE IF NOT EXISTS barangay_db;

-- 2. Select the Database
USE barangay_db;

-- 3. Create the Table (matches your PHP files)
CREATE TABLE IF NOT EXISTS visitors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visitor_name VARCHAR(100) NOT NULL,
    purpose VARCHAR(255) NOT NULL,
    time_in TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);