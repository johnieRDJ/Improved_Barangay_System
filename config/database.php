<?php
if(!defined('APP_URL')){
    // Change this when you deploy online.
    // Example: https://yourdomain.com/barangay
    define('APP_URL', 'http://localhost/barangay');
}

$conn = mysqli_connect("localhost", "root", "", "barangay_db");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

include(__DIR__ . '/../includes/seed_superadmin.php');
?>
