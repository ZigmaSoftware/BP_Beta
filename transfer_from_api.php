<?php
// Third database connection (blue_planet)
$driver = "mysql";
$host = "localhost";
$username = "zigma";
$password = "?WSzvxHv1LGZ";
$databasename = "blue_planet";

try {
    $conn = new PDO("$driver:host=$host;dbname=$databasename", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch data from the API
    $apiUrl = "https://oefvsvp.com/api_get_data.php";
    $jsonData = file_get_contents($apiUrl);
    $data = json_decode($jsonData, true);

    // Insert data into the third database
    foreach ($data as $record) {
        // Check if the record already exists
        $checkQuery = $conn->prepare("
            SELECT COUNT(*) 
            FROM zigfly_recognized 
            WHERE emp_id = :emp_id 
              AND recognition_date = :recognition_date 
              AND recognition_time = :recognition_time
        ");
        $checkQuery->execute([
            ':emp_id' => $record['emp_id'],
            ':recognition_date' => $record['recognition_date'],
            ':recognition_time' => $record['recognition_time']
        ]);
        $nRows = $checkQuery->fetchColumn();

        // If the record does not exist, insert it
        if ($nRows == 0) {
            $insertQuery = $conn->prepare("
                INSERT INTO zigfly_recognized (
                    emp_id, 
                    name, 
                    records, 
                    captured_image_path, 
                    similarity_score, 
                    latitude, 
                    longitude, 
                    recognition_date, 
                    recognition_time
                ) VALUES (
                    :emp_id, 
                    :name, 
                    :records, 
                    :captured_image_path, 
                    :similarity_score, 
                    :latitude, 
                    :longitude, 
                    :recognition_date, 
                    :recognition_time
                )
            ");
            $insertQuery->execute([
                ':emp_id' => $record['emp_id'],
                ':name' => $record['name'],
                ':records' => $record['records'],
                ':captured_image_path' => $record['captured_image_path'],
                ':similarity_score' => $record['similarity_score'],
                ':latitude' => $record['latitude'],
                ':longitude' => $record['longitude'],
                ':recognition_date' => $record['recognition_date'],
                ':recognition_time' => $record['recognition_time']
            ]);
            echo "Inserted record for emp_id: " . $record['emp_id'] . "<br>";
        } else {
            // echo "Record for emp_id: " . $record['emp_id'] . " already exists in the third database.<br>";
        }
    }

    echo "Data transfer from API completed.";

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
