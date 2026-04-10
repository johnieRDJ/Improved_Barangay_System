<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../phpmailer/src/Exception.php';
require_once '../phpmailer/src/PHPMailer.php';
require_once '../phpmailer/src/SMTP.php';

function sendResetLink($email, $fullname, $token){

    $mail = new PHPMailer(true);
    $safeFullname = htmlspecialchars($fullname, ENT_QUOTES, 'UTF-8');
    $safeEmail = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    $link = rtrim(APP_URL, '/') . "/auth/reset_password.php?token=" . urlencode($token);

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

        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';

        $mail->Body = "
        <div style='font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;'>
            <h2 style='margin-bottom: 8px;'>Password Reset Request</h2>
            <p>Hello <strong>$safeFullname</strong>,</p>
            <p>We received a request to reset the password for the account registered with <strong>$safeEmail</strong>.</p>

            <p style='margin: 20px 0;'>
                <a href='$link' 
                   style='display: inline-block; background: #0f766e; color: #ffffff; padding: 12px 20px; text-decoration: none; border-radius: 8px; font-weight: bold;'>
                   Reset Password
                </a>
            </p>

            <p>This link will expire in <strong>15 minutes</strong>.</p>
            <p>If you did not request this, you can safely ignore this email and your password will stay unchanged.</p>
            <p>If the button does not work, copy and paste this link into your browser:</p>
            <p style='word-break: break-all;'><a href='$link'>$link</a></p>
        </div>
        ";

        $mail->AltBody = "Hello $fullname,\n\nWe received a request to reset the password for the account registered with $email.\n\nReset your password here:\n$link\n\nThis link will expire in 15 minutes.\nIf you did not request this, you can safely ignore this email.";

        $mail->send();

    } catch (Exception $e){
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
}
?>
