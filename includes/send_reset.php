<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

function sendResetLink($email, $token){

    $mail = new PHPMailer(true);

    try{

        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'argierydertz@gmail.com';
        $mail->Password   = 'xygl mvhd jfpv sjjx';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('argierydertz@gmail.com', 'Barangay Digital Complaint System');
        $mail->addAddress($email);

        // 🔴 CHANGE THIS IF ONLINE (IMPORTANT)
        $link = rtrim(APP_URL, '/') . "/auth/reset_password.php?token=" . urlencode($token);

        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';

        $mail->Body = "
        <div style='font-family:Arial;'>
            <h2>Password Reset</h2>
            <p>You requested to reset your password.</p>

            <p>
                <a href='$link' 
                   style='background:#007bff;color:white;padding:10px 15px;text-decoration:none;border-radius:5px;'>
                   Reset Password
                </a>
            </p>

            <p>If the button doesn't work, copy this link:</p>
            <p>$link</p>

            <p style='color:red;'><strong>This link will expire in 15 minutes.</strong></p>

            <hr>
            <small>If you did not request this, please ignore this email.</small>
        </div>
        ";

        $mail->send();

    } catch (Exception $e){
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
}
?>
