<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'superadmin'){
    header("Location: ../auth/login.php");
    exit();
}

include('../config/database.php');
include('../includes/header.php');
include('../includes/sidebar.php');

$logs = db_select_all($conn,
"SELECT logs.*, users.firstname, users.lastname
 FROM logs
 LEFT JOIN users ON logs.user_id = users.user_id
 ORDER BY log_time DESC");
?>

<h2>System Logs</h2>

<table border="1" cellpadding="10" width="100%">
<tr>
    <th>User</th>
    <th>Action</th>
    <th>Date</th>
</tr>

<?php foreach($logs as $row): ?>
<tr>

<td>
<?php 
echo $row['firstname'] 
? htmlspecialchars($row['firstname']." ".$row['lastname'])
: "System"; 
?>
</td>

<td><?php echo htmlspecialchars($row['action']); ?></td>

<td><?php echo htmlspecialchars($row['log_time']); ?></td>

</tr>
<?php endforeach; ?>

</table>

<?php include('../includes/footer.php'); ?>
