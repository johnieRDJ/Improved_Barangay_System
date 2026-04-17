<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'complainant'){
    header("Location: ../auth/login.php");
    exit();
}

include('../config/database.php');

$user_id = intval($_SESSION['user_id']);
$complaint_id = intval($_GET['id'] ?? 0);
$complaint = null;

if($complaint_id > 0){
    $result = mysqli_query($conn,
    "SELECT complaints.complaint_id,
            complaints.tracking_number,
            complaints.subject,
            complaints.description,
            complaints.status,
            complaints.resolution_confirmation,
            complaints.staff_comment,
            complaints.created_at,
            complainant.firstname AS complainant_firstname,
            complainant.lastname AS complainant_lastname,
            complainant.email AS complainant_email,
            staff.firstname AS staff_firstname,
            staff.lastname AS staff_lastname
     FROM complaints
     INNER JOIN users complainant ON complaints.complainant_id = complainant.user_id
     LEFT JOIN users staff ON complaints.assigned_staff_id = staff.user_id
     WHERE complaints.complaint_id='$complaint_id'
     AND complaints.complainant_id='$user_id'
     LIMIT 1");

    if($result){
        $complaint = mysqli_fetch_assoc($result);
    }
}

include('../includes/header.php');
include('../includes/sidebar.php');
?>

<div class="page-shell ticket-page">
    <div class="dashboard-header no-print">
        <h1>Complaint Ticket</h1>
        <p>This is your official proof that the complaint was received by the system.</p>
    </div>

    <?php if(isset($_GET['submitted']) && $_GET['submitted'] === '1'): ?>
        <div class="table-card no-print">
            <p style="margin:0; color:#15803d; font-weight:700;">Complaint submitted successfully. You can print or save this ticket.</p>
        </div>
    <?php endif; ?>

    <?php if(!$complaint): ?>
        <div class="table-card">
            <p style="margin:0; color:#b91c1c; font-weight:700;">Complaint ticket not found.</p>
            <p class="developer-note" style="margin-top:8px;">Please open the ticket from your own complaint list.</p>
        </div>
    <?php else: ?>
        <?php
        $complainantName = trim($complaint['complainant_firstname'] . ' ' . $complaint['complainant_lastname']);
        $staffName = trim(($complaint['staff_firstname'] ?? '') . ' ' . ($complaint['staff_lastname'] ?? ''));
        $status = $complaint['status'];
        $submittedAt = date('F j, Y g:i A', strtotime($complaint['created_at']));
        $generatedAt = date('F j, Y g:i A');
        ?>

        <div class="ticket-actions no-print">
            <button type="button" onclick="window.print()">Print Ticket</button>
            <a href="my_complaints.php" class="page-action secondary-action">Back to My Complaints</a>
        </div>

        <article class="ticket-sheet">
            <header class="ticket-header">
                <div>
                    <p class="ticket-kicker">Barangay Digital Complaint Desk System</p>
                    <h2>Complaint Acknowledgement Ticket</h2>
                    <p>This ticket serves as proof that your complaint has been submitted and recorded.</p>
                </div>
                <div class="ticket-number-box">
                    <span>Tracking Number</span>
                    <strong><?php echo htmlspecialchars($complaint['tracking_number']); ?></strong>
                </div>
            </header>

            <section class="ticket-section">
                <h3>Ticket Details</h3>
                <div class="ticket-grid">
                    <div>
                        <span>Complaint ID</span>
                        <strong><?php echo intval($complaint['complaint_id']); ?></strong>
                    </div>
                    <div>
                        <span>Date Submitted</span>
                        <strong><?php echo htmlspecialchars($submittedAt); ?></strong>
                    </div>
                    <div>
                        <span>Current Status</span>
                        <strong><?php echo htmlspecialchars($status); ?></strong>
                    </div>
                    <div>
                        <span>Generated On</span>
                        <strong><?php echo htmlspecialchars($generatedAt); ?></strong>
                    </div>
                </div>
            </section>

            <section class="ticket-section">
                <h3>Complainant Information</h3>
                <div class="ticket-grid">
                    <div>
                        <span>Name</span>
                        <strong><?php echo htmlspecialchars($complainantName); ?></strong>
                    </div>
                    <div>
                        <span>Email</span>
                        <strong><?php echo htmlspecialchars($complaint['complainant_email']); ?></strong>
                    </div>
                    <div>
                        <span>Assigned Staff</span>
                        <strong><?php echo $staffName !== '' ? htmlspecialchars($staffName) : 'Not assigned yet'; ?></strong>
                    </div>
                </div>
            </section>

            <section class="ticket-section">
                <h3>Complaint Information</h3>
                <div class="ticket-block">
                    <span>Subject</span>
                    <strong><?php echo htmlspecialchars($complaint['subject']); ?></strong>
                </div>
                <div class="ticket-block">
                    <span>Description</span>
                    <p><?php echo nl2br(htmlspecialchars($complaint['description'])); ?></p>
                </div>
            </section>

            <section class="ticket-note">
                <strong>Important Reminder:</strong>
                Keep this ticket for follow-up. Use the tracking number when checking the status of your complaint.
            </section>

            <footer class="ticket-signature-row">
                <div>
                    <span>Received By</span>
                    <strong>Barangay Digital Complaint Desk System</strong>
                </div>
                <div>
                    <span>Complainant Signature</span>
                    <strong>&nbsp;</strong>
                </div>
            </footer>
        </article>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>
