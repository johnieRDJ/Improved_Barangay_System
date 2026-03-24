<?php
include('../config/database.php');
include('../includes/send_reset.php'); // create this next

if(isset($_POST['send'])){

    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if(mysqli_num_rows($check) > 0){

        $token = bin2hex(random_bytes(50));
        $expiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));

        mysqli_query($conn,
        "UPDATE users 
         SET reset_token='$token', reset_expiry='$expiry'
         WHERE email='$email'");

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