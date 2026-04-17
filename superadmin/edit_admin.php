<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'superadmin'){
    header("Location: ../auth/login.php");
    exit();
}

include('../config/database.php');

if(!isset($_GET['id'])){
    header("Location: manage_admins.php");
    exit();
}

$id = intval($_GET['id']);

$user = db_select_one($conn,
"SELECT * FROM users WHERE user_id=? AND role='admin' LIMIT 1",
'i',
[$id]);

if(!$user){
    header("Location: manage_admins.php");
    exit();
}

if(isset($_POST['save'])){

    $fname = trim($_POST['firstname'] ?? '');
    $lname = trim($_POST['lastname'] ?? '');
    $email = trim($_POST['email'] ?? '');

    db_execute($conn,
    "UPDATE users SET
     firstname=?,
     lastname=?,
     email=?
     WHERE user_id=? AND role='admin'",
     'sssi',
     [$fname, $lname, $email, $id]);

    db_execute($conn,
    "INSERT INTO logs (user_id, action)
     VALUES (?, ?)",
     'is',
     [intval($_SESSION['user_id']), "Updated admin ID $id"]);

    header("Location: manage_admins.php");
    exit();
}

include('../includes/header.php');
include('../includes/sidebar.php');
?>

<h2>Edit Admin</h2>

<form method="POST">
<input type="text" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>"><br><br>
<input type="text" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>"><br><br>
<input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"><br><br>

<button name="save">Save Changes</button>
</form>

<?php include('../includes/footer.php'); ?>
