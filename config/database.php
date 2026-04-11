<?php
$configuredAppUrl = '';
// Example for phone or LAN testing:
// $configuredAppUrl = 'http://192.168.1.10/barangay';

date_default_timezone_set('Asia/Manila');

if(!defined('APP_URL')){
    if($configuredAppUrl !== ''){
        define('APP_URL', rtrim($configuredAppUrl, '/'));
    } elseif(!empty($_SERVER['HTTP_HOST'])){
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $scriptPath = str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '')));
        $basePath = ($scriptPath === '/' || $scriptPath === '.') ? '' : rtrim($scriptPath, '/');
        define('APP_URL', $scheme . '://' . $_SERVER['HTTP_HOST'] . $basePath);
    } else {
        // CLI/local fallback
        define('APP_URL', 'http://localhost/barangay');
    }
}

$conn = mysqli_connect("localhost", "root", "", "barangay_db");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_query($conn, "SET time_zone = '+08:00'");

$complaintTrackingColumn = mysqli_query($conn, "SHOW COLUMNS FROM complaints LIKE 'tracking_number'");
if($complaintTrackingColumn instanceof mysqli_result && mysqli_num_rows($complaintTrackingColumn) === 0){
    mysqli_query($conn, "ALTER TABLE complaints ADD COLUMN tracking_number VARCHAR(30) DEFAULT NULL AFTER complaint_id");
}

mysqli_query($conn, "UPDATE complaints
SET tracking_number = CONCAT('CMP-', DATE_FORMAT(created_at, '%Y%m%d'), '-', LPAD(complaint_id, 5, '0'))
WHERE tracking_number IS NULL
OR tracking_number = ''");

$complaintTrackingIndex = mysqli_query($conn, "SHOW INDEX FROM complaints WHERE Key_name='tracking_number'");
if($complaintTrackingIndex instanceof mysqli_result && mysqli_num_rows($complaintTrackingIndex) === 0){
    mysqli_query($conn, "ALTER TABLE complaints ADD UNIQUE KEY tracking_number (tracking_number)");
}

$complaintResolutionColumn = mysqli_query($conn, "SHOW COLUMNS FROM complaints LIKE 'resolution_confirmation'");
if($complaintResolutionColumn instanceof mysqli_result && mysqli_num_rows($complaintResolutionColumn) === 0){
    mysqli_query($conn, "ALTER TABLE complaints ADD COLUMN resolution_confirmation ENUM('pending','confirmed','reopened') DEFAULT NULL AFTER status");
}

mysqli_query($conn, "UPDATE complaints
SET resolution_confirmation='confirmed'
WHERE status='Resolved'
AND resolution_confirmation IS NULL");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS complaint_updates (
    update_id INT(11) NOT NULL AUTO_INCREMENT,
    complaint_id INT(11) NOT NULL,
    actor_user_id INT(11) DEFAULT NULL,
    actor_role VARCHAR(50) DEFAULT NULL,
    update_type VARCHAR(50) DEFAULT NULL,
    status_snapshot VARCHAR(50) DEFAULT NULL,
    message TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (update_id),
    KEY complaint_id (complaint_id),
    KEY actor_user_id (actor_user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

$complaintUpdateProofPathColumn = mysqli_query($conn, "SHOW COLUMNS FROM complaint_updates LIKE 'proof_path'");
if($complaintUpdateProofPathColumn instanceof mysqli_result && mysqli_num_rows($complaintUpdateProofPathColumn) === 0){
    mysqli_query($conn, "ALTER TABLE complaint_updates ADD COLUMN proof_path VARCHAR(255) DEFAULT NULL AFTER message");
}

$complaintUpdateProofNameColumn = mysqli_query($conn, "SHOW COLUMNS FROM complaint_updates LIKE 'proof_original_name'");
if($complaintUpdateProofNameColumn instanceof mysqli_result && mysqli_num_rows($complaintUpdateProofNameColumn) === 0){
    mysqli_query($conn, "ALTER TABLE complaint_updates ADD COLUMN proof_original_name VARCHAR(255) DEFAULT NULL AFTER proof_path");
}

mysqli_query($conn, "INSERT INTO complaint_updates (
    complaint_id,
    actor_user_id,
    actor_role,
    update_type,
    status_snapshot,
    message,
    created_at
)
SELECT
    complaints.complaint_id,
    complaints.complainant_id,
    'complainant',
    'submitted',
    'Pending',
    'Complaint submitted by complainant.',
    complaints.created_at
FROM complaints
WHERE NOT EXISTS (
    SELECT 1
    FROM complaint_updates
    WHERE complaint_updates.complaint_id = complaints.complaint_id
)");

include(__DIR__ . '/../includes/seed_superadmin.php');
?>
