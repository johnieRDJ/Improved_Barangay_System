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
        // Insert complaint
        db_execute($conn,
        "INSERT INTO complaints (complainant_id, subject, description)
         VALUES (?, ?, ?)",
         'iss',
         [$user_id, $subject, $description]);

        $complaint_id = mysqli_insert_id($conn);

        if($complaint_id > 0){
            $tracking_number = 'CMP-' . date('Ymd') . '-' . str_pad((string)$complaint_id, 5, '0', STR_PAD_LEFT);
            db_execute($conn,
            "UPDATE complaints
             SET tracking_number=?
             WHERE complaint_id=?",
             'si',
             [$tracking_number, $complaint_id]);

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
            db_execute($conn,
            "INSERT INTO logs (user_id, action)
             VALUES (?, ?)",
             'is',
             [$user_id, "Created complaint $tracking_number"]);

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
