<div class="sidebar">
    <h2 class="sidebar-brand">Barangay Digital Complaint Desk System</h2>

    <?php if($_SESSION['role'] == 'superadmin'): ?>

        <a href="../superadmin/dashboard.php">Dashboard</a>
        <a href="../superadmin/manage_admins.php">Manage Admins</a>
        <a href="../superadmin/add_admin.php">Add Admin</a>
        <a href="../admin/profile.php">My Profile</a>
        <a href="../superadmin/system_logs.php">System Logs</a>

    <?php elseif($_SESSION['role'] == 'admin'): ?>

        <a href="../admin/profile.php">My Profile</a>
        <a href="../admin/dashboard.php">Dashboard</a>
        <a href="../admin/manage_users.php">Manage Users</a>
        <a href="../admin/manage_complaints.php">Manage Complaints</a>
        <a href="../admin/view_logs.php">System Logs</a>
        <a href="../admin/view_profiles.php">User Profiles</a>

    <?php elseif($_SESSION['role'] == 'staff'): ?>

        <a href="../staff/profile.php">My Profile</a>
        <a href="../staff/dashboard.php">Dashboard</a>
        <a href="../staff/view_complaints.php">View Complaints</a>
        <a href="../staff/view_logs.php">My Logs</a>

    <?php elseif($_SESSION['role'] == 'complainant'): ?>

        <a href="../complainant/profile.php">My Profile</a>
        <a href="../complainant/dashboard.php">Dashboard</a>
        <a href="../complainant/create_complaint.php">Submit Complaint</a>
        <a href="../complainant/my_complaints.php">My Complaints</a>

    <?php else: ?>

    <?php endif; ?>

    <a href="../auth/logout.php" class="logout-link">Logout</a>
</div>

<div class="main">
    <div class="topbar">
        Welcome, <?php echo $_SESSION['role']; ?>
    </div>
