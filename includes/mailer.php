<?php
use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../phpmailer/src/Exception.php';
require_once __DIR__ . '/../phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../phpmailer/src/SMTP.php';

if(!function_exists('createBarangayMailer')){
    function createBarangayMailer(): PHPMailer{
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = app_config('mail.host', 'smtp.gmail.com');
        $mail->SMTPAuth = true;
        $mail->Username = app_config('mail.username', '');
        $mail->Password = app_config('mail.password', '');
        $mail->SMTPSecure = app_config('mail.encryption', 'tls');
        $mail->Port = intval(app_config('mail.port', 587));
        $mail->setFrom(
            app_config('mail.from_email', app_config('mail.username', '')),
            app_config('mail.from_name', 'Barangay Digital Complaint System')
        );
        $mail->isHTML(true);

        return $mail;
    }
}
?>
