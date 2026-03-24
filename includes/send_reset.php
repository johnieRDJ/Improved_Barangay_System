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
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'argierydertz@gmail.com';
        $mail->Password = 'xygl mvhd jfpv sjjx';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('argierydertz@gmail.com', 'Barangay System');
        $mail->addAddress($email);

        $link = "http://localhost/barangay/auth/reset_password.php?token=$token";

        $mail->isHTML(true);
        $mail->Subject = 'Reset Your Password';

        $mail->Body = "
        <h3>Password Reset Request</h3>
        <p>Click the link below to reset your password:</p>
        <a href='$link'>$link</a>
        <p>This link expires in 15 minutes.</p>
        ";

        $mail->send();

    } catch (Exception $e){
        echo "Error: {$mail->ErrorInfo}";
    }
}
?>