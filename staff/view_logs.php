<?php
session_start();

include('../config/database.php');

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'staff'){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = intval($_SESSION['user_id']);

$logs = db_select_all(
    $conn,
    "SELECT logs.*, users.firstname, users.lastname
     FROM logs
     JOIN users ON logs.user_id = users.user_id
     WHERE logs.user_id = ?
     ORDER BY logs.log_time DESC",
    'i',
    [$user_id]
);

include('../includes/header.php');
include('../includes/sidebar.php');
?>

<h2>My Activity Logs</h2>

<table border="1" cellpadding="10" width="100%">

<tr>
    <th>Staff</th>
    <th>Action</th>
    <th>Date & Time</th>
</tr>

<?php foreach($logs as $row): ?>

<tr>

<td>
<?php echo htmlspecialchars($row['firstname']." ".$row['lastname']); ?>
</td>

<td>
<?php echo htmlspecialchars($row['action']); ?>
</td>

<td>
<?php echo htmlspecialchars($row['log_time']); ?>
</td>

</tr>

<?php endforeach; ?>

</table>

<?php include('../includes/footer.php'); ?>
