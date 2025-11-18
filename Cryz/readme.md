-- Table for storing completed transactions
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    total_amount DECIMAL(10, 2) NOT NULL,
    amount_paid DECIMAL(10, 2) NOT NULL,
    change_given DECIMAL(10, 2) NOT NULL,
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for storing the items associated with each transaction
CREATE TABLE transaction_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    price_per_item DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id)
        ON DELETE CASCADE
);

-- NEW TABLE: For storing fixed products (SKUs)
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(255) NOT NULL UNIQUE,
    price_per_item DECIMAL(10, 2) NOT NULL,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
