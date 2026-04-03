<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'superadmin'){
    header("Location: ../auth/login.php");
    exit();
}

include('../includes/header.php');
include('../includes/sidebar.php');
include('../config/database.php');

// COUNT ADMINS
$admins = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) as total FROM users WHERE role='admin'"))['total'];

// COUNT USERS
$users = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) as total FROM users WHERE role != 'superadmin'"))['total'];
?>

<h1>Superadmin Dashboard</h1>

<div class="cards">

    <div class="card">
        <h3><?php echo $admins; ?></h3>
        <p>Total Admins</p>
    </div>

    <div class="card">
        <h3><?php echo $users; ?></h3>
        <p>Total Users</p>
    </div>

</div>

<a href="manage_admins.php">Manage Admins</a><br>
<a href="system_logs.php">View System Logs</a><br>
<a href="../auth/logout.php">Logout</a>

<?php include('../includes/footer.php'); ?>