<?php
$host = 'localhost'; // or your MySQL server address
$dbname = 'barterdb'; // your database name
$username = 'root'; // your database username
$password = ''; // your database password (empty by default for XAMPP)

try {
    // Create a PDO connection
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle connection error
    echo "Database connection failed: " . $e->getMessage();
    exit(); // Terminate the script if connection fails
}
?>
