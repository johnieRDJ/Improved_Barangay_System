<?php
session_start();
include('../config/database.php');

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

$id = intval($_GET['id'] ?? 0);
$form_error = '';

$user = db_select_one(
    $conn,
    "SELECT * FROM users WHERE user_id=? LIMIT 1",
    'i',
    [$id]
);

if(!$user){
    header("Location: manage_users.php");
    exit();
}

if($user['role'] == 'superadmin'){
    echo "<script>alert('Superadmin account is protected.'); window.location='manage_users.php';</script>";
    exit();
}

if(isset($_POST['update'])){
    $fname = trim($_POST['firstname'] ?? '');
    $lname = trim($_POST['lastname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $status = $_POST['account_status'] ?? 'pending';

    if($fname === '' || $lname === '' || $email === ''){
        $form_error = 'Please complete all fields.';
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $form_error = 'Please enter a valid email address.';
    } elseif(!in_array($status, ['pending', 'approved', 'rejected'], true)){
        $form_error = 'Invalid account status.';
    } else {
        db_execute($conn,
        "UPDATE users SET
         firstname=?,
         lastname=?,
         email=?,
         account_status=?
         WHERE user_id=? AND role!='superadmin'",
         'ssssi',
         [$fname, $lname, $email, $status, $id]);

        if($status == 'approved'){
            db_execute($conn,
            "INSERT INTO residency (user_id, status)
             SELECT ?, 'pending'
             WHERE NOT EXISTS (
                 SELECT 1 FROM residency WHERE user_id=?
             )",
             'ii',
             [$id, $id]);
        }

        db_execute($conn,
        "INSERT INTO logs (user_id, action)
         VALUES (?, ?)",
         'is',
         [intval($_SESSION['user_id']), "Updated user ID $id"]);

        header("Location: manage_users.php");
        exit();
    }
}

include('../includes/header.php');
include('../includes/sidebar.php');
?>

<h2>Edit User</h2>

<?php if($form_error !== ''): ?>
    <p style="color:#b91c1c; font-weight:600;"><?php echo htmlspecialchars($form_error); ?></p>
<?php endif; ?>

<form method="POST">

<input type="text" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>"><br><br>
<input type="text" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>"><br><br>
<input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"><br><br>

<select name="account_status">
    <option value="pending" <?php if($user['account_status']=='pending') echo 'selected'; ?>>Pending</option>
    <option value="approved" <?php if($user['account_status']=='approved') echo 'selected'; ?>>Approved</option>
    <option value="rejected" <?php if($user['account_status']=='rejected') echo 'selected'; ?>>Rejected</option>
</select><br><br>

<button name="update">Update</button>

</form>

<?php include('../includes/footer.php'); ?>
