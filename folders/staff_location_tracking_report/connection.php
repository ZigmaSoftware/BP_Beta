<?php
$servername = "125.17.238.158";
$username = "my_root";
$password = "my@123456";
$dbname = "employee_ho";

try {
    // Create a PDO connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);

    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connection successful!";
} catch (PDOException $e) {
    // Handle connection error
    die("Connection failed: " . $e->getMessage());
}
?>