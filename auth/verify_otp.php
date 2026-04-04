<?php 
include('../config/database.php'); 
session_start(); 

if(isset($_POST['verify'])){

    // 🔴 Session check
    if(!isset($_SESSION['temp_user'])){
        echo "<script>alert('Session expired. Please login again.'); window.location='login.php';</script>";
        exit();
    }

    $entered_otp = mysqli_real_escape_string($conn, $_POST['otp']);
    $user_id = $_SESSION['temp_user'];

    // ✅ JOIN users + user_auth
    $query = mysqli_query($conn, 
        "SELECT users.role, user_auth.otp_code, user_auth.otp_expiry
         FROM user_auth
         JOIN users ON user_auth.user_id = users.user_id
         WHERE user_auth.user_id='$user_id'");

    $user = mysqli_fetch_assoc($query);

    if($user){

        // 🔴 Check OTP + expiry
        if($entered_otp == $user['otp_code'] && strtotime($user['otp_expiry']) > time()){

            // ✅ Login success
            $_SESSION['user_id'] = $user_id;
            $_SESSION['role'] = $user['role'];
            unset($_SESSION['temp_user']);

            // 🔴 Clear OTP after use
            mysqli_query($conn,
            "UPDATE user_auth SET 
                otp_code=NULL,
                otp_expiry=NULL
             WHERE user_id='$user_id'");

            // 🔴 Log
            mysqli_query($conn, 
            "INSERT INTO logs (user_id, action) 
             VALUES ('$user_id','Logged in successfully with 2FA')");

            // 🔴 Redirect by role
            if($user['role'] == 'superadmin'){
                header("Location: ../superadmin/dashboard.php");
            }
            elseif($user['role'] == 'admin'){
                header("Location: ../admin/dashboard.php");
            }
            elseif($user['role'] == 'staff'){
                header("Location: ../staff/dashboard.php");
            }
            else{
                header("Location: ../complainant/dashboard.php");
            }

            exit();

        } else {
            echo "<script>alert('Invalid or expired OTP');</script>";
        }

    } else {
        echo "<script>alert('User not found');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="container">
    <h2>Verify OTP</h2>

    <form method="POST">
        <input type="text" name="otp" placeholder="Enter 6-digit OTP" required>
        <button type="submit" name="verify">Verify</button>
    </form>
</div>

</body>
</html>
