<?php 
include('../config/database.php'); 
session_start(); 

if(isset($_POST['verify'])){

    //  Session check
    if(!isset($_SESSION['temp_user'])){
        echo "<script>alert('Session expired. Please login again.'); window.location='login.php';</script>";
        exit();
    }

    $entered_otp = trim($_POST['otp'] ?? '');
    $user_id = intval($_SESSION['temp_user']);

    //  JOIN users + user_auth
    $user = db_select_one($conn,
        "SELECT
            users.role,
            users.account_status,
            user_auth.email_verified,
            user_auth.otp_code,
            user_auth.otp_expiry
         FROM user_auth
         JOIN users ON user_auth.user_id = users.user_id
         WHERE user_auth.user_id=?
         LIMIT 1",
         'i',
         [$user_id]);

    if($user){

        //  Check OTP + expiry
        if($entered_otp == $user['otp_code'] && strtotime($user['otp_expiry']) > time()){

            if(intval($user['email_verified']) !== 1){
                unset($_SESSION['temp_user']);
                db_execute($conn,
                "UPDATE user_auth
                 SET otp_code=NULL,
                     otp_expiry=NULL
                 WHERE user_id=?",
                 'i',
                 [$user_id]);

                echo "<script>alert('Please verify your email first before logging in.'); window.location='login.php';</script>";
                exit();
            }

            if($user['role'] != 'superadmin' && $user['account_status'] != 'approved'){
                unset($_SESSION['temp_user']);
                db_execute($conn,
                "UPDATE user_auth
                 SET otp_code=NULL,
                     otp_expiry=NULL
                 WHERE user_id=?",
                 'i',
                 [$user_id]);

                echo "<script>alert('Your account is still waiting for admin approval.'); window.location='login.php';</script>";
                exit();
            }

            //  Login success
            $_SESSION['user_id'] = $user_id;
            $_SESSION['role'] = $user['role'];
            unset($_SESSION['temp_user']);

            //  Clear OTP after use
            db_execute($conn,
            "UPDATE user_auth SET 
                otp_code=NULL,
                otp_expiry=NULL,
                failed_login_attempts=0,
                require_otp_until=NULL
             WHERE user_id=?",
             'i',
             [$user_id]);

            //  Log
            db_execute($conn,
            "INSERT INTO logs (user_id, action) 
             VALUES (?, ?)",
             'is',
             [$user_id, 'Logged in successfully after OTP verification']);

            //  Redirect by role
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="container">
    <h2>Verify OTP</h2>

    <form method="POST">
        <input type="text" name="otp" placeholder="Enter 6-digit OTP" required>
        <button type="submit" name="verify">Verify</button>
    </form>

    <div class="link">
        <a href="login.php">Back to Login</a>
    </div>
</div>

</body>
</html>
