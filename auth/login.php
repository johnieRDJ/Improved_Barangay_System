<?php 
include('../config/database.php'); 
include('../includes/send_otp.php'); 
session_start(); 

function dashboardPathForRole($role){
    if($role == 'superadmin'){
        return '../superadmin/dashboard.php';
    }
    if($role == 'admin'){
        return '../admin/dashboard.php';
    }
    if($role == 'staff'){
        return '../staff/dashboard.php';
    }

    return '../complainant/dashboard.php';
}

if(isset($_SESSION['user_id'])){

    if($_SESSION['role'] == 'superadmin'){
        header("Location: ../superadmin/dashboard.php");
    }
    elseif($_SESSION['role'] == 'admin'){
        header("Location: ../admin/dashboard.php");
    }
    elseif($_SESSION['role'] == 'staff'){
        header("Location: ../staff/dashboard.php");
    }
    else{
        header("Location: ../complainant/dashboard.php");
    }

    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="container">
    <h2>Login</h2>

    <form method="POST">
        
        <input type="email" name="email" placeholder="Email" required>

        <input type="password" name="password" placeholder="Password" required>

        <button type="submit" name="login">Login</button>

    </form>

    <div class="link">
        <a href="forgot_password.php">Forgot Password?</a>
    </div>
    
    <div class="link">
        <a href="register.php">Create an account</a>
    </div>
</div>

</body>
</html>

<?php
if(isset($_POST['login'])){

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    //  JOIN users + user_auth (NORMALIZED)
    $query = mysqli_query($conn,
    "SELECT users.*,
            COALESCE(user_auth.email_verified, 0) AS email_verified,
            COALESCE(user_auth.failed_login_attempts, 0) AS failed_login_attempts,
            user_auth.require_otp_until
     FROM users
     LEFT JOIN user_auth 
     ON users.user_id = user_auth.user_id
     WHERE users.email='$email'");

    $user = mysqli_fetch_assoc($query);

    if($user && password_verify($password, $user['password'])){

        //  CHECK EMAIL VERIFIED
        if($user['email_verified'] == 0){
            echo "<script>alert('Please verify your email first!');</script>";
            exit();
        }

        //  CHECK ADMIN APPROVAL
        if($user['role'] != 'superadmin' && $user['account_status'] != 'approved'){
            echo "<script>alert('Account not approved by admin yet.');</script>";
            exit();
        }

        //  Generate OTP when the account is temporarily challenged
        $failedAttempts = intval($user['failed_login_attempts']);
        $otpRequiredUntil = $user['require_otp_until'];
        $requiresOtp = $failedAttempts >= 3
            || (!empty($otpRequiredUntil) && strtotime($otpRequiredUntil) > time());

        if($requiresOtp){
            $otp = rand(100000,999999);
            $expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));

            //  UPDATE IN user_auth (NOT users anymore)
            mysqli_query($conn,
            "UPDATE user_auth 
             SET otp_code='$otp',
                 otp_expiry='$expiry',
                 require_otp_until=DATE_ADD(NOW(), INTERVAL 15 MINUTE)
             WHERE user_id='".$user['user_id']."'");

            $_SESSION['temp_user'] = $user['user_id'];

            sendOTP($email, $otp);

            echo "<script>
            alert('Multiple failed login attempts were detected. An OTP was sent to your email to verify that it is really you.');
            window.location='verify_otp.php';
            </script>";
            exit();
        }

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
        unset($_SESSION['temp_user']);

        mysqli_query($conn,
        "UPDATE user_auth
         SET failed_login_attempts=0,
             require_otp_until=NULL,
             otp_code=NULL,
             otp_expiry=NULL
         WHERE user_id='".$user['user_id']."'");

        mysqli_query($conn,
        "INSERT INTO logs (user_id, action)
         VALUES ('".$user['user_id']."','Logged in successfully')");

        $dashboardPath = dashboardPathForRole($user['role']);
        echo "<script>
        window.location='$dashboardPath';
        </script>";
        exit();

    } else {
        unset($_SESSION['temp_user']);

        $failedMessage = 'Invalid email or password';

        if($user){
            $userId = intval($user['user_id']);
            $newFailedAttempts = intval($user['failed_login_attempts']) + 1;

            mysqli_query($conn,
            "UPDATE user_auth
             SET failed_login_attempts=failed_login_attempts + 1,
                 require_otp_until=CASE
                     WHEN failed_login_attempts + 1 >= 3 THEN DATE_ADD(NOW(), INTERVAL 15 MINUTE)
                     ELSE require_otp_until
                 END
             WHERE user_id='$userId'");

            if($newFailedAttempts >= 3){
                if(intval($user['email_verified']) !== 1){
                    echo "<script>alert('Please verify your email first before logging in.');</script>";
                    exit();
                }

                if($user['role'] != 'superadmin' && $user['account_status'] != 'approved'){
                    echo "<script>alert('Your account is still waiting for admin approval.');</script>";
                    exit();
                }

                $otp = rand(100000,999999);
                $expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));

                mysqli_query($conn,
                "UPDATE user_auth
                 SET otp_code='$otp',
                     otp_expiry='$expiry',
                     require_otp_until=DATE_ADD(NOW(), INTERVAL 15 MINUTE)
                 WHERE user_id='$userId'");

                $_SESSION['temp_user'] = $userId;
                sendOTP($user['email'], $otp);

                echo "<script>
                alert('Multiple failed login attempts detected. An OTP was sent to your email.');
                window.location='verify_otp.php';
                </script>";
                exit();
            }
        }

        echo "<script>alert('$failedMessage');</script>";
    }
}
?>
