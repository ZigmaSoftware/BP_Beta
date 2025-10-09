<?php
session_start();
date_default_timezone_set("Asia/Kolkata");
ini_set('max_execution_time', '0');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
error_reporting(0);

// Include Common Database Class
require_once 'dbclass.php';

// Local vs Production (auto-detect)
$whitelist = array('127.0.0.1', '::1');

if (!in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
    // --- Production Server ---
    $driver       = "mysql";
    $host         = "zigmaglobal.in";
    $username     = "zigmaglobal_new_user";
    $password     = "Bq3[1PYLs6q2";
    $databasename = "zigmaglo_erp";
} else {
    // --- Development Server ---
    $driver       = "mysql";
    $host         = "zigmaglobal.in";
    $username     = "zigmaglobal_new_user";
    $password     = "Bq3[1PYLs6q2";
    $databasename = "zigmaglo_erp";
}

// ⚡ IMPORTANT: use different variable names so we don't overwrite main $pdo
$pdo_w = new Db($driver, $host, $username, $password, $databasename); // <- note _w

try {
    $conn = new PDO(
        "$driver:host=$host;port=3306;dbname=$databasename;charset=utf8mb4",
        $username,
        $password
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Optional upload config
require_once "SecureUPload/src/autoloader.php";
try {
    $fileUploadConfig = new Alirdn\SecureUPload\Config\SecureUPloadConfig();
    $fileUploadConfig->setArray([
        'upload_folder' => dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR
    ]);
} catch (Alirdn\SecureUPload\Exceptions\UploadFolderException $exception) {
    echo "Exception: " . $exception->getMessage() . ' Code: ' . $exception->getCode();
    die();
}

// Common helper functions
require_once 'comfun.php';
file_upload_extention_lowercase_helper();
?>
