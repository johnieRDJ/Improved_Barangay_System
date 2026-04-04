<?php
session_start();
include('../config/database.php');

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

$id = intval($_GET['id']);

$user = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT * FROM users WHERE user_id='$id'"));

if(!$user){
    header("Location: manage_users.php");
    exit();
}

if($user['role'] == 'superadmin'){
    echo "<script>alert('Superadmin account is protected.'); window.location='manage_users.php';</script>";
    exit();
}

if(isset($_POST['update'])){

    $fname = $_POST['firstname'];
    $lname = $_POST['lastname'];
    $email = $_POST['email'];
    $status = $_POST['account_status'];

    mysqli_query($conn,
    "UPDATE users SET
     firstname='$fname',
     lastname='$lname',
     email='$email',
     account_status='$status'
     WHERE user_id='$id' AND role!='superadmin'");

    if($status == 'approved'){
        mysqli_query($conn,
        "INSERT INTO residency (user_id, status)
         SELECT '$id', 'pending'
         WHERE NOT EXISTS (
             SELECT 1 FROM residency WHERE user_id='$id'
         )");
    }

    mysqli_query($conn,
    "INSERT INTO logs (user_id, action)
     VALUES ('".$_SESSION['user_id']."','Updated user ID $id')");

    header("Location: manage_users.php");
}
?>

<h2>Edit User</h2>

<form method="POST">

<input type="text" name="firstname" value="<?php echo $user['firstname']; ?>"><br><br>
<input type="text" name="lastname" value="<?php echo $user['lastname']; ?>"><br><br>
<input type="email" name="email" value="<?php echo $user['email']; ?>"><br><br>

<select name="account_status">
    <option value="pending" <?php if($user['account_status']=='pending') echo 'selected'; ?>>Pending</option>
    <option value="approved" <?php if($user['account_status']=='approved') echo 'selected'; ?>>Approved</option>
    <option value="rejected" <?php if($user['account_status']=='rejected') echo 'selected'; ?>>Rejected</option>
</select><br><br>

<button name="update">Update</button>

</form>
