<?php
// --- 1. CONFIGURATION ---
// We set the variables for the database connection.
$host = "localhost";        // The server (your computer)
$user = "root";             // The username (default for XAMPP)
$pass = "";                 // The password (default is empty)
$db_name = "barangay_db";   // The name of your database folder

// --- 2. CONNECT ---
// Open the connection to the database. We call this link '$conn'.
$conn = new mysqli($host, $user, $pass, $db_name);

// --- 3. CHECK CONNECTION ---
// If the connection fails (has an error), stop the code and show why.
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>