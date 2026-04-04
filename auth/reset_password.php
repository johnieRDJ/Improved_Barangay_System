<?php
session_start();
include('../config/database.php');

// 🔴 CHECK TOKEN
if(!isset($_GET['token']) || empty($_GET['token'])){
    die("Invalid access (No token)");
}

$token = mysqli_real_escape_string($conn, $_GET['token']);

// 🔴 GET RESET RECORD
$reset = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT * FROM password_resets 
 WHERE reset_token='$token'"));

if(!$reset){
    die("Invalid token");
}

// 🔴 CHECK EXPIRY
$current_time = date("Y-m-d H:i:s");

if($reset['reset_expiry'] < $current_time){
    die("Token expired");
}

$user_id = $reset['user_id'];
$user = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT password FROM users WHERE user_id='$user_id'"));

if(!$user){
    die("User not found");
}

if(isset($_POST['reset'])){

    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if($password != $confirm){
        echo "<script>alert('Passwords do not match');</script>";
        exit();
    }

    if(password_verify($password, $user['password'])){
        echo "<script>alert('Please choose a new password different from your old password');</script>";
        exit();
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    // 🔴 UPDATE PASSWORD IN user_auth
    mysqli_query($conn,
    "UPDATE users 
     SET password='$hash'
     WHERE user_id='$user_id'");

    mysqli_query($conn,
    "UPDATE user_auth
     SET otp_code=NULL, otp_expiry=NULL
     WHERE user_id='$user_id'");

    if(isset($_SESSION['temp_user']) && $_SESSION['temp_user'] == $user_id){
        unset($_SESSION['temp_user']);
    }

    // 🔴 DELETE TOKEN (IMPORTANT 🔥)
    mysqli_query($conn,
    "DELETE FROM password_resets 
     WHERE reset_token='$token'");

    // 🔴 LOG
    mysqli_query($conn,
    "INSERT INTO logs (user_id, action)
     VALUES ('$user_id','Reset password via email')");

    echo "<script>
    alert('Password reset successful');
    window.location='login.php';
    </script>";
}
?>

<h2>Reset Password</h2>

<form method="POST">
    <input type="password" name="password" placeholder="New Password" required>
    <input type="password" name="confirm" placeholder="Confirm Password" required>
    <button name="reset">Reset Password</button>
</form>
