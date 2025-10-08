<?php
// Database connection
$driver = "mysql";
$host = "localhost";
$username = "zigma";
$password = "?WSzvxHv1LGZ";
$databasename = "blue_planet";

try {
    $conn = new PDO("$driver:host=$host;dbname=$databasename", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch data from the API
    $apiUrl = "https://oefvsvp.com/api_get_data_2.php";
    $jsonData = file_get_contents($apiUrl);

    if ($jsonData === false) {
        throw new Exception("Failed to fetch data from the API.");
    }

    $data = json_decode($jsonData, true);

    if (!is_array($data)) {
        throw new Exception("Invalid JSON format received from the API.");
    }

    // Insert data into the database
    foreach ($data as $record) {
        // Check if the record already exists
        $checkQuery = $conn->prepare("
            SELECT COUNT(*) 
            FROM employees 
            WHERE emp_id = :emp_id
        ");
        $checkQuery->execute([':emp_id' => $record['emp_id']]);
        $nRows = $checkQuery->fetchColumn();

        // If the record does not exist, insert it
        if ($nRows == 0) {
            $insertQuery = $conn->prepare("
                INSERT INTO employees (
                    emp_id, 
                    name, 
                    department, 
                    image_path, 
                    dob, 
                    qr_code_path, 
                    designation, 
                    blood_group
                ) VALUES (
                    :emp_id, 
                    :name, 
                    :department, 
                    :image_path, 
                    :dob, 
                    :qr_code_path, 
                    :designation, 
                    :blood_group
                )
            ");
            $insertQuery->execute([
                ':emp_id' => $record['emp_id'],
                ':name' => $record['name'],
                ':department' => $record['department'],
                ':image_path' => $record['image_path'],
                ':dob' => $record['dob'],
                ':qr_code_path' => $record['qr_code_path'],
                ':designation' => $record['designation'],
                ':blood_group' => $record['blood_group']
            ]);
            echo "Inserted record for emp_id: " . $record['emp_id'] . "<br>";
        } else {
            echo "Record for emp_id: " . $record['emp_id'] . " already exists in the database.<br>";
        }
    }

    echo "Data transfer from API completed.";

} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
