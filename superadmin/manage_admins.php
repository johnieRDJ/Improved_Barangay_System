<?php
session_start();

if($_SESSION['role'] != 'superadmin'){
    header("Location: ../auth/login.php");
    exit();
}

include('../config/database.php');
include('../includes/header.php');
include('../includes/sidebar.php');

// DELETE ADMIN
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);

    mysqli_query($conn,"DELETE FROM users WHERE user_id='$id' AND role='admin'");

    mysqli_query($conn,
    "INSERT INTO logs (user_id, action)
     VALUES ('".$_SESSION['user_id']."','Deleted admin ID $id')");

    header("Location: manage_admins.php");
    exit();
}

$result = mysqli_query($conn,
"SELECT * FROM users WHERE role='admin'");
?>

<h2>Manage Admins</h2>

<a href="add_admin.php">➕ Add Admin</a><br><br>

<table border="1" cellpadding="10">
<tr>
    <th>Name</th>
    <th>Email</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)): ?>
<tr>

<td><?php echo $row['firstname']." ".$row['lastname']; ?></td>
<td><?php echo $row['email']; ?></td>
<td><?php echo $row['account_status']; ?></td>

<td>
<a href="promote.php?id=<?php echo $row['user_id']; ?>">Make Admin</a>
<a href="edit_admin.php?id=<?php echo $row['user_id']; ?>">Edit</a> |
<a href="?delete=<?php echo $row['user_id']; ?>" onclick="return confirm('Delete admin?')">Delete</a>
</td>

</tr>
<?php endwhile; ?>
</table>

<?php include('../includes/footer.php'); ?>