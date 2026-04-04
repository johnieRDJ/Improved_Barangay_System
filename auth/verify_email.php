<?php
include('../config/database.php');

if(isset($_GET['token'])){

    $token = mysqli_real_escape_string($conn, $_GET['token']);

    $result = mysqli_query($conn,
    "SELECT * FROM user_auth WHERE verification_token='$token'");

    if(mysqli_num_rows($result) > 0){

        mysqli_query($conn,
        "UPDATE user_auth 
         SET email_verified=1, verification_token=NULL
         WHERE verification_token='$token'");

        echo "<script>
        alert('Email Verified! Wait for admin approval.');
        window.location='login.php';
        </script>";

    } else {
        echo "Invalid or expired token.";
    }
}
?>
