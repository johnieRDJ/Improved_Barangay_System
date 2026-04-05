<?php
if(!defined('APP_URL')){
    if(!empty($_SERVER['HTTP_HOST'])){
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $scriptPath = str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '')));
        $basePath = ($scriptPath === '/' || $scriptPath === '.') ? '' : rtrim($scriptPath, '/');
        define('APP_URL', $scheme . '://' . $_SERVER['HTTP_HOST'] . $basePath);
    } else {
        // CLI/local fallback
        define('APP_URL', 'http://localhost/barangay');
    }
}

$conn = mysqli_connect("localhost", "root", "", "barangay_db");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

include(__DIR__ . '/../includes/seed_superadmin.php');
?>
