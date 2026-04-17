<?php
session_start();
include('../config/database.php');

db_execute($conn,
"DELETE FROM password_resets
 WHERE reset_expiry < NOW()");

//  CHECK TOKEN
if(!isset($_GET['token']) || empty($_GET['token'])){
    die("Invalid access (No token)");
}

$token = $_GET['token'];

//  GET RESET RECORD
$reset = db_select_one($conn,
"SELECT * FROM password_resets 
 WHERE reset_token=?
 LIMIT 1",
 's',
 [$token]);

if(!$reset){
    die("Invalid token");
}

//  CHECK EXPIRY
$current_time = date("Y-m-d H:i:s");

if($reset['reset_expiry'] < $current_time){
    die("Token expired");
}

$user_id = intval($reset['user_id']);
$user = db_select_one(
    $conn,
    "SELECT password FROM users WHERE user_id=? LIMIT 1",
    'i',
    [$user_id]
);

if(!$user){
    die("User not found");
}

$reset_error = '';

if(isset($_POST['reset'])){

    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if($password != $confirm){
        $reset_error = 'Passwords do not match. Please type the same password in both fields.';
    }

    if($reset_error === '' && strlen($password) < 8){
        $reset_error = 'Password must be at least 8 characters long.';
    }

    if($reset_error === '' && password_verify($password, $user['password'])){
        $reset_error = 'Please choose a new password different from your old password.';
    }

    if($reset_error === ''){

    $hash = password_hash($password, PASSWORD_DEFAULT);

    //  UPDATE PASSWORD IN user_auth
    db_execute($conn,
    "UPDATE users 
     SET password=?
     WHERE user_id=?",
     'si',
     [$hash, $user_id]);

    db_execute($conn,
    "UPDATE user_auth
     SET otp_code=NULL,
         otp_expiry=NULL,
         failed_login_attempts=0,
         require_otp_until=NULL
     WHERE user_id=?",
     'i',
     [$user_id]);

    if(isset($_SESSION['temp_user']) && $_SESSION['temp_user'] == $user_id){
        unset($_SESSION['temp_user']);
    }

    //  DELETE TOKEN (IMPORTANT )
    db_execute($conn,
    "DELETE FROM password_resets
     WHERE user_id=?",
     'i',
     [$user_id]);

    //  LOG
    db_execute($conn,
    "INSERT INTO logs (user_id, action)
     VALUES (?, ?)",
     'is',
     [$user_id, 'Reset password via email']);

    echo "<script>
    alert('Password reset successful. You can now log in with your new password.');
    window.location='login.php';
    </script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="container">
    <h2>Reset Password</h2>

    <?php if($reset_error !== ''): ?>
        <p style="color:#b91c1c; font-weight:600;"><?php echo htmlspecialchars($reset_error, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="password" name="password" placeholder="New Password" required>
        <input type="password" name="confirm" placeholder="Confirm Password" required>
        <button name="reset">Reset Password</button>
    </form>
</div>

</body>
</html>
