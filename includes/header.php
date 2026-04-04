<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Barangay Digital Complaint Desk</title>
    <?php $style_version = filemtime(__DIR__ . '/../css/style.css'); ?>
    <link rel="stylesheet" href="../css/style.css?v=<?php echo $style_version; ?>">
</head>
<body>
