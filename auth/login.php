<?php 
include('../config/database.php'); 
include('../includes/send_otp.php'); 
session_start(); 

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

    // ✅ JOIN users + user_auth (NORMALIZED)
    $query = mysqli_query($conn,
    "SELECT users.*, COALESCE(user_auth.email_verified, 0) AS email_verified
     FROM users
     LEFT JOIN user_auth 
     ON users.user_id = user_auth.user_id
     WHERE users.email='$email'");

    $user = mysqli_fetch_assoc($query);

    if($user && password_verify($password, $user['password'])){

        // 🔴 CHECK EMAIL VERIFIED
        if($user['email_verified'] == 0){
            echo "<script>alert('Please verify your email first!');</script>";
            exit();
        }

        // 🔴 CHECK ADMIN APPROVAL
        if($user['role'] != 'superadmin' && $user['account_status'] != 'approved'){
            echo "<script>alert('Account not approved by admin yet.');</script>";
            exit();
        }

        // ✅ Generate OTP
        $otp = rand(100000,999999);
        $expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));

        // ✅ UPDATE IN user_auth (NOT users anymore)
        mysqli_query($conn,
        "UPDATE user_auth 
         SET otp_code='$otp', otp_expiry='$expiry'
         WHERE user_id='".$user['user_id']."'");

        $_SESSION['temp_user'] = $user['user_id'];

        sendOTP($email, $otp);

        echo "<script>
        alert('OTP sent to your email.');
        window.location='verify_otp.php';
        </script>";

    } else {
        unset($_SESSION['temp_user']);
        echo "<script>alert('Invalid email or password');</script>";
    }
}
?>
