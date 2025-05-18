-- Create database
CREATE DATABASE IF NOT EXISTS uwu_zone;
USE uwu_zone;

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(255),
    featured BOOLEAN DEFAULT FALSE,
    sales_count INT DEFAULT 0,
    review_count INT DEFAULT 0,
    rating DECIMAL(3,2) DEFAULT 5.00,
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
