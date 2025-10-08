<?php
$host = "localhost";
$dbname = "zigma_complaints";
$username = "zigma";
$password = "?WSzvxHv1LGZ";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Prepare the UPDATE query
    
    $sql = "UPDATE complaint_creation 
            SET complaint_no = CONCAT('CMP-250', id) 
            WHERE id BETWEEN :start_id AND :end_id";

    $stmt = $pdo->prepare($sql);

    // Bind parameters
    $start_id = 2410;
    $end_id = 2613;
    $stmt->bindParam(':start_id', $start_id, PDO::PARAM_INT);
    $stmt->bindParam(':end_id', $end_id, PDO::PARAM_INT);

    // Execute the update
    $stmt->execute();

    echo "Complaint numbers updated successfully!";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close connection
$pdo = null;
?>