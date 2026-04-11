<?php
session_start();

include('../config/database.php');
include('../includes/complaint_updates.php');


include('../includes/send_complaint_update.php');

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

// ============================
// 🔴 HANDLE ASSIGN / REASSIGN
// ============================
if(isset($_POST['assign'])){

    $complaint_id = intval($_POST['complaint_id']);
    $staff_id = intval($_POST['staff_id']);

    // Check if already assigned
    $check = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT complaints.assigned_staff_id,
            complaints.tracking_number,
            complaints.subject,
            users.email,
            users.firstname,
            users.lastname
     FROM complaints
     JOIN users ON complaints.complainant_id = users.user_id
     WHERE complaints.complaint_id='$complaint_id'
     LIMIT 1"));

    if($check['assigned_staff_id']){
        $log_msg = "Updated staff assignment for complaint ID $complaint_id";
    } else {
        $log_msg = "Assigned staff to complaint ID $complaint_id";
    }

    $staff_data = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT firstname, lastname FROM users WHERE user_id='$staff_id' LIMIT 1"));

    $staff_name = $staff_data
        ? trim($staff_data['firstname'] . ' ' . $staff_data['lastname'])
        : 'selected staff member';

    // Update complaint
    mysqli_query($conn,
    "UPDATE complaints
     SET assigned_staff_id='$staff_id',
         status='In Progress',
         resolution_confirmation=NULL
     WHERE complaint_id='$complaint_id'");

    // Save log
    mysqli_query($conn,
    "INSERT INTO logs (user_id, action)
     VALUES ('".$_SESSION['user_id']."', '$log_msg')");

    addComplaintUpdate(
        $conn,
        $complaint_id,
        intval($_SESSION['user_id']),
        'admin',
        'assigned',
        'In Progress',
        "Complaint assigned to $staff_name."
    );

    $fullname = trim($check['firstname'] . ' ' . $check['lastname']);
    sendComplaintTimelineUpdate(
        $check['email'],
        $fullname,
        $check['subject'],
        $check['tracking_number'],
        'In Progress',
        "Your complaint has been assigned to $staff_name for action.",
        'Barangay Admin'
    );

    header("Location: manage_complaints.php");
    exit();
}

include('../includes/header.php');
include('../includes/sidebar.php');

// ============================
// 🔴 GET DATA
// ============================
$result = mysqli_query($conn,
"SELECT complaints.*, 
        u.firstname AS fname, u.lastname AS lname, u.email,
        s.firstname AS staff_fname, s.lastname AS staff_lname
 FROM complaints
 JOIN users u ON complaints.complainant_id = u.user_id
 LEFT JOIN users s ON complaints.assigned_staff_id = s.user_id
 ORDER BY complaints.complaint_id DESC");

// Only approved staff
$staff = mysqli_query($conn,
"SELECT * FROM users 
 WHERE role='staff' AND account_status='approved'");
?>

<h2>Manage Complaints</h2>

<div class="table-card">
<table border="1" cellpadding="10" width="100%" class="responsive-table admin-complaints-table">
<tr>
    <th>Tracking No.</th>
    <th>Complainant</th>
    <th>Subject</th>
    <th>Description</th>
    <th>Status</th>
    <th>Assigned Staff</th>
    <th>Assign / Reassign</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)): ?>

<tr>

<td><span class="tracking-number compact"><?php echo htmlspecialchars($row['tracking_number']); ?></span></td>

<td><?php echo $row['fname']." ".$row['lname']; ?></td>

<td><?php echo $row['subject']; ?></td>

<td><?php echo $row['description']; ?></td>

<td>
<?php
if($row['status'] == 'Pending'){
    echo "<span style='color:orange;'>Pending</span>";
}
elseif($row['status'] == 'Resolved' && $row['resolution_confirmation'] == 'pending'){
    echo "<span style='color:#1d4f91;'>Awaiting Confirmation</span>";
}
elseif($row['status'] == 'In Progress' && $row['resolution_confirmation'] == 'reopened'){
    echo "<span style='color:#b45309;'>Reopened</span>";
}
elseif($row['status'] == 'In Progress'){
    echo "<span style='color:blue;'>In Progress</span>";
}
else{
    echo "<span style='color:green;'>Resolved</span>";
}
?>
</td>

<td>
<?php
if($row['staff_fname']){
    echo $row['staff_fname']." ".$row['staff_lname'];
}else{
    echo "<i>Not Assigned</i>";
}
?>
</td>

<td>

<!-- 🔴 ALWAYS ALLOW ASSIGN / REASSIGN -->
<form method="POST">

<input type="hidden" name="complaint_id" value="<?php echo $row['complaint_id']; ?>">
<input type="hidden" name="email" value="<?php echo $row['email']; ?>">
<input type="hidden" name="fullname" value="<?php echo $row['fname']." ".$row['lname']; ?>">
<input type="hidden" name="subject" value="<?php echo $row['subject']; ?>">

<select name="staff_id" required>

<?php
mysqli_data_seek($staff, 0);
while($s = mysqli_fetch_assoc($staff)):
?>

<option value="<?php echo $s['user_id']; ?>">
<?php echo $s['firstname']." ".$s['lastname']; ?>
</option>

<?php endwhile; ?>

</select>

<button type="submit" name="assign">
<?php echo $row['assigned_staff_id'] ? 'Update' : 'Assign'; ?>
</button>

</form>



</td>

</tr>

<?php endwhile; ?>

</table>
</div>



<?php include('../includes/footer.php'); ?>

