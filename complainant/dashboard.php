<?php
session_start();
include('../includes/header.php');

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'complainant'){
    header("Location: ../auth/login.php");
    exit();
}
include('../config/database.php');
include('../includes/sidebar.php');

$user_id = intval($_SESSION['user_id']);

$totalRow = db_select_one($conn,
"SELECT COUNT(*) as total FROM complaints 
 WHERE complainant_id=?",
 'i',
 [$user_id]);
$total = $totalRow['total'] ?? 0;

$pendingRow = db_select_one($conn,
"SELECT COUNT(*) as total FROM complaints 
 WHERE complainant_id=?
 AND status='Pending'",
 'i',
 [$user_id]);
$pending = $pendingRow['total'] ?? 0;

$resolvedRow = db_select_one($conn,
"SELECT COUNT(*) as total FROM complaints 
 WHERE complainant_id=?
 AND status='Resolved'",
 'i',
 [$user_id]);
$resolved = $resolvedRow['total'] ?? 0;

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
