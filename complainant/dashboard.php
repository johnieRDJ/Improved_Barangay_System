<?php
session_start();
include('../includes/header.php');

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'complainant'){
    header("Location: ../auth/login.php");
    exit();
}
include('../config/database.php');
include('../includes/sidebar.php');

$user_id = $_SESSION['user_id'];

$total = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) as total FROM complaints 
 WHERE complainant_id='$user_id'"))['total'];

$pending = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) as total FROM complaints 
 WHERE complainant_id='$user_id'
 AND status='pending'"))['total'];

$resolved = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) as total FROM complaints 
 WHERE complainant_id='$user_id'
 AND status='resolved'"))['total'];

?>

<div class="dashboard-wrapper page-shell">

    <div class="dashboard-header">
        <h1>Complainant Dashboard</h1>
        <p>Submit and track your complaints here.</p>
    </div>

    <div class="stats-grid">

        <div class="stat-card">
            <h3><?php echo $total; ?></h3>
            <p>My Total Complaints</p>
        </div>

        <div class="stat-card">
            <h3><?php echo $pending; ?></h3>
            <p>Pending</p>
        </div>

        <div class="stat-card">
            <h3><?php echo $resolved; ?></h3>
            <p>Resolved</p>
        </div>

    </div>

</div>

<?php include('../includes/footer.php'); ?>
