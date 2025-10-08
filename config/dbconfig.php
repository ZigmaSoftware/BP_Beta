<?php
session_start();
date_default_timezone_set("Asia/Kolkata");   //India time (GMT+5:30)
ini_set('max_execution_time', '0');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
error_reporting(0);
// Import Database Common Class file
require 'dbclass.php';


// Production Server Auto Detect PHP function Start
$whitelist = array(
    '127.0.0.1',
    '::1'
);

if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
    // Production Server Configuration
    $driver         = "mysql";
    $host           = "localhost";
    $username       = "zigma";
    $password       = "?WSzvxHv1LGZ";
    $databasename   = "blue_planet_beta";    
} else {
    // Development Server Configuration
    $driver         = "mysql";
    $host           = "localhost";
    $username       = "zigma";
    $password       = "?WSzvxHv1LGZ";
    $databasename   = "blue_planet_beta";
}

$pdo            = new Db($driver, $host, $username, $password, $databasename);

try {
    $conn = new PDO( $driver.":host=".$host.";port=3306;dbname=".$databasename, $username, $password);

    $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 }
 catch(PDOException $e)
 {
     echo $sql . "<br>" . $e->getMessage();
 }  

require_once "SecureUPload/src/autoloader.php";

// File Upload Configuration
try {

    $fileUploadConfig = new Alirdn\SecureUPload\Config\SecureUPloadConfig();

    // Upload Configuration
    // Below Configuration over writes default configuration
    $fileUploadConfig->setArray(
        array(
            'upload_folder' => dirname( __FILE__ ,2) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR
        )
    );
    // Current Upload Directiry [upload_folder] => C:\xampp\htdocs\ascent\uploads\



    // More configuration Details see config/SecureUPload/src/config/SecureUPloadConfig.php file 

} catch ( Alirdn\SecureUPload\Exceptions\UploadFolderException $exception ) {
    echo "Exception: " . $exception->getMessage() . ' Code: ' . $exception->getCode() . ' Note: For more information check php error_log.';
    die();
}

// Common Functions Import 
require_once 'comfun.php';

// Call this Function Before any File Upload
// This Function located in comfun.php
file_upload_extention_lowercase_helper();

// Production Server Auto Detect PHP function End

?>