<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'complainant'){
    header("Location: ../auth/login.php");
    exit();
}

include('../config/database.php');

$user_id = intval($_SESSION['user_id']);
$id = intval($_GET['id'] ?? 0);

$stmt = db_prepared_query(
    $conn,
    "DELETE FROM complaints
     WHERE complaint_id=?
     AND complainant_id=?
     AND status='Pending'",
    'ii',
    [$id, $user_id]
);

$deleted = $stmt ? mysqli_stmt_affected_rows($stmt) : 0;
if($stmt){
    mysqli_stmt_close($stmt);
}

if($deleted > 0){
    db_execute(
        $conn,
        "INSERT INTO logs (user_id, action)
         VALUES (?, ?)",
        'is',
        [$user_id, "Deleted complaint ID $id"]
    );
}

header("Location: my_complaints.php");
exit();
?>
