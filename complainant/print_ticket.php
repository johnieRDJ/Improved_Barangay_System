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
    $complaint = db_select_one($conn,
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
     WHERE complaints.complaint_id=?
     AND complaints.complainant_id=?
     LIMIT 1",
     'ii',
     [$complaint_id, $user_id]);
}

include('../includes/header.php');
include('../includes/sidebar.php');
?>

<style>
@media print {
    .ticket-sheet.compact-ticket,
    .ticket-sheet.compact-ticket * {
        box-sizing: border-box !important;
        position: static !important;
        transform: none !important;
    }

    .ticket-sheet.compact-ticket {
        display: block !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
        box-shadow: none !important;
        font-size: 8pt !important;
        line-height: 1.15 !important;
    }

    .compact-ticket .ticket-header {
        display: grid !important;
        grid-template-columns: minmax(0, 1fr) 1.65in !important;
        grid-template-areas:
            "brand tracking"
            "title title"
            "desc desc" !important;
        align-items: start !important;
        gap: 0.035in 0.16in !important;
        margin: 0 0 0.11in !important;
        padding: 0 0 0.09in !important;
        border-bottom: 1px solid #163a63 !important;
    }

    .compact-ticket .ticket-title-block {
        grid-area: brand !important;
        min-width: 0 !important;
    }

    .compact-ticket .ticket-kicker {
        margin: 0 0 0.035in !important;
        font-size: 7pt !important;
        line-height: 1.05 !important;
    }

    .compact-ticket .ticket-header h2 {
        grid-area: title !important;
        margin: 0 !important;
        font-size: 12.2pt !important;
        line-height: 1.05 !important;
        white-space: normal !important;
    }

    .compact-ticket .ticket-header p {
        grid-area: desc !important;
        margin: 0 !important;
        font-size: 7.3pt !important;
        line-height: 1.25 !important;
    }

    .compact-ticket .ticket-number-box {
        display: block !important;
        grid-area: tracking !important;
        width: 1.65in !important;
        height: auto !important;
        min-height: 0 !important;
        max-height: none !important;
        margin: 0 !important;
        padding: 0.04in 0.06in !important;
        border: 1px solid #cfd8e3 !important;
        border-radius: 0.06in !important;
        text-align: center !important;
    }

    .compact-ticket .ticket-number-box span {
        display: block !important;
        white-space: normal !important;
    }

    .compact-ticket .ticket-number-box span,
    .compact-ticket .ticket-grid span,
    .compact-ticket .ticket-block span,
    .compact-ticket .ticket-signature-row span {
        margin: 0 !important;
        font-size: 6.8pt !important;
        line-height: 1.05 !important;
        letter-spacing: 0.03em !important;
    }

    .compact-ticket .ticket-number-box strong {
        margin: 0.02in 0 0 !important;
        font-size: 7.6pt !important;
        line-height: 1.05 !important;
    }

    .compact-ticket .ticket-section {
        display: block !important;
        clear: both !important;
        margin: 0 !important;
        padding: 0.065in 0 !important;
        border-bottom: 1px solid #e5eaf2 !important;
        break-inside: auto !important;
    }

    .compact-ticket .ticket-section h3 {
        margin: 0 0 0.05in !important;
        font-size: 9.4pt !important;
        line-height: 1.12 !important;
    }

    .compact-ticket .ticket-grid {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 0.055in !important;
    }

    .compact-ticket .ticket-grid div,
    .compact-ticket .ticket-block {
        display: block !important;
        margin: 0 !important;
        padding: 0.065in 0.075in !important;
        border: 1px solid #e5eaf2 !important;
        border-radius: 0.055in !important;
        background: #ffffff !important;
        min-height: 0 !important;
    }

    .compact-ticket .ticket-grid strong,
    .compact-ticket .ticket-block strong {
        margin: 0.035in 0 0 !important;
        font-size: 7.8pt !important;
        line-height: 1.16 !important;
    }

    .compact-ticket .ticket-block + .ticket-block {
        margin-top: 0.055in !important;
    }

    .compact-ticket .ticket-block p {
        margin: 0.035in 0 0 !important;
        font-size: 7.8pt !important;
        line-height: 1.22 !important;
    }

    .compact-ticket .ticket-note {
        display: block !important;
        margin: 0.08in 0 0.1in !important;
        padding: 0.07in 0.085in !important;
        border-radius: 0.055in !important;
        background: #ffffff !important;
        line-height: 1.22 !important;
        break-inside: auto !important;
    }

    .compact-ticket .ticket-signature-row {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 0.16in !important;
        margin: 0.13in 0 0 !important;
        break-inside: auto !important;
    }

    .compact-ticket .ticket-signature-row div {
        padding-top: 0.18in !important;
        border-top: 1px solid #172033 !important;
    }

    .compact-ticket .ticket-signature-row strong {
        margin: 0.03in 0 0 !important;
        min-height: 0 !important;
        font-size: 7.5pt !important;
        line-height: 1.1 !important;
    }
}
</style>

<style>
.print-ticket-sheet {
    width: min(100%, 900px);
    margin: 0 auto;
    background: #ffffff;
    border: 1px solid #d7e0eb;
    border-radius: 18px;
    padding: 28px;
    box-shadow: 0 18px 42px rgba(15, 23, 42, 0.10);
    color: #172033;
}

.print-ticket-sheet .ticket-header {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    gap: 8px 24px;
    align-items: start;
    padding-bottom: 16px;
    border-bottom: 2px solid #163a63;
}

.print-ticket-sheet .ticket-kicker {
    margin: 0;
    color: #1d4f91;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.print-ticket-sheet .ticket-number-box {
    text-align: right;
}

.print-ticket-sheet .ticket-number-box span,
.print-ticket-sheet .ticket-grid span,
.print-ticket-sheet .ticket-block span,
.print-ticket-sheet .ticket-signature-row span {
    display: block;
    color: #5b6b7f;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.05em;
    text-transform: uppercase;
}

.print-ticket-sheet .ticket-number-box strong {
    display: block;
    margin-top: 4px;
    color: #102a43;
    font-family: "Courier New", monospace;
    font-size: 14px;
}

.print-ticket-sheet .ticket-header h2 {
    grid-column: 1 / -1;
    margin: 4px 0 0;
    text-align: left;
    color: #102a43;
    font-size: 26px;
}

.print-ticket-sheet .ticket-header p {
    grid-column: 1 / -1;
    margin: 0;
    color: #5b6b7f;
}

.print-ticket-sheet .ticket-section {
    padding: 16px 0;
    border-bottom: 1px solid #e5eaf2;
}

.print-ticket-sheet .ticket-section h3 {
    margin: 0 0 10px;
    color: #12355b;
}

.print-ticket-sheet .ticket-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
}

.print-ticket-sheet .ticket-grid div,
.print-ticket-sheet .ticket-block {
    padding: 12px;
    background: #f8fbff;
    border: 1px solid #e5eaf2;
    border-radius: 10px;
}

.print-ticket-sheet .ticket-grid strong,
.print-ticket-sheet .ticket-block strong {
    display: block;
    margin-top: 5px;
    color: #172033;
    overflow-wrap: anywhere;
}

.print-ticket-sheet .ticket-block + .ticket-block {
    margin-top: 10px;
}

.print-ticket-sheet .ticket-block p {
    margin: 6px 0 0;
    color: #344256;
    line-height: 1.5;
}

.print-ticket-sheet .ticket-note {
    display: flex;
    align-items: center;
    gap: 4px;
    min-height: 42px;
    margin: 14px 0 28px;
    padding: 10px 12px;
    background: #fff7db;
    border: 1px solid #f0d991;
    border-radius: 10px;
    color: #614700;
    line-height: 1.4;
}

.print-ticket-sheet .ticket-note span {
    display: inline;
}

.print-ticket-sheet .ticket-signature-row {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 26px;
    margin-top: 22px;
}

.print-ticket-sheet .ticket-signature-row div {
    padding-top: 30px;
    border-top: 1px solid #172033;
}

.print-ticket-sheet .ticket-signature-row strong {
    display: block;
    margin-top: 4px;
    min-height: 18px;
}

@media screen and (max-width: 640px) {
    .print-ticket-sheet {
        padding: 20px;
        border-radius: 16px;
    }

    .print-ticket-sheet .ticket-header {
        grid-template-columns: 1fr;
        gap: 8px;
    }

    .print-ticket-sheet .ticket-number-box {
        text-align: left;
    }

    .print-ticket-sheet .ticket-header h2 {
        font-size: 22px;
        line-height: 1.15;
    }

    .print-ticket-sheet .ticket-grid,
    .print-ticket-sheet .ticket-signature-row {
        grid-template-columns: 1fr;
    }

    .print-ticket-sheet .ticket-note {
        display: flex;
        align-items: center;
        gap: 4px;
        min-height: 52px;
        margin: 14px 0 24px;
        padding: 12px 14px;
        text-align: left;
        overflow-wrap: anywhere;
    }

    .print-ticket-sheet .ticket-note strong {
        flex: 0 0 auto;
    }

    .print-ticket-sheet .ticket-note span {
        flex: 1 1 160px;
    }

    .print-ticket-sheet .ticket-signature-row {
        gap: 20px;
    }
}

@media print {
    @page {
        size: 8.5in 13in;
        margin: 0.35in;
    }

    body {
        background: #ffffff !important;
    }

    .no-print,
    .sidebar,
    .topbar,
    .site-footer {
        display: none !important;
    }

    .main,
    .page-shell,
    .ticket-page {
        display: block !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .print-ticket-sheet,
    .print-ticket-sheet * {
        box-sizing: border-box !important;
        position: static !important;
        transform: none !important;
    }

    .print-ticket-sheet {
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        color: #172033 !important;
        font-size: 8.5pt !important;
        line-height: 1.22 !important;
    }

    .print-ticket-sheet .ticket-header {
        display: grid !important;
        grid-template-columns: minmax(0, 1fr) 1.75in !important;
        gap: 5pt 16pt !important;
        padding-bottom: 8pt !important;
        margin-bottom: 8pt !important;
        border-bottom: 1pt solid #163a63 !important;
    }

    .print-ticket-sheet .ticket-kicker {
        margin: 0 !important;
        font-size: 7.2pt !important;
        line-height: 1.1 !important;
    }

    .print-ticket-sheet .ticket-number-box {
        padding: 0 !important;
        border: 0 !important;
        text-align: right !important;
    }

    .print-ticket-sheet .ticket-number-box span,
    .print-ticket-sheet .ticket-grid span,
    .print-ticket-sheet .ticket-block span,
    .print-ticket-sheet .ticket-signature-row span {
        font-size: 6.9pt !important;
        line-height: 1.1 !important;
        letter-spacing: 0.03em !important;
    }

    .print-ticket-sheet .ticket-number-box strong {
        margin-top: 2pt !important;
        font-size: 7.8pt !important;
        line-height: 1.1 !important;
    }

    .print-ticket-sheet .ticket-header h2 {
        margin: 3pt 0 0 !important;
        font-size: 13pt !important;
        line-height: 1.08 !important;
    }

    .print-ticket-sheet .ticket-header p {
        margin: 0 !important;
        font-size: 7.4pt !important;
        line-height: 1.2 !important;
    }

    .print-ticket-sheet .ticket-section {
        padding: 7pt 0 !important;
        border-bottom: 1px solid #e5eaf2 !important;
        break-inside: avoid !important;
    }

    .print-ticket-sheet .ticket-section h3 {
        margin: 0 0 5pt !important;
        font-size: 9.5pt !important;
        line-height: 1.1 !important;
    }

    .print-ticket-sheet .ticket-grid {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 5pt !important;
    }

    .print-ticket-sheet .ticket-grid div,
    .print-ticket-sheet .ticket-block {
        padding: 5pt 6pt !important;
        background: #ffffff !important;
        border: 1px solid #e5eaf2 !important;
        border-radius: 4pt !important;
    }

    .print-ticket-sheet .ticket-grid strong,
    .print-ticket-sheet .ticket-block strong {
        margin-top: 2pt !important;
        font-size: 7.8pt !important;
        line-height: 1.15 !important;
    }

    .print-ticket-sheet .ticket-block + .ticket-block {
        margin-top: 5pt !important;
    }

    .print-ticket-sheet .ticket-block p {
        margin: 2pt 0 0 !important;
        font-size: 7.8pt !important;
        line-height: 1.22 !important;
    }

    .print-ticket-sheet .ticket-note {
        display: flex !important;
        align-items: center !important;
        min-height: 28pt !important;
        margin: 8pt 0 24pt !important;
        padding: 6pt 7pt !important;
        background: #ffffff !important;
        border: 1px solid #f0d991 !important;
        border-radius: 4pt !important;
        line-height: 1.22 !important;
        break-inside: avoid !important;
    }

    .print-ticket-sheet .ticket-signature-row {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 18pt !important;
        margin-top: 24pt !important;
        break-inside: avoid !important;
    }

    .print-ticket-sheet .ticket-signature-row div {
        padding-top: 22pt !important;
        border-top: 1px solid #172033 !important;
    }

    .print-ticket-sheet .ticket-signature-row strong {
        margin-top: 3pt !important;
        min-height: 10pt !important;
        font-size: 7.8pt !important;
        line-height: 1.1 !important;
    }
}
</style>

<div class="page-shell ticket-page">
    <div class="dashboard-header no-print">
        <h1>Print Complaint</h1>
        <p>This is your printable copy showing that the complaint was received by the system.</p>
    </div>

    <?php if(isset($_GET['submitted']) && $_GET['submitted'] === '1'): ?>
        <div class="table-card no-print">
            <p style="margin:0; color:#15803d; font-weight:700;">Complaint submitted successfully. You can print or save this complaint copy.</p>
        </div>
    <?php endif; ?>

    <?php if(!$complaint): ?>
        <div class="table-card">
            <p style="margin:0; color:#b91c1c; font-weight:700;">Complaint copy not found.</p>
            <p class="developer-note" style="margin-top:8px;">Please open the printable complaint from your own complaint list.</p>
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
            <button type="button" onclick="window.print()">Print Complaint</button>
            <a href="my_complaints.php" class="page-action secondary-action">Back to My Complaints</a>
        </div>

        <article class="print-ticket-sheet">
            <header class="ticket-header">
                <div class="ticket-title-block">
                    <p class="ticket-kicker">Barangay Digital Complaint Desk System</p>
                </div>
                <div class="ticket-number-box">
                    <span>Tracking Number</span>
                    <strong><?php echo htmlspecialchars($complaint['tracking_number']); ?></strong>
                </div>
                <h2>Complaint Acknowledgement Copy</h2>
                <p>This printed complaint serves as proof that your complaint has been submitted and recorded.</p>
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
                <span>Keep this complaint copy for follow-up. Use the tracking number when checking the status of your complaint.</span>
            </section>

            <footer class="ticket-signature-row">
                <div>
                    <span>Received By</span>
                    <strong>Barangay Digital Complaint Desk System</strong>
                </div>
                <div>
                    <span>Complainant Name / Signature</span>
                    <strong><?php echo htmlspecialchars($complainantName); ?></strong>
                </div>
            </footer>
        </article>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>
