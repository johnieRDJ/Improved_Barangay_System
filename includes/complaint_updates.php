<?php

function addComplaintUpdate(
    mysqli $conn,
    int $complaintId,
    ?int $actorUserId,
    string $actorRole,
    string $updateType,
    string $statusSnapshot,
    string $message,
    ?string $proofPath = null,
    ?string $proofOriginalName = null
): void
{
    $safeActorRole = mysqli_real_escape_string($conn, $actorRole);
    $safeUpdateType = mysqli_real_escape_string($conn, $updateType);
    $safeStatusSnapshot = mysqli_real_escape_string($conn, $statusSnapshot);
    $safeMessage = mysqli_real_escape_string($conn, $message);
    $safeProofPath = $proofPath !== null ? "'" . mysqli_real_escape_string($conn, $proofPath) . "'" : 'NULL';
    $safeProofOriginalName = $proofOriginalName !== null ? "'" . mysqli_real_escape_string($conn, $proofOriginalName) . "'" : 'NULL';
    $actorUserIdValue = $actorUserId === null ? 'NULL' : "'" . intval($actorUserId) . "'";

    mysqli_query(
        $conn,
        "INSERT INTO complaint_updates (
            complaint_id,
            actor_user_id,
            actor_role,
            update_type,
            status_snapshot,
            message,
            proof_path,
            proof_original_name
        ) VALUES (
            '" . intval($complaintId) . "',
            $actorUserIdValue,
            '$safeActorRole',
            '$safeUpdateType',
            '$safeStatusSnapshot',
            '$safeMessage',
            $safeProofPath,
            $safeProofOriginalName
        )"
    );
}
?>
