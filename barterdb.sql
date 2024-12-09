CREATE DATABASE barterdb;

USE barterdb;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    address TEXT,
    role ENUM('user', 'admin') DEFAULT 'user',
    status ENUM('active', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Items table
CREATE TABLE IF NOT EXISTS items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    item_type ENUM('product', 'service') NOT NULL,
    quantity INT NOT NULL,
    status ENUM('available', 'traded') DEFAULT 'available',
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Transactions table
CREATE TABLE IF NOT EXISTS transactions (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT,
    user_id INT,
    partner_id INT,
    hash_key VARCHAR(16) NOT NULL,
    status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    cost_summary FLOAT DEFAULT 0,
    start_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    end_date DATETIME,
    FOREIGN KEY (item_id) REFERENCES items(item_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (partner_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Equivalence table for item trading
CREATE TABLE IF NOT EXISTS equivalence (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item1 VARCHAR(100) NOT NULL,
    item2 VARCHAR(100) NOT NULL,
    equivalent_value DECIMAL(10, 2) NOT NULL,
    UNIQUE KEY (item1, item2)
);

ALTER TABLE items ADD COLUMN value INT CHECK (value BETWEEN 1 AND 15);
ALTER TABLE users ADD role ENUM('user', 'admin') DEFAULT 'user';
UPDATE users SET role = 'admin' WHERE user_id = :user_id; -- Assuming you have the user ID of the person to promote

CREATE TABLE IF NOT EXISTS contact_messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    received_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


