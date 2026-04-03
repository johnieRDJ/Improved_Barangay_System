<?php
session_start();
include('../config/database.php');

if($_SESSION['role'] != 'superadmin'){
    header("Location: ../auth/login.php");
    exit();
}

$id = $_GET['id'];

$user = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT * FROM users WHERE user_id='$id'"));

if(isset($_POST['save'])){

    $fname = $_POST['firstname'];
    $lname = $_POST['lastname'];
    $email = $_POST['email'];

    mysqli_query($conn,
    "UPDATE users SET
     firstname='$fname',
     lastname='$lname',
     email='$email'
     WHERE user_id='$id'");

    mysqli_query($conn,
    "INSERT INTO logs (user_id, action)
     VALUES ('".$_SESSION['user_id']."','Updated admin ID $id')");

    header("Location: manage_admins.php");
}
?>

<h2>Edit Admin</h2>

<form method="POST">
<input type="text" name="firstname" value="<?php echo $user['firstname']; ?>"><br><br>
<input type="text" name="lastname" value="<?php echo $user['lastname']; ?>"><br><br>
<input type="email" name="email" value="<?php echo $user['email']; ?>"><br><br>

<button name="save">Save Changes</button>
</form>