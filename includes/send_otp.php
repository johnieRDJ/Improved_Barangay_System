<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../phpmailer/src/Exception.php';
require_once '../phpmailer/src/PHPMailer.php';
require_once '../phpmailer/src/SMTP.php';

function createBarangayMailer(){

    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'argierydertz@gmail.com';
    $mail->Password   = 'xygl mvhd jfpv sjjx';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;
    $mail->setFrom('argierydertz@gmail.com', 'Barangay Digital Complaint System');
    $mail->isHTML(true);

    return $mail;
}

function sendOTP($email, $content){

    $mail = createBarangayMailer();

    try{

        $mail->addAddress($email);

        // 🔥 Detect if OTP or message
        if(is_numeric($content)){
            $verifyOtpLink = rtrim(defined('APP_URL') ? APP_URL : 'http://localhost/barangay', '/') . '/auth/verify_otp.php';
            $safeVerifyOtpLink = htmlspecialchars($verifyOtpLink, ENT_QUOTES, 'UTF-8');

            $mail->Subject = 'Your OTP Code';
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;'>
                    <h3 style='margin-bottom: 10px;'>Your OTP Code</h3>
                    <h2 style='margin: 0 0 12px;'>$content</h2>
                    <p style='margin-bottom: 16px;'>This code will expire in 5 minutes.</p>
                    <p style='margin-bottom: 10px;'>If the OTP page was closed after switching apps, use the link below to return and enter your OTP.</p>
                    <p style='margin-bottom: 18px;'>
                        <a href='$safeVerifyOtpLink' style='display: inline-block; background: #1d4f91; color: #ffffff; text-decoration: none; padding: 12px 20px; border-radius: 8px; font-weight: 600;'>
                            Open Verify OTP Page
                        </a>
                    </p>
                    <p style='margin-bottom: 4px;'>Verify OTP link:</p>
                    <p style='margin-top: 0; word-break: break-all;'>
                        <a href='$safeVerifyOtpLink'>$safeVerifyOtpLink</a>
                    </p>
                </div>
            ";
            $mail->AltBody = "Your OTP code is $content. This code will expire in 5 minutes.\n\nIf the OTP page was closed after switching apps, go back here:\n$verifyOtpLink";
        } else {
            $mail->Subject = 'Barangay System Notification';
            $mail->Body = $content;
            $mail->AltBody = strip_tags(str_replace('<br>', "\n", $content));
        }

        $mail->send();

    } catch (Exception $e){
        echo "Mailer Error: " . $mail->ErrorInfo;
    }


}

function sendRegistrationVerificationEmail($email, $fullname, $role, $verificationLink){

    $mail = createBarangayMailer();

    $safeFullname = htmlspecialchars($fullname, ENT_QUOTES, 'UTF-8');
    $safeEmail = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    $safeRole = htmlspecialchars(ucfirst($role), ENT_QUOTES, 'UTF-8');
    $safeLink = htmlspecialchars($verificationLink, ENT_QUOTES, 'UTF-8');
    $registeredAt = date('F j, Y g:i A');

    try{

        $mail->addAddress($email);
        $mail->Subject = 'Verify Your Barangay Account';
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;'>
                <h2 style='margin-bottom: 8px;'>Welcome to the Barangay Digital Complaint System</h2>
                <p>Hello <strong>$safeFullname</strong>,</p>
                <p>Your account registration has been received. Please review your details below and verify your email address to continue.</p>

                <div style='background: #f8fafc; border: 1px solid #dbe3ea; border-radius: 10px; padding: 16px; margin: 18px 0;'>
                    <p style='margin: 0 0 8px;'><strong>Registered name:</strong> $safeFullname</p>
                    <p style='margin: 0 0 8px;'><strong>Email address:</strong> $safeEmail</p>
                    <p style='margin: 0 0 8px;'><strong>Role selected:</strong> $safeRole</p>
                    <p style='margin: 0;'><strong>Registration date:</strong> $registeredAt</p>
                </div>

                <p style='margin-bottom: 18px;'>For your security, your password is not included in this email.</p>

                <p style='margin-bottom: 22px;'>
                    <a href='$safeLink' style='display: inline-block; background: #0f766e; color: #ffffff; text-decoration: none; padding: 12px 22px; border-radius: 8px; font-weight: bold;'>
                        Verify Email
                    </a>
                </p>

                <p style='margin-bottom: 8px;'>After email verification, your account will stay pending until an administrator approves it.</p>
                <p style='margin-bottom: 8px;'>If the button does not work, copy and paste this link into your browser:</p>
                <p style='word-break: break-all; margin-top: 0;'><a href='$safeLink'>$safeLink</a></p>
            </div>
        ";

        $mail->AltBody = "Hello $fullname,\n\nYour registration has been received.\nEmail: $email\nRole: " . ucfirst($role) . "\nRegistered at: $registeredAt\n\nVerify your email here:\n$verificationLink\n\nFor your security, your password is not included in this email.\nAfter verification, wait for admin approval.";

        $mail->send();

    } catch (Exception $e){
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
}
?>
