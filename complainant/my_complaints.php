<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'complainant'){
    header("Location: ../auth/login.php");
    exit();
}

include('../config/database.php');
include('../includes/complaint_updates.php');
include('../includes/send_complaint_update.php');

$user_id = intval($_SESSION['user_id']);
$action_error = '';

if(isset($_POST['complaint_action'])){
    $complaintId = intval($_POST['complaint_id']);
    $complaintAction = $_POST['complaint_action'];
    $reopenNote = trim($_POST['reopen_note'] ?? '');

    $complaint = db_select_one($conn,
    "SELECT complaints.complaint_id,
            complaints.tracking_number,
            complaints.subject,
            complaints.status,
            complaints.resolution_confirmation,
            staff.email AS staff_email,
            staff.firstname AS staff_firstname,
            staff.lastname AS staff_lastname
     FROM complaints
     LEFT JOIN users staff ON complaints.assigned_staff_id = staff.user_id
     WHERE complaint_id=?
     AND complainant_id=?
     LIMIT 1",
     'ii',
     [$complaintId, $user_id]);

    if(!$complaint){
        $action_error = 'Complaint not found.';
    } elseif($complaint['status'] !== 'Resolved' || $complaint['resolution_confirmation'] !== 'pending'){
        $action_error = 'This complaint is not waiting for your confirmation.';
    } elseif($complaintAction === 'confirm'){
        db_execute($conn,
        "UPDATE complaints
         SET resolution_confirmation='confirmed'
         WHERE complaint_id=?
         AND complainant_id=?",
         'ii',
         [$complaintId, $user_id]);

        addComplaintUpdate(
            $conn,
            $complaintId,
            $user_id,
            'complainant',
            'resolution_confirmed',
            'Resolved',
            'Complainant confirmed that the complaint has been resolved.'
        );

        db_execute($conn,
        "INSERT INTO logs (user_id, action)
         VALUES (?, ?)",
         'is',
         [$user_id, "Confirmed resolution for complaint ID $complaintId"]);

        if(!empty($complaint['staff_email'])){
            $staffName = trim($complaint['staff_firstname'] . ' ' . $complaint['staff_lastname']);
            sendComplaintTimelineUpdate(
                $complaint['staff_email'],
                $staffName,
                $complaint['subject'],
                $complaint['tracking_number'],
                'Resolved',
                'The complainant confirmed that the complaint has been resolved.',
                'Complainant',
                rtrim(defined('APP_URL') ? APP_URL : 'http://localhost/barangay', '/') . '/staff/view_complaints.php'
            );
        }

        header("Location: my_complaints.php?confirmation=confirmed");
        exit();
    } elseif($complaintAction === 'reopen'){
        if($reopenNote === ''){
            $action_error = 'Please tell the staff why the complaint is not yet resolved.';
        } else {
            db_execute($conn,
            "UPDATE complaints
             SET status='In Progress',
                 resolution_confirmation='reopened'
             WHERE complaint_id=?
             AND complainant_id=?",
             'ii',
             [$complaintId, $user_id]);

            addComplaintUpdate(
                $conn,
                $complaintId,
                $user_id,
                'complainant',
                'resolution_reopened',
                'In Progress',
                "Complainant marked the complaint as not yet resolved. Reason: $reopenNote"
            );

            db_execute($conn,
            "INSERT INTO logs (user_id, action)
             VALUES (?, ?)",
             'is',
             [$user_id, "Reopened complaint ID $complaintId with feedback: $reopenNote"]);

            if(!empty($complaint['staff_email'])){
                $staffName = trim($complaint['staff_firstname'] . ' ' . $complaint['staff_lastname']);
                sendComplaintTimelineUpdate(
                    $complaint['staff_email'],
                    $staffName,
                    $complaint['subject'],
                    $complaint['tracking_number'],
                    'In Progress - Reopened',
                    "The complainant marked the complaint as not yet resolved.\n\nReason: $reopenNote",
                    'Complainant',
                    rtrim(defined('APP_URL') ? APP_URL : 'http://localhost/barangay', '/') . '/staff/view_complaints.php'
                );
            }

            header("Location: my_complaints.php?confirmation=reopened");
            exit();
        }
    }
}

include('../includes/header.php');
include('../includes/sidebar.php');

$complaints = db_select_all($conn,
"SELECT complaints.*, u.firstname AS staff_firstname, u.lastname AS staff_lastname
 FROM complaints
 LEFT JOIN users u ON complaints.assigned_staff_id = u.user_id
 WHERE complaints.complainant_id=?
 ORDER BY complaints.complaint_id DESC",
 'i',
 [$user_id]);

$timelineRows = db_select_all($conn,
"SELECT complaint_updates.*, users.firstname, users.lastname
 FROM complaint_updates
 LEFT JOIN users ON complaint_updates.actor_user_id = users.user_id
 INNER JOIN complaints ON complaints.complaint_id = complaint_updates.complaint_id
 WHERE complaints.complainant_id=?
 ORDER BY complaint_updates.created_at DESC, complaint_updates.update_id DESC",
 'i',
 [$user_id]);

$timelineByComplaint = [];

foreach($timelineRows as $timelineRow){
    $timelineByComplaint[$timelineRow['complaint_id']][] = $timelineRow;
}
?>

<div class="page-shell">
    <div class="dashboard-header">
        <h1>My Complaints</h1>
        <p>Track every action on your complaint, including who handled it and when updates were recorded.</p>
    </div>

    <?php if($action_error !== ''): ?>
        <div class="table-card">
            <p style="margin:0; color:#b91c1c; font-weight:600;"><?php echo htmlspecialchars($action_error); ?></p>
        </div>
    <?php endif; ?>

    <?php if(isset($_GET['confirmation']) && $_GET['confirmation'] === 'confirmed'): ?>
        <div class="table-card">
            <p style="margin:0; color:#15803d; font-weight:600;">You confirmed that the complaint has been resolved.</p>
        </div>
    <?php endif; ?>

    <?php if(isset($_GET['confirmation']) && $_GET['confirmation'] === 'reopened'): ?>
        <div class="table-card">
            <p style="margin:0; color:#b45309; font-weight:600;">The complaint was returned to staff for more action.</p>
        </div>
    <?php endif; ?>

    <div class="complaint-list">
        <?php if(count($complaints) === 0): ?>
            <div class="table-card">
                <p style="margin:0; color:#5b6b7f;">You have not submitted any complaints yet.</p>
            </div>
        <?php endif; ?>

        <?php foreach($complaints as $row): ?>
            <?php
            $complaintId = intval($row['complaint_id']);
            $timeline = $timelineByComplaint[$complaintId] ?? [];
            $assignedStaff = trim(($row['staff_firstname'] ?? '') . ' ' . ($row['staff_lastname'] ?? ''));
            ?>

            <div class="table-card complaint-card">
                <div class="complaint-card-header">
                    <div>
                        <h2 style="text-align:left; margin-bottom:6px;"><?php echo htmlspecialchars($row['subject']); ?></h2>
                        <p class="tracking-number">Tracking No. <?php echo htmlspecialchars($row['tracking_number']); ?></p>
                        <p class="developer-note" style="margin-bottom:0;">Submitted on <?php echo date('F j, Y g:i A', strtotime($row['created_at'])); ?></p>
                    </div>

                    <div class="complaint-status-group">
                        <?php if($row['status'] === 'Pending'): ?>
                            <span class="status-badge status-pending">Pending</span>
                        <?php elseif($row['status'] === 'Resolved' && $row['resolution_confirmation'] === 'pending'): ?>
                            <span class="status-badge complaint-status-awaiting">Awaiting Your Confirmation</span>
                        <?php elseif($row['status'] === 'In Progress' && $row['resolution_confirmation'] === 'reopened'): ?>
                            <span class="status-badge complaint-status-reopened">Reopened</span>
                        <?php elseif($row['status'] === 'In Progress'): ?>
                            <span class="status-badge complaint-status-progress">In Progress</span>
                        <?php else: ?>
                            <span class="status-badge status-approved">Resolved</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="complaint-detail-grid">
                    <div>
                        <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                        <p><strong>Assigned Staff:</strong> <?php echo $assignedStaff !== '' ? htmlspecialchars($assignedStaff) : '<span class="table-muted">Not assigned yet</span>'; ?></p>
                        <p><strong>Latest Staff Update:</strong> <?php echo !empty($row['staff_comment']) ? nl2br(htmlspecialchars($row['staff_comment'])) : '<span class="table-muted">No update yet</span>'; ?></p>
                    </div>

                    <div class="complaint-action-links">
                        <?php if($row['status'] === 'Pending'): ?>
                            <a href="print_ticket.php?id=<?php echo $complaintId; ?>" class="page-action">Print Complaint</a>
                            <a href="edit_complaint.php?id=<?php echo $complaintId; ?>" class="page-action secondary-action">Edit Complaint</a>
                            <a href="delete_complaints.php?id=<?php echo $complaintId; ?>" class="page-action secondary-action">Delete Complaint</a>
                        <?php elseif($row['status'] === 'Resolved' && $row['resolution_confirmation'] === 'pending'): ?>
                            <a href="print_ticket.php?id=<?php echo $complaintId; ?>" class="page-action">Print Complaint</a>
                            <form method="POST" class="complaint-confirmation-form">
                                <input type="hidden" name="complaint_id" value="<?php echo $complaintId; ?>">
                                <textarea name="reopen_note" placeholder="If not yet resolved, explain what is still needed."></textarea>
                                <button type="submit" name="complaint_action" value="confirm">Confirm Resolved</button>
                                <button type="submit" name="complaint_action" value="reopen" class="secondary-action">Not Yet Resolved</button>
                            </form>
                        <?php elseif($row['status'] === 'Resolved' && $row['resolution_confirmation'] === 'confirmed'): ?>
                            <a href="print_ticket.php?id=<?php echo $complaintId; ?>" class="page-action">Print Complaint</a>
                            <span class="table-muted">You already confirmed that this complaint was resolved.</span>
                        <?php elseif($row['status'] === 'In Progress' && $row['resolution_confirmation'] === 'reopened'): ?>
                            <a href="print_ticket.php?id=<?php echo $complaintId; ?>" class="page-action">Print Complaint</a>
                            <span class="table-muted">You sent this complaint back to staff for more action.</span>
                        <?php else: ?>
                            <a href="print_ticket.php?id=<?php echo $complaintId; ?>" class="page-action">Print Complaint</a>
                            <span class="table-muted">Editing is disabled once work has started.</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="complaint-timeline">
                    <h3>Progress Timeline</h3>

                    <?php if(empty($timeline)): ?>
                        <p class="table-muted">No activity has been recorded yet.</p>
                    <?php else: ?>
                        <?php foreach($timeline as $update): ?>
                            <?php
                            $actorName = trim(($update['firstname'] ?? '') . ' ' . ($update['lastname'] ?? ''));
                            $actorLabel = $actorName !== '' ? $actorName : ucfirst($update['actor_role']);
                            ?>

                            <div class="timeline-item">
                                <div class="timeline-item-header">
                                    <strong><?php echo htmlspecialchars($update['status_snapshot']); ?></strong>
                                    <span><?php echo date('F j, Y g:i A', strtotime($update['created_at'])); ?></span>
                                </div>
                                <p class="timeline-item-meta">Updated by <?php echo htmlspecialchars($actorLabel); ?></p>
                                <p><?php echo nl2br(htmlspecialchars($update['message'])); ?></p>
                                <?php if(!empty($update['proof_path'])): ?>
                                    <p class="timeline-proof-link">
                                        <a href="../view_proof.php?update_id=<?php echo intval($update['update_id']); ?>" target="_blank" rel="noopener noreferrer">
                                            View Proof: <?php echo htmlspecialchars($update['proof_original_name'] ?? basename($update['proof_path'])); ?>
                                        </a>
                                    </p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
