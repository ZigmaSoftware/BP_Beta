<?php
// Second database connection (xqxcigqg_tree_plantation)
$servername2 = "103.174.10.14";
$username2 = "xqxcigqg_tree_plant";
$password2 = "TreePlant@123";
$dbname2 = "xqxcigqg_tree_plantation";

$conn2 = new PDO("mysql:host=$servername2;dbname=$dbname2", $username2, $password2);
if (!$conn2) {
    die("Connection to the second database failed: " . mysqli_connect_error());
} else {
    echo "Connected to the second database successfully.<br>";
}

// // Third database connection (blue_planet)
// $driver = "mysql";
// $host = "localhost";
// $username3 = "zigma";
// $password3 = "?WSzvxHv1LGZ";
// $databasename3 = "blue_planet";

// $conn3 = new PDO("$driver:host=$host;dbname=$databasename3", $username3, $password3);
// if (!$conn3) {
//     die("Connection to the third database failed: " . mysqli_connect_error());
// } else {
//     echo "Connected to the third database successfully.<br>";
// }

// // Fetch data from the second database
// $sql2 = $conn2->prepare("SELECT * FROM zigfly_recognized");
// $sql2->execute();
// $rows = $sql2->fetchAll(PDO::FETCH_ASSOC);

// // Insert data into the third database
// foreach ($rows as $record) {
//     // Check if the record already exists in the third database
//     $checkQuery = $conn3->prepare("
//         SELECT COUNT(*) 
//         FROM zigfly_recognized 
//         WHERE emp_id = :emp_id 
//           AND recognition_date = :recognition_date 
//           AND recognition_time = :recognition_time
//     ");
//     $checkQuery->execute([
//         ':emp_id' => $record['emp_id'],
//         ':recognition_date' => $record['recognition_date'],
//         ':recognition_time' => $record['recognition_time']
//     ]);
//     $nRows = $checkQuery->fetchColumn();

//     // If the record does not exist, insert it
//     if ($nRows == 0) {
//         $insertQuery = $conn3->prepare("
//             INSERT INTO zigfly_recognized (
//                 emp_id, 
//                 name, 
//                 records, 
//                 captured_image_path, 
//                 similarity_score, 
//                 latitude, 
//                 longitude, 
//                 recognition_date, 
//                 recognition_time
//             ) VALUES (
//                 :emp_id, 
//                 :name, 
//                 :records, 
//                 :captured_image_path, 
//                 :similarity_score, 
//                 :latitude, 
//                 :longitude, 
//                 :recognition_date, 
//                 :recognition_time
//             )
//         ");
//         $insertQuery->execute([
//             ':emp_id' => $record['emp_id'],
//             ':name' => $record['name'],
//             ':records' => $record['records'],
//             ':captured_image_path' => $record['captured_image_path'],
//             ':similarity_score' => $record['similarity_score'],
//             ':latitude' => $record['latitude'],
//             ':longitude' => $record['longitude'],
//             ':recognition_date' => $record['recognition_date'],
//             ':recognition_time' => $record['recognition_time']
//         ]);
//         echo "Inserted record for emp_id: " . $record['emp_id'] . "<br>";
//     } else {
//         echo "Record for emp_id: " . $record['emp_id'] . " already exists in the third database.<br>";
//     }
// }

// echo "Data transfer completed.";

?>
