<?php
include('../config/database.php');

$page_title = 'Verification Link';
$message = 'Invalid or expired verification link.';
$details = 'Please use the latest verification email or register again if needed.';

if(isset($_GET['token'])){

    $token = $_GET['token'];

    $user = db_select_one($conn,
    "SELECT user_auth.user_id, users.firstname, users.lastname
     FROM user_auth
     INNER JOIN users ON users.user_id = user_auth.user_id
     WHERE user_auth.verification_token=?
     LIMIT 1",
     's',
     [$token]);

    if($user){

        db_execute($conn,
        "UPDATE user_auth
         SET email_verified=1, verification_token=NULL
         WHERE verification_token=?",
         's',
         [$token]);

        $fullname = trim($user['firstname'] . ' ' . $user['lastname']);
        $page_title = 'Email Verified';
        $message = 'Thank you, ' . htmlspecialchars($fullname, ENT_QUOTES, 'UTF-8') . '. Your email has been verified successfully.';
        $details = 'Your account is now waiting for administrator approval. You can return to the login page, but you will only be able to sign in after approval.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $page_title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="container">
    <h2><?php echo $page_title; ?></h2>
    <p><?php echo $message; ?></p>
    <p><?php echo $details; ?></p>

    <div class="link">
        <a href="login.php">Back to Login</a>
    </div>
</div>

</body>
</html>
