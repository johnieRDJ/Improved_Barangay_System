<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'superadmin'){
    header("Location: ../auth/login.php");
    exit();
}

include('../config/database.php');

if(isset($_POST['add'])){

    $fname = trim($_POST['firstname'] ?? '');
    $lname = trim($_POST['lastname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);

    db_execute($conn,
    "INSERT INTO users (firstname, lastname, email, password, role, account_status)
     VALUES (?, ?, ?, ?, 'admin', 'approved')",
     'ssss',
     [$fname, $lname, $email, $password]);

    $user_id = mysqli_insert_id($conn);

    db_execute($conn,
    "INSERT INTO user_auth (user_id, email_verified)
     VALUES (?, 1)",
     'i',
     [$user_id]);

    db_execute($conn,
    "INSERT INTO user_profiles (user_id)
     VALUES (?)",
     'i',
     [$user_id]);

    db_execute($conn,
    "INSERT INTO residency (user_id, status)
     VALUES (?, 'verified')",
     'i',
     [$user_id]);

    db_execute($conn,
    "INSERT INTO logs (user_id, action)
     VALUES (?, ?)",
     'is',
     [intval($_SESSION['user_id']), 'Created new admin account']);

    header("Location: manage_admins.php");
    exit();
}

include('../includes/header.php');
include('../includes/sidebar.php');
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
