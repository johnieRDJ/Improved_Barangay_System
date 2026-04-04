<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

include('../includes/header.php');
include('../includes/send_residency_schedule.php');

include('../config/database.php');
include('../includes/sidebar.php');

$user_id = intval($_GET['id']);

$target = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT role FROM users WHERE user_id='$user_id'"));

if($target && $target['role'] == 'superadmin'){
    echo "<script>alert('Superadmin account is protected.'); window.location='manage_users.php';</script>";
    exit();
}
?>

<h2>Schedule Residency Appointment</h2>

<form method="POST">
    <input type="datetime-local" name="appointment_date" required>
    <button type="submit" name="schedule">Schedule</button>
</form>

<?php
if(isset($_POST['schedule'])){

    $date = $_POST['appointment_date'];

    // Save appointment to database
    mysqli_query($conn,
    "INSERT INTO appointments (user_id, appointment_date, purpose)
     VALUES ('$user_id','$date','Barangay Residency Verification')");

    mysqli_query($conn,
    "UPDATE residency
     SET status='pending'
     WHERE user_id='$user_id'
     AND status='none'");

    if(mysqli_affected_rows($conn) == 0){
        mysqli_query($conn,
        "INSERT INTO residency (user_id, status)
         SELECT '$user_id', 'pending'
         WHERE NOT EXISTS (
             SELECT 1 FROM residency WHERE user_id='$user_id'
         )");
    }

    // Get user information
    $result = mysqli_query($conn, "SELECT firstname, lastname, email FROM users WHERE user_id='$user_id'");
    $user = mysqli_fetch_assoc($result);

    if(!$user){
        echo "<script>
        alert('User not found.');
        window.location='manage_users.php';
        </script>";
        exit();
    }

    $fullname = $user['firstname']." ".$user['lastname'];
    $email = $user['email'];

    // Format date nicely for email
    $formatted_date = date("F d, Y - g:i A", strtotime($date));

    // Send email notification
    sendResidencySchedule($email, $fullname, $formatted_date);

    // Save log
    mysqli_query($conn,
    "INSERT INTO logs (user_id, action)
     VALUES ('".$_SESSION['user_id']."',
     'Scheduled residency appointment for user ID $user_id')");

    echo "<script>
    alert('Appointment Scheduled and Email Sent!');
    window.location='manage_users.php';
    </script>";
}
?>

<?php include('../includes/footer.php'); ?>

