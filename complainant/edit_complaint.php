<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'complainant'){
    header("Location: ../auth/login.php");
    exit();
}

include('../config/database.php');

$user_id = intval($_SESSION['user_id']);
$id = intval($_GET['id'] ?? 0);
$form_error = '';

if(isset($_POST['update'])){
    $subject = trim($_POST['subject'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if($subject === '' || $description === ''){
        $form_error = 'Please complete the subject and complaint details.';
    } else {
        $stmt = db_prepared_query(
            $conn,
            "UPDATE complaints
             SET subject=?, description=?
             WHERE complaint_id=?
             AND complainant_id=?
             AND status='Pending'",
            'ssii',
            [$subject, $description, $id, $user_id]
        );

        $updated = $stmt ? mysqli_stmt_affected_rows($stmt) : 0;
        if($stmt){
            mysqli_stmt_close($stmt);
        }

        if($updated > 0){
            db_execute(
                $conn,
                "INSERT INTO logs (user_id, action)
                 VALUES (?, ?)",
                'is',
                [$user_id, "Updated complaint ID $id"]
            );

            header("Location: my_complaints.php");
            exit();
        }

        $form_error = 'Complaint not found or editing is no longer allowed.';
    }
}

$data = db_select_one(
    $conn,
    "SELECT * FROM complaints
     WHERE complaint_id=?
     AND complainant_id=?
     LIMIT 1",
    'ii',
    [$id, $user_id]
);

include('../includes/header.php');
include('../includes/sidebar.php');
?>

<h2>Edit Complaint</h2>

<?php if(!$data): ?>
    <div class="table-card">
        <p style="margin:0; color:#b91c1c; font-weight:600;">Complaint not found.</p>
    </div>
<?php else: ?>
    <?php if($form_error !== ''): ?>
        <div class="table-card">
            <p style="margin:0; color:#b91c1c; font-weight:600;"><?php echo htmlspecialchars($form_error); ?></p>
        </div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="subject" value="<?php echo htmlspecialchars($data['subject']); ?>" required>
        <textarea name="description" required><?php echo htmlspecialchars($data['description']); ?></textarea>
        <button type="submit" name="update">Update</button>
    </form>
<?php endif; ?>

<?php include('../includes/footer.php'); ?>
