<?php
include('../config/database.php');
include('../includes/send_reset.php');

if(isset($_POST['send'])){

    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // 🔴 CHECK USER
    $user = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT user_id FROM users WHERE email='$email'"));

    if($user){

        $user_id = $user['user_id'];

        // 🔴 GENERATE TOKEN
        $token = bin2hex(random_bytes(50));
        $expiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));

        // 🔴 DELETE OLD TOKENS (IMPORTANT 🔥)
        mysqli_query($conn,
        "DELETE FROM password_resets WHERE user_id='$user_id'");

        // 🔴 INSERT NEW TOKEN
        mysqli_query($conn,
        "INSERT INTO password_resets (user_id, reset_token, reset_expiry)
         VALUES ('$user_id','$token','$expiry')");

        // 🔴 SEND EMAIL
        sendResetLink($email, $token);

        echo "<script>alert('Reset link sent to your email');</script>";

    } else {
        echo "<script>alert('Email not found');</script>";
    }
}
?>

<h2>Forgot Password</h2>

<form method="POST">
    <input type="email" name="email" placeholder="Enter your email" required>
    <button name="send">Send Reset Link</button>
</form>
