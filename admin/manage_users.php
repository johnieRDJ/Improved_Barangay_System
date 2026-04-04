<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

include('../config/database.php');
include('../includes/header.php');
include('../includes/sidebar.php');



// ============================
// DELETE USER
// ============================
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);

    $target = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT role FROM users WHERE user_id='$id'"));

    if($target && $target['role'] == 'superadmin'){
        echo "<script>alert('Superadmin account is protected.'); window.location='manage_users.php';</script>";
        exit();
    }

    mysqli_query($conn,"DELETE FROM users WHERE user_id='$id' AND role!='superadmin'");

    mysqli_query($conn,
    "INSERT INTO logs (user_id, action)
     VALUES ('".$_SESSION['user_id']."','Deleted user ID $id')");

    header("Location: manage_users.php");
    exit();
}

// ============================
// SEARCH + FILTER FUNCTION
// ============================
$search = "";
$role_filter = "";
$status_filter = "";
$residency_filter = "";

if(isset($_GET['search'])){
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

if(isset($_GET['role'])){
    $role_filter = mysqli_real_escape_string($conn, $_GET['role']);
}

if(isset($_GET['account_status'])){
    $status_filter = mysqli_real_escape_string($conn, $_GET['account_status']);
}

if(isset($_GET['residency'])){
    $residency_filter = mysqli_real_escape_string($conn, $_GET['residency']);
}

$where_conditions = ["1=1"];

if($search != ""){
    $where_conditions[] = "(
        users.firstname LIKE '%$search%'
        OR users.lastname LIKE '%$search%'
        OR users.email LIKE '%$search%'
    )";
}

if($role_filter != ""){
    $where_conditions[] = "users.role='$role_filter'";
}

if($status_filter != ""){
    $where_conditions[] = "users.account_status='$status_filter'";
}

if($residency_filter == "none"){
    $where_conditions[] = "(residency.status IS NULL OR residency.status='')";
}
elseif($residency_filter != ""){
    $where_conditions[] = "residency.status='$residency_filter'";
}

$where_sql = implode(" AND ", $where_conditions);

$result = mysqli_query($conn,
"SELECT users.*, residency.status AS residency_status
 FROM users
 LEFT JOIN residency ON users.user_id = residency.user_id
 WHERE $where_sql
 ORDER BY users.role, users.lastname, users.firstname");

$status_message = "";
if(isset($_SESSION['status_message'])){
    $status_message = $_SESSION['status_message'];
    unset($_SESSION['status_message']);
}
?>

<?php if($status_message != ""): ?>
<script>
window.addEventListener('DOMContentLoaded', function () {
    alert(<?php echo json_encode($status_message); ?>);
});
</script>
<?php endif; ?>

<div class="page-shell">

<div class="page-header-row">
    <div class="page-title-block">
        <h1>Manage Users</h1>
        <p>Search the directory and manage account approval separately from residency verification.</p>
    </div>
</div>

<form method="GET" class="filters-bar">
    <div class="filter-group filter-search">
        <label for="search">Search</label>
        <input type="text" id="search" name="search" placeholder="Search name or email..." value="<?php echo htmlspecialchars($search); ?>">
    </div>

    <div class="filter-group">
        <label for="role">Role</label>
        <select name="role" id="role">
            <option value="">All Roles</option>
            <option value="superadmin" <?php echo ($role_filter == 'superadmin') ? 'selected' : ''; ?>>superadmin</option>
            <option value="admin" <?php echo ($role_filter == 'admin') ? 'selected' : ''; ?>>admin</option>
            <option value="staff" <?php echo ($role_filter == 'staff') ? 'selected' : ''; ?>>staff</option>
            <option value="complainant" <?php echo ($role_filter == 'complainant') ? 'selected' : ''; ?>>complainant</option>
        </select>
    </div>

    <div class="filter-group">
        <label for="account_status">Account Status</label>
        <select name="account_status" id="account_status">
            <option value="">All Status</option>
            <option value="approved" <?php echo ($status_filter == 'approved') ? 'selected' : ''; ?>>approved</option>
            <option value="pending" <?php echo ($status_filter == 'pending') ? 'selected' : ''; ?>>pending</option>
            <option value="rejected" <?php echo ($status_filter == 'rejected') ? 'selected' : ''; ?>>rejected</option>
        </select>
    </div>

    <div class="filter-group">
        <label for="residency">Residency</label>
        <select name="residency" id="residency">
            <option value="">All Residency</option>
            <option value="verified" <?php echo ($residency_filter == 'verified') ? 'selected' : ''; ?>>verified</option>
            <option value="pending" <?php echo ($residency_filter == 'pending') ? 'selected' : ''; ?>>pending</option>
            <option value="none" <?php echo ($residency_filter == 'none') ? 'selected' : ''; ?>>none</option>
        </select>
    </div>

    <div class="filter-group filter-actions">
        <div class="filter-primary-action">
            <button type="submit">Apply</button>
        </div>
        <div class="filter-secondary-actions">
            <a href="manage_users.php" class="page-action secondary-action">Clear Filters</a>
            <a href="add_user.php" class="page-action add-staff-action">Add Staff</a>
        </div>
    </div>
</form>

<div class="table-card">
<table border="1" cellpadding="10" width="100%" class="users-table responsive-table">
<tr>
    <th>Name</th>
    <th>Email</th>
    <th>Role</th>
    <th>Residency</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)): ?>
<tr>

<td><?php echo $row['firstname']." ".$row['lastname']; ?></td>

<td><?php echo $row['email']; ?></td>

<td><?php echo $row['role']; ?></td>



<td><?php $residency = $row['residency_status'] ?? 'none';

if($residency == 'verified'){
    echo "<span style='color:green;'>Verified</span>";
}
elseif($residency == 'pending'){
    echo "<span style='color:orange;'>Pending</span>";
}
else{
    echo "<span style='color:red;'>Not Verified</span>";
}
?>
</td>

<td>
<?php
if($row['account_status'] == 'approved'){
    echo "<span class='status-badge status-approved'>Approved</span>";
}
elseif($row['account_status'] == 'pending'){
    echo "<span class='status-badge status-pending'>Pending</span>";
}
else{
    echo "<span class='status-badge status-rejected'>Rejected</span>";
}
?>
</td>

<td class="action-cell">
    <div class="action-links">
    <?php if($row['role'] == 'superadmin'): ?>
        <span style="color:#8b5e00; font-weight:bold;">Protected</span>
    <?php else: ?>
        <a href="edit_user.php?id=<?php echo $row['user_id']; ?>">Edit</a>
        <a href="manage_users.php?delete=<?php echo $row['user_id']; ?>"
        onclick="return confirm('Delete this user?')">Delete</a>

        <?php if($row['account_status'] == 'pending'): ?>
            <a href="approve_user.php?id=<?php echo $row['user_id']; ?>">Approve</a>
            <a href="reject_user.php?id=<?php echo $row['user_id']; ?>">Reject</a>
        <?php elseif($row['account_status'] == 'approved' && $residency != 'verified'): ?>
            <a href="schedule_appointment.php?id=<?php echo $row['user_id']; ?>">Schedule</a>
            <a href="verify_residency.php?id=<?php echo $row['user_id']; ?>">Verify Residency</a>
        <?php elseif($row['account_status'] == 'approved' && $residency == 'verified'): ?>
            <span class="table-muted">Residency Verified</span>
        <?php else: ?>
            <span class="table-muted">No Action</span>
        <?php endif; ?>
    <?php endif; ?>
    </div>
</td>

</tr>
<?php endwhile; ?>

</table>
</div>

</div>

<?php include('../includes/footer.php'); ?>
