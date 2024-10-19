<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lakhvinder_blog_midterm";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
$conn->query($sql);

// Connect to the blog database
$conn = new mysqli($servername, $username, $password, $dbname);

// Create the blog posts table if not exists
$table_sql = "CREATE TABLE IF NOT EXISTS blog_posts (
    PostID INT AUTO_INCREMENT PRIMARY KEY,
    Title VARCHAR(255) NOT NULL,
    Image VARCHAR(255) NOT NULL,
    Content TEXT NOT NULL,
    Author VARCHAR(100) NOT NULL DEFAULT 'Lakhvinder Singh',
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Visibility ENUM('visible', 'hidden') DEFAULT 'visible'
)";
$conn->query($table_sql);
?>
