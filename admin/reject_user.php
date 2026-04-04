<?php
session_start();

include('../config/database.php');
include('../includes/send_account_status.php');

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

$id = intval($_GET['id']); // safer

$target = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT role FROM users WHERE user_id='$id'"));

if($target && $target['role'] == 'superadmin'){
    echo "<script>alert('Superadmin account is protected.'); window.location='manage_users.php';</script>";
    exit();
}

// Reject the user
mysqli_query($conn, 
"UPDATE users 
 SET account_status='rejected'
 WHERE user_id='$id' AND role!='superadmin'");

// Get user information
$result = mysqli_query($conn,"SELECT firstname, lastname, email FROM users WHERE user_id='$id'");
$user = mysqli_fetch_assoc($result);

if(!$user){
    header("Location: manage_users.php");
    exit();
}

// Combine first and last name
$fullname = $user['firstname']." ".$user['lastname'];

// Send rejection email
sendAccountStatus($user['email'], $fullname, "rejected");

// Save log
mysqli_query($conn, 
"INSERT INTO logs (user_id, action)
 VALUES ('".$_SESSION['user_id']."',
 'Rejected user ID $id')");

$_SESSION['status_message'] = $fullname . " has been rejected.";

// Redirect
header("Location: manage_users.php");
exit();
?>



