<?php
include('../config/database.php');

// 🔴 CHECK IF TOKEN EXISTS
if(!isset($_GET['token']) || empty($_GET['token'])){
    die("Invalid access (No token)");
}

// 🔴 SANITIZE TOKEN
$token = mysqli_real_escape_string($conn, $_GET['token']);

if(isset($_POST['reset'])){

    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if($password != $confirm){
        echo "<script>alert('Passwords do not match');</script>";
        exit();
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    // 🔴 USE PHP TIME (FIXES MOST BUGS)
    $current_time = date("Y-m-d H:i:s");

    $query = mysqli_query($conn,
    "SELECT * FROM users 
     WHERE reset_token='$token' 
     AND reset_expiry > '$current_time'");

    if(mysqli_num_rows($query) > 0){

        mysqli_query($conn,
        "UPDATE users 
         SET password='$hash',
             reset_token=NULL,
             reset_expiry=NULL
         WHERE reset_token='$token'");

        echo "<script>
        alert('Password reset successful');
        window.location='login.php';
        </script>";

    } else {
        echo "<script>alert('Invalid or expired token');</script>";
    }
}
?>

<h2>Reset Password</h2>

<form method="POST">
    <input type="password" name="password" placeholder="New Password" required>
    <input type="password" name="confirm" placeholder="Confirm Password" required>
    <button name="reset">Reset Password</button>
</form>