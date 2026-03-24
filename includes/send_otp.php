<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

function sendOTP($email, $content){

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

        $mail->isHTML(true);

        // 🔥 Detect if OTP or message
        if(is_numeric($content)){
            $mail->Subject = 'Your OTP Code';
            $mail->Body = "
                <h3>Your OTP Code</h3>
                <h2>$content</h2>
                <p>This code will expire in 5 minutes.</p>
            ";
        } else {
            $mail->Subject = 'Barangay System Notification';
            $mail->Body = $content;
        }

        $mail->send();

    } catch (Exception $e){
        echo "Mailer Error: " . $mail->ErrorInfo;
    }


}
?>