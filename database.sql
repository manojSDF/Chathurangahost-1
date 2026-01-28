-- Create Database
CREATE DATABASE IF NOT EXISTS billing_system;
USE billing_system;

-- Customers Table
CREATE TABLE IF NOT EXISTS customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(15),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bills Table
CREATE TABLE IF NOT EXISTS bills (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT,
    subtotal DECIMAL(10, 2),
    tax_percent DECIMAL(5, 2),
    tax_amount DECIMAL(10, 2),
    grand_total DECIMAL(10, 2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id)
);

-- Bill Items Table
CREATE TABLE IF NOT EXISTS bill_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    bill_id INT NOT NULL,
    item_name VARCHAR(100) NOT NULL,
    quantity DECIMAL(10, 2),
    price DECIMAL(10, 2),
    total DECIMAL(10, 2),
    FOREIGN KEY (bill_id) REFERENCES bills(id)
);

-- Index for faster queries
CREATE INDEX idx_created_at ON bills(created_at);
