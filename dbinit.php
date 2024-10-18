<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lakhvinder_8959531_toystore";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
$conn->query($sql);

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Create table if not exists
$table_sql = "CREATE TABLE IF NOT EXISTS toys (
    ToyID INT AUTO_INCREMENT PRIMARY KEY,
    ToyName VARCHAR(255) NOT NULL,
    ToyDescription TEXT,
    QuantityAvailable INT NOT NULL,
    Price DECIMAL(10,2) NOT NULL,
    ProductAddedBy VARCHAR(100) NOT NULL DEFAULT 'Lakhvinder Singh',
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Visibility ENUM('visible', 'hidden') DEFAULT 'visible'
)";
$conn->query($table_sql);
?>
