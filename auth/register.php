<?php
include('../config/database.php');
include('../includes/send_otp.php');

$register_error = '';
$register_success = '';

if(isset($_POST['register'])){
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? '';

    if($firstname === '' || $lastname === '' || $email === '' || $password === '' || $confirm_password === ''){
        $register_error = 'Please complete all required fields.';
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $register_error = 'Please enter a valid email address.';
    } elseif(!in_array($role, ['complainant', 'staff'], true)){
        $register_error = 'Please select a valid role.';
    } elseif($password !== $confirm_password){
        $register_error = 'Passwords do not match.';
    } else {
        $existingUser = db_select_one(
            $conn,
            "SELECT user_id FROM users WHERE email=? LIMIT 1",
            's',
            [$email]
        );

        if($existingUser){
            $register_error = 'Email already exists.';
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $token = bin2hex(random_bytes(16));

            db_execute(
                $conn,
                "INSERT INTO users (firstname, lastname, email, password, role)
                 VALUES (?, ?, ?, ?, ?)",
                'sssss',
                [$firstname, $lastname, $email, $password_hash, $role]
            );

            $user_id = mysqli_insert_id($conn);

            if($user_id > 0){
                db_execute(
                    $conn,
                    "INSERT INTO user_auth (user_id, email_verified, verification_token)
                     VALUES (?, 0, ?)",
                    'is',
                    [$user_id, $token]
                );

                db_execute(
                    $conn,
                    "INSERT INTO user_profiles (user_id)
                     VALUES (?)",
                    'i',
                    [$user_id]
                );

                db_execute(
                    $conn,
                    "INSERT INTO residency (user_id, status)
                     VALUES (?, ?)",
                    'is',
                    [$user_id, 'pending']
                );

                $link = rtrim(APP_URL, '/') . "/auth/verify_email.php?token=" . urlencode($token);
                $fullname = trim($firstname . ' ' . $lastname);
                sendRegistrationVerificationEmail($email, $fullname, $role, $link);

                $register_success = 'Registration successful. Check your email for the verification message and next steps.';
            } else {
                $register_error = 'Unable to create account. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/script.js"></script>
</head>
<body>

<div class="container">
    <h2>Register</h2>

    <?php if($register_error !== ''): ?>
        <p style="color:#b91c1c; font-weight:600;"><?php echo htmlspecialchars($register_error, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <?php if($register_success !== ''): ?>
        <p style="color:#15803d; font-weight:600;"><?php echo htmlspecialchars($register_success, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <form method="POST" onsubmit="return validateRegister()">
        <input type="text" name="firstname" placeholder="First Name" required>
        <input type="text" name="lastname" placeholder="Last Name" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" id="password" name="password" placeholder="Password" required>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>

        <select name="role" required>
            <option value="">Select Role</option>
            <option value="complainant">Complainant</option>
            <option value="staff">Staff (Recipient)</option>
        </select>

        <button type="submit" name="register">Register</button>
    </form>

    <div class="link">
        <a href="login.php">Already have an account? Login</a>
    </div>
</div>

</body>
</html>
