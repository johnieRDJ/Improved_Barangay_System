<?php
session_start();
include('../includes/header.php');

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'staff'){
    header("Location: ../auth/login.php");
    exit();
}
include('../config/database.php');
include('../includes/sidebar.php');

$user_id = intval($_SESSION['user_id']);

//  LOG: Staff opened dashboard
db_execute($conn,
"INSERT INTO logs (user_id, action)
 VALUES (?, ?)",
 'is',
 [$user_id, 'Opened staff dashboard']);

$totalRow = db_select_one($conn,
"SELECT COUNT(*) as total FROM complaints 
 WHERE assigned_staff_id=?",
 'i',
 [$user_id]);
$total_assigned = $totalRow['total'] ?? 0;

$progressRow = db_select_one($conn,
"SELECT COUNT(*) as total FROM complaints 
 WHERE assigned_staff_id=?
 AND status='In Progress'",
 'i',
 [$user_id]);
$in_progress = $progressRow['total'] ?? 0;

$resolvedRow = db_select_one($conn,
"SELECT COUNT(*) as total FROM complaints 
 WHERE assigned_staff_id=?
 AND status='Resolved'",
 'i',
 [$user_id]);
$resolved = $resolvedRow['total'] ?? 0;
?>

<div class="dashboard-wrapper page-shell">

    <div class="dashboard-header">
        <h1>Staff Dashboard</h1>
        <p>View and update assigned complaints here.</p>
    </div>

    <div class="stats-grid">

        <div class="stat-card">
            <h3><?php echo $total_assigned; ?></h3>
            <p>Total Assigned</p>
        </div>

        <div class="stat-card">
            <h3><?php echo $in_progress; ?></h3>
            <p>In Progress</p>
        </div>

        <div class="stat-card">
            <h3><?php echo $resolved; ?></h3>
            <p>Resolved</p>
        </div>

    </div>

</div>

<?php include('../includes/footer.php'); ?>
