<?php
// Database connection configuration
$host = 'localhost';           // Hostname of the MySQL server
$dbname = 'bkim71';            // Name of the database to connect to
$username = 'bkim71';          // MySQL username
$password = 'bkim71';          // MySQL password

try {
    // Create a new PDO instance to connect to the MySQL database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Set the PDO error mode to exception for better debugging
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // If connection fails, output error message and stop execution
    die("Database connection failed: " . $e->getMessage());
}
?>
