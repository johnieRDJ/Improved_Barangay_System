<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'superadmin'){
    header("Location: ../auth/login.php");
    exit();
}

include('../config/database.php');
include('../includes/header.php');
include('../includes/sidebar.php');

if(isset($_POST['add'])){

    $fname = $_POST['firstname'];
    $lname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    mysqli_query($conn,
    "INSERT INTO users (firstname, lastname, email, password, role, account_status)
     VALUES ('$fname','$lname','$email','$password','admin','approved')");

    $user_id = mysqli_insert_id($conn);

    mysqli_query($conn,
    "INSERT INTO user_auth (user_id, email_verified)
     VALUES ('$user_id',1)");

    mysqli_query($conn,
    "INSERT INTO user_profiles (user_id)
     VALUES ('$user_id')");

    mysqli_query($conn,
    "INSERT INTO residency (user_id, status)
     VALUES ('$user_id','verified')");

    mysqli_query($conn,
    "INSERT INTO logs (user_id, action)
     VALUES ('".$_SESSION['user_id']."','Created new admin account')");

    header("Location: manage_admins.php");
}
?>

<h2>Add Admin</h2>

<form method="POST">
<input type="text" name="firstname" placeholder="First Name" required><br><br>
<input type="text" name="lastname" placeholder="Last Name" required><br><br>
<input type="email" name="email" placeholder="Email" required><br><br>
<input type="password" name="password" placeholder="Password" required><br><br>

<button name="add">Create Admin</button>
</form>

<?php include('../includes/footer.php'); ?>
