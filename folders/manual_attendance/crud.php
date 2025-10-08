<?php
header("Content-Type: application/json");

ini_set('display_errors', 1);
error_reporting(E_ALL);

include "../../config/dbconfig.php";   // ERP main


$action = $_POST['action'] ?? '';
$response = ["status" => 0, "error" => "Invalid action"];

switch ($action) {

    case "createupdate":

        $emp_id    = $_SESSION['staff_id'] ?? 'UNKNOWN';
        $name      = $_SESSION['staff_name'] ?? 'Unknown User';
        $latitude  = $_POST['latitude']  ?? '';
        $longitude = $_POST['longitude'] ?? '';
        $imageData = $_POST['image']     ?? '';

        if (!$latitude || !$longitude || !$imageData) {
            $response['error'] = "Missing image or location data.";
            break;
        }

        // ---------- IMAGE SAVE ----------
        $folder = "../../uploads/manual_attendance/";
        if (!is_dir($folder)) mkdir($folder, 0777, true);

        $file_name = "manual_{$emp_id}_" . date("Ymd_His") . ".jpg";
        $file_path = $folder . $file_name;

        $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);

        if (!file_put_contents($file_path, base64_decode($imageData))) {
            $response['error'] = "Failed to save image file.";
            break;
        }

        // ---------- DATETIME FIELDS ----------
        $records           = date("Y-m-d H:i:s");
        $recognition_date  = date("Y-m-d");
        $recognition_time  = date("H:i:s");

        try {
            // ---------- ATTENDANCE DB INSERT ----------
           $sql = "INSERT INTO zigfly_recognized 
                    (emp_id, name, records, captured_image_path, similarity_score, latitude, longitude, recognition_date, recognition_time)
                    VALUES (:emp_id, :name, :records, :captured_image_path, :similarity_score, :latitude, :longitude, :recognition_date, :recognition_time)";
            
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute([
                ':emp_id' => $emp_id,
                ':name' => $name,
                ':records' => $records,
                ':captured_image_path' => $file_name,
                ':similarity_score' => 1.0,
                ':latitude' => $latitude,
                ':longitude' => $longitude,
                ':recognition_date' => $recognition_date,
                ':recognition_time' => $recognition_time
            ]);

            
            error_log(print_r($result, true) . "\n", 3, "result.log");

            $response = [
                "status"  => 1,
                "message" => "✅ Manual attendance recorded successfully",
                "file"    => $file_name,
                "lat"     => $latitude,
                "lon"     => $longitude,
                "time"    => $records
            ];

        } catch (PDOException $e) {
            $response = [
                "status" => 0,
                "error"  => "❌ Insert failed: " . $e->getMessage()
            ];
        }

        break;
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>
