<?php
session_start();

include('../config/database.php');

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

$id = intval($_GET['id']);

$target = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT firstname, lastname, role FROM users WHERE user_id='$id'"));

if(!$target){
    header("Location: manage_users.php");
    exit();
}

if($target['role'] == 'superadmin'){
    echo "<script>alert('Superadmin account is protected.'); window.location='manage_users.php';</script>";
    exit();
}

mysqli_query($conn,
"UPDATE residency
 SET status='verified'
 WHERE user_id='$id'");

if(mysqli_affected_rows($conn) == 0){
    mysqli_query($conn,
    "INSERT INTO residency (user_id, status)
     SELECT '$id', 'verified'
     WHERE NOT EXISTS (
         SELECT 1 FROM residency WHERE user_id='$id'
     )");
}

$fullname = $target['firstname'] . " " . $target['lastname'];

mysqli_query($conn,
"INSERT INTO logs (user_id, action)
 VALUES ('".$_SESSION['user_id']."','Verified residency for user ID $id')");

$_SESSION['status_message'] = "Residency for " . $fullname . " has been verified.";

header("Location: manage_users.php");
exit();
?>
