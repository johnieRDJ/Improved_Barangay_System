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
    db_execute(
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
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?
        )",
        'iissssss',
        [
            intval($complaintId),
            $actorUserId,
            $actorRole,
            $updateType,
            $statusSnapshot,
            $message,
            $proofPath,
            $proofOriginalName
        ]
    );
}
?>
