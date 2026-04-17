<?php
session_start();

include('../config/database.php');
include('../includes/complaint_updates.php');

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'complainant'){
    header("Location: ../auth/login.php");
    exit();
}

$form_error = '';

if(isset($_POST['submit'])){

    // Sanitize input to prevent SQL errors
    $subject = trim($_POST['subject'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $user_id = $_SESSION['user_id'];

    if($subject === '' || $description === ''){
        $form_error = 'Please complete the subject and complaint details.';
    } else {
        $safe_subject = mysqli_real_escape_string($conn, $subject);
        $safe_description = mysqli_real_escape_string($conn, $description);

        // Insert complaint
        mysqli_query($conn,
        "INSERT INTO complaints (complainant_id, subject, description)
         VALUES ('$user_id','$safe_subject','$safe_description')");

        $complaint_id = mysqli_insert_id($conn);

        if($complaint_id > 0){
            $tracking_number = 'CMP-' . date('Ymd') . '-' . str_pad((string)$complaint_id, 5, '0', STR_PAD_LEFT);
            $safe_tracking_number = mysqli_real_escape_string($conn, $tracking_number);

            mysqli_query($conn,
            "UPDATE complaints
             SET tracking_number='$safe_tracking_number'
             WHERE complaint_id='$complaint_id'");

            addComplaintUpdate(
                $conn,
                $complaint_id,
                intval($user_id),
                'complainant',
                'submitted',
                'Pending',
                'Complaint submitted by complainant.'
            );

            // Insert log
            mysqli_query($conn,
            "INSERT INTO logs (user_id, action)
             VALUES ('$user_id','Created complaint $safe_tracking_number')");

            header("Location: print_ticket.php?id=" . $complaint_id . "&submitted=1");
            exit();
        }

        $form_error = 'Unable to submit complaint. Please try again.';
    }
}

include('../includes/header.php');
include('../includes/sidebar.php');
?>

<h2>Submit Complaint</h2>

<?php if($form_error !== ''): ?>
    <div class="table-card">
        <p style="margin:0; color:#b91c1c; font-weight:600;"><?php echo htmlspecialchars($form_error); ?></p>
    </div>
<?php endif; ?>

<form method="POST">
    <input type="text" name="subject" placeholder="Subject" required>
    <textarea name="description" placeholder="Complaint Details" required></textarea>
    <button type="submit" name="submit">Submit</button>
</form>

<?php include('../includes/footer.php'); ?>
