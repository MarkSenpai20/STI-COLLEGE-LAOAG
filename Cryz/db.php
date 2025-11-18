<?php
/*
 * DATABASE CONNECTION & SETUP
 *
 * 1.  Create a database in your MySQL server (e.g., using phpMyAdmin)
 * named 'kahera_db'.
 * 2.  Run the SQL queries (found in the comments below) to create
 * your tables.
 * 3.  Update the $servername, $username, $password, and $dbname
 * variables below to match your local server setup.
 */

$servername = "localhost"; // Or "127.0.0.1"
$username = "root";        // Default for XAMPP/MAMP
$password = "";            // Default for XAMPP (empty)
$dbname = "kahera_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// You can leave this file as-is after configuring the variables.
// The api.php file will include it.

/*
 * ===================================================================
 * SQL FOR DATABASE SETUP
 * ===================================================================
 *
 * Run these queries in your SQL client (like phpMyAdmin)
 * to create the necessary tables.
 *
 *
 * -- Table for storing completed transactions --
 *
 * CREATE TABLE transactions (
 * id INT AUTO_INCREMENT PRIMARY KEY,
 * total_amount DECIMAL(10, 2) NOT NULL,
 * amount_paid DECIMAL(10, 2) NOT NULL,
 * change_given DECIMAL(10, 2) NOT NULL,
 * transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
 * );
 *
 *
 * -- Table for storing the items associated with each transaction --
 *
 * CREATE TABLE transaction_items (
 * id INT AUTO_INCREMENT PRIMARY KEY,
 * transaction_id INT NOT NULL,
 * item_name VARCHAR(255) NOT NULL,
 * quantity INT NOT NULL,
 * price_per_item DECIMAL(10, 2) NOT NULL,
 * FOREIGN KEY (transaction_id) REFERENCES transactions(id)
 * ON DELETE CASCADE
 * );
 *
 *
 * -- NEW TABLE: For storing fixed products (SKUs) --
 *
 * CREATE TABLE products (
 * id INT AUTO_INCREMENT PRIMARY KEY,
 * item_name VARCHAR(255) NOT NULL UNIQUE,
 * price_per_item DECIMAL(10, 2) NOT NULL,
 * date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
 * );
 *
 */

?>


