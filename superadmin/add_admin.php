<?php
session_start();
include('../config/database.php');

if($_SESSION['role'] != 'superadmin'){
    header("Location: ../auth/login.php");
    exit();
}

if(isset($_POST['add'])){

    $fname = $_POST['firstname'];
    $lname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    mysqli_query($conn,
    "INSERT INTO users (firstname, lastname, email, password, role, account_status)
     VALUES ('$fname','$lname','$email','$password','admin','approved')");

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