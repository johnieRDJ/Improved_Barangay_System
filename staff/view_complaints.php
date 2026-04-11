<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'staff'){
    header("Location: ../auth/login.php");
    exit();
}

include('../config/database.php');
include('../includes/complaint_updates.php');
include('../includes/send_complaint_update.php');

$user_id = intval($_SESSION['user_id']);
$update_error = '';

if(isset($_POST['update'])){

    $id = intval($_POST['complaint_id']);
    $comment = trim($_POST['comment']);
    $status = $_POST['status'] ?? 'In Progress';

    if(!in_array($status, ['In Progress', 'Resolved'], true)){
        $status = 'In Progress';
    }

    $proofPath = null;
    $proofOriginalName = null;
    $hasUpload = isset($_FILES['proof']) && $_FILES['proof']['error'] !== UPLOAD_ERR_NO_FILE;

    if($status === 'Resolved' && !$hasUpload){
        $update_error = 'Proof file is required before marking a complaint as resolved.';
    }

    if($comment === ''){
        $update_error = 'Please add a progress remark for the complainant.';
    }

    if($update_error === '' && $hasUpload){
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
        $originalName = $_FILES['proof']['name'];
        $tmpName = $_FILES['proof']['tmp_name'];
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $uploadError = $_FILES['proof']['error'];
        $uploadSize = intval($_FILES['proof']['size']);

        if($uploadError !== UPLOAD_ERR_OK){
            $update_error = 'The proof file could not be uploaded. Please try again.';
        } elseif(!in_array($extension, $allowedExtensions, true)){
            $update_error = 'Only JPG, PNG, and PDF files are allowed as proof.';
        } elseif($uploadSize > 5 * 1024 * 1024){
            $update_error = 'Proof files must be 5MB or smaller.';
        } else {
            $uploadsRoot = realpath(__DIR__ . '/../uploads');
            $proofFolder = $uploadsRoot === false ? false : $uploadsRoot . DIRECTORY_SEPARATOR . 'complaint_proofs';

            if($uploadsRoot === false){
                $update_error = 'Upload directory is not available.';
            } elseif(!is_dir($proofFolder) && !mkdir($proofFolder, 0777, true)){
                $update_error = 'Could not create the proof upload folder.';
            } else {
                $safeFileName = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($originalName));
                $storedFileName = time() . '_' . $id . '_' . $user_id . '_' . $safeFileName;
                $destinationPath = $proofFolder . DIRECTORY_SEPARATOR . $storedFileName;

                if(move_uploaded_file($tmpName, $destinationPath)){
                    $proofPath = 'uploads/complaint_proofs/' . $storedFileName;
                    $proofOriginalName = $originalName;
                } else {
                    $update_error = 'Could not save the proof file.';
                }
            }
        }
    }

    if($update_error === ''){
        $safe_comment = mysqli_real_escape_string($conn, $comment);
        $safe_status = mysqli_real_escape_string($conn, $status);
        $resolutionConfirmationValue = $status === 'Resolved' ? "'pending'" : "resolution_confirmation";
        $complaintNotice = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT complaints.tracking_number,
                complaints.subject,
                users.email,
                users.firstname,
                users.lastname
         FROM complaints
         JOIN users ON complaints.complainant_id = users.user_id
         WHERE complaints.complaint_id='$id'
         AND complaints.assigned_staff_id='$user_id'
         LIMIT 1"));

        mysqli_query($conn,
        "UPDATE complaints
         SET status='$safe_status',
             staff_comment='$safe_comment',
             resolution_confirmation=$resolutionConfirmationValue
         WHERE complaint_id='$id'
         AND assigned_staff_id='$user_id'");

        if(mysqli_affected_rows($conn) > 0){
            addComplaintUpdate(
                $conn,
                $id,
                $user_id,
                'staff',
                $status === 'Resolved' ? 'resolved' : 'progress_update',
                $status,
                $comment,
                $proofPath,
                $proofOriginalName
            );

            $log_action = $status === 'Resolved'
                ? "Resolved complaint ID $id and added comment"
                : "Updated complaint ID $id with progress remarks";

            mysqli_query($conn,
            "INSERT INTO logs (user_id, action)
             VALUES ('$user_id', '$log_action')");

            if($complaintNotice){
                $fullname = trim($complaintNotice['firstname'] . ' ' . $complaintNotice['lastname']);
                $emailStatus = $status === 'Resolved'
                    ? 'Resolved - Awaiting Your Confirmation'
                    : $status;
                $emailMessage = $comment;

                if($status === 'Resolved'){
                    $emailMessage .= "\n\nPlease open your complaint timeline and confirm if the issue is truly resolved.";
                }

                sendComplaintTimelineUpdate(
                    $complaintNotice['email'],
                    $fullname,
                    $complaintNotice['subject'],
                    $complaintNotice['tracking_number'],
                    $emailStatus,
                    $emailMessage,
                    'Barangay Staff'
                );
            }
        }

        header("Location: view_complaints.php?updated=1");
        exit();
    }
}

include('../includes/header.php');
include('../includes/sidebar.php');

// ✅ LOG: Viewed assigned complaints
mysqli_query($conn,
"INSERT INTO logs (user_id, action)
 VALUES ('$user_id','Viewed assigned complaints')");

$result = mysqli_query($conn,
"SELECT complaints.*, u.firstname AS complainant_firstname, u.lastname AS complainant_lastname
 FROM complaints
 LEFT JOIN users u ON complaints.complainant_id = u.user_id
 WHERE complaints.assigned_staff_id='$user_id'
 ORDER BY complaints.complaint_id DESC");
?>

<div class="page-shell">
    <div class="dashboard-header">
        <h1>Assigned Complaints</h1>
        <p>Post progress remarks and proof files so complainants can see real work happening on their concern.</p>
    </div>

    <?php if($update_error !== ''): ?>
        <div class="table-card">
            <p style="margin:0; color:#b91c1c; font-weight:600;"><?php echo htmlspecialchars($update_error); ?></p>
        </div>
    <?php endif; ?>

    <?php if(isset($_GET['updated'])): ?>
        <div class="table-card">
            <p style="margin:0; color:#15803d; font-weight:600;">Complaint progress updated successfully.</p>
        </div>
    <?php endif; ?>

    <div class="table-card">
        <table border="1" cellpadding="10" width="100%" class="responsive-table staff-complaints-table">
            <tr>
                <th>Complainant</th>
                <th>Tracking No.</th>
                <th>Subject</th>
                <th>Description</th>
                <th>Status</th>
                <th>Latest Staff Update</th>
                <th>Action</th>
            </tr>

            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars(trim(($row['complainant_firstname'] ?? '') . ' ' . ($row['complainant_lastname'] ?? ''))); ?></td>
                    <td><span class="tracking-number compact"><?php echo htmlspecialchars($row['tracking_number']); ?></span></td>
                    <td><?php echo htmlspecialchars($row['subject']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td>
                        <?php if($row['status'] === 'Pending'): ?>
                            <span class="status-badge status-pending">Pending</span>
                        <?php elseif($row['status'] === 'Resolved' && $row['resolution_confirmation'] === 'pending'): ?>
                            <span class="status-badge complaint-status-awaiting">Awaiting Confirmation</span>
                        <?php elseif($row['status'] === 'In Progress' && $row['resolution_confirmation'] === 'reopened'): ?>
                            <span class="status-badge complaint-status-reopened">Reopened</span>
                        <?php elseif($row['status'] === 'In Progress'): ?>
                            <span class="status-badge complaint-status-progress">In Progress</span>
                        <?php else: ?>
                            <span class="status-badge status-approved">Resolved</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php echo !empty($row['staff_comment']) ? nl2br(htmlspecialchars($row['staff_comment'])) : '<span class="table-muted">No comment yet</span>'; ?>
                    </td>
                    <td class="action-cell">
                        <?php if($row['status'] === 'Resolved' && $row['resolution_confirmation'] === 'pending'): ?>
                            <span class="table-muted">Waiting for complainant confirmation</span>
                        <?php elseif($row['status'] === 'Resolved'): ?>
                            <span class="table-muted">Already resolved</span>
                        <?php else: ?>
                            <form method="POST" enctype="multipart/form-data" class="complaint-update-form">
                                <input type="hidden" name="complaint_id" value="<?php echo intval($row['complaint_id']); ?>">
                                <select name="status" required>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Resolved">Resolved</option>
                                </select>
                                <textarea name="comment" placeholder="Add an update the complainant can see..." required></textarea>
                                <input type="file" name="proof" accept=".jpg,.jpeg,.png,.pdf">
                                <p class="table-muted" style="margin:0;">Attach a photo or PDF as proof. Required for resolved complaints.</p>
                                <button type="submit" name="update">Save Update</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>

<?php
if(false && isset($_POST['update'])){

    $id = $_POST['complaint_id'];
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);

    mysqli_query($conn,
    "UPDATE complaints
     SET status='Resolved',
         staff_comment='$comment'
     WHERE complaint_id='$id'");

    // ✅ LOG: Detailed action
    mysqli_query($conn,
    "INSERT INTO logs (user_id, action)
     VALUES ('$user_id',
     'Resolved complaint ID $id and added comment')");

    echo "<script>
    alert('Complaint updated!');
    window.location='view_complaints.php';
    </script>";
}
?>

<?php include('../includes/footer.php'); ?>

