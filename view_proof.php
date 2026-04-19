<?php
session_start();

if(!isset($_SESSION['user_id'], $_SESSION['role'])){
    header("Location: auth/login.php");
    exit();
}

include(__DIR__ . '/config/database.php');

function showProofMessage(string $title, string $message, int $statusCode = 404): void
{
    http_response_code($statusCode);
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title><?php echo htmlspecialchars($title); ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
        <?php $styleVersion = file_exists(__DIR__ . '/css/style.css') ? filemtime(__DIR__ . '/css/style.css') : time(); ?>
        <link rel="stylesheet" href="css/style.css?v=<?php echo $styleVersion; ?>">
    </head>
    <body>
        <div class="page-shell" style="min-height:100vh; display:flex; align-items:center; justify-content:center;">
            <div class="table-card" style="max-width:640px;">
                <h1 style="text-align:left; margin-top:0;"><?php echo htmlspecialchars($title); ?></h1>
                <p><?php echo htmlspecialchars($message); ?></p>
                <button type="button" onclick="history.back()">Go Back</button>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit();
}

$updateId = intval($_GET['update_id'] ?? 0);

if($updateId <= 0){
    showProofMessage('Proof Not Found', 'The proof link is invalid.');
}

$update = db_select_one($conn,
"SELECT complaint_updates.update_id,
        complaint_updates.proof_path,
        complaint_updates.proof_original_name,
        complaints.complainant_id,
        complaints.assigned_staff_id
 FROM complaint_updates
 INNER JOIN complaints ON complaints.complaint_id = complaint_updates.complaint_id
 WHERE complaint_updates.update_id=?
 LIMIT 1",
 'i',
 [$updateId]);

if(!$update || empty($update['proof_path'])){
    showProofMessage('Proof Not Found', 'No proof file is attached to this update.');
}

$userId = intval($_SESSION['user_id']);
$role = $_SESSION['role'];
$canView = false;

if($role === 'complainant'){
    $canView = intval($update['complainant_id']) === $userId;
} elseif($role === 'staff'){
    $canView = intval($update['assigned_staff_id']) === $userId;
} elseif(in_array($role, ['admin', 'superadmin'], true)){
    $canView = true;
}

if(!$canView){
    showProofMessage('Access Denied', 'You are not allowed to view this proof file.', 403);
}

$proofPath = trim((string)$update['proof_path']);
$normalizedProofPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, ltrim($proofPath, "/\\"));
$allowedFolder = realpath(__DIR__ . '/uploads/complaint_proofs');
$filePath = realpath(__DIR__ . DIRECTORY_SEPARATOR . $normalizedProofPath);

if($allowedFolder === false || $filePath === false || strpos($filePath, $allowedFolder . DIRECTORY_SEPARATOR) !== 0 || !is_file($filePath)){
    showProofMessage(
        'Proof File Missing',
        'The database has a proof record, but the actual file is not in uploads/complaint_proofs on this server. Upload the proof file again or include the uploaded proof files when moving the system online.'
    );
}

$extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
$contentTypes = [
    'pdf' => 'application/pdf',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png'
];

if(!isset($contentTypes[$extension])){
    showProofMessage('Unsupported File', 'This proof file type cannot be opened here.', 415);
}

$displayName = $update['proof_original_name'] ?: basename($filePath);
$displayName = str_replace(['"', "\r", "\n"], '', $displayName);

if(ob_get_length()){
    ob_clean();
}

header('Content-Type: ' . $contentTypes[$extension]);
header('Content-Length: ' . filesize($filePath));
header('Content-Disposition: inline; filename="' . $displayName . '"');
header('X-Content-Type-Options: nosniff');
readfile($filePath);
exit();
?>
