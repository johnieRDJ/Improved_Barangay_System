<?php
session_start();

include('../config/database.php');

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

$id = intval($_GET['id'] ?? 0);

$target = db_select_one(
    $conn,
    "SELECT firstname, lastname, role FROM users WHERE user_id=? LIMIT 1",
    'i',
    [$id]
);

if(!$target){
    header("Location: manage_users.php");
    exit();
}

if($target['role'] == 'superadmin'){
    echo "<script>alert('Superadmin account is protected.'); window.location='manage_users.php';</script>";
    exit();
}

$stmt = db_prepared_query(
    $conn,
    "UPDATE residency
     SET status='verified'
     WHERE user_id=?",
    'i',
    [$id]
);

$updated = $stmt ? mysqli_stmt_affected_rows($stmt) : 0;
if($stmt){
    mysqli_stmt_close($stmt);
}

if($updated == 0){
    db_execute(
        $conn,
        "INSERT INTO residency (user_id, status)
         SELECT ?, 'verified'
         WHERE NOT EXISTS (
             SELECT 1 FROM residency WHERE user_id=?
         )",
        'ii',
        [$id, $id]
    );
}

$fullname = $target['firstname'] . " " . $target['lastname'];

db_execute(
    $conn,
    "INSERT INTO logs (user_id, action)
     VALUES (?, ?)",
    'is',
    [intval($_SESSION['user_id']), "Verified residency for user ID $id"]
);

$_SESSION['status_message'] = "Residency for " . $fullname . " has been verified.";

header("Location: manage_users.php");
exit();
?>
