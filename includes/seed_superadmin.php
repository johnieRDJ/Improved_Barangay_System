<?php
if(!isset($conn) || !($conn instanceof mysqli)){
    return;
}

function barangayTableExists(mysqli $conn, string $tableName): bool
{
    $escapedTableName = mysqli_real_escape_string($conn, $tableName);
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$escapedTableName'");

    return $result instanceof mysqli_result && mysqli_num_rows($result) > 0;
}

$requiredTables = ['users', 'user_profiles', 'user_auth', 'residency', 'logs'];

foreach($requiredTables as $requiredTable){
    if(!barangayTableExists($conn, $requiredTable)){
        return;
    }
}

$superadmin_email = 'superadmin@barangay.com';

$check_superadmin = mysqli_query($conn,
"SELECT user_id FROM users
 WHERE email='$superadmin_email'
 LIMIT 1");

if(mysqli_num_rows($check_superadmin) == 0){

    $superadmin_password = password_hash('superadmin123', PASSWORD_DEFAULT);

    mysqli_query($conn,
    "INSERT INTO users (firstname, lastname, email, password, role, account_status)
     VALUES ('Super','Admin','$superadmin_email','$superadmin_password','superadmin','approved')");

    $superadmin_id = mysqli_insert_id($conn);

    mysqli_query($conn,
    "INSERT INTO user_profiles (user_id)
     VALUES ('$superadmin_id')");

    mysqli_query($conn,
    "INSERT INTO user_auth (user_id, email_verified)
     VALUES ('$superadmin_id',1)");

    mysqli_query($conn,
    "INSERT INTO residency (user_id, status)
     VALUES ('$superadmin_id','verified')");

    mysqli_query($conn,
    "INSERT INTO logs (user_id, action)
     VALUES ('$superadmin_id','System created default superadmin account')");
}
?>
