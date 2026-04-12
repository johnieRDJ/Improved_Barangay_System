<?php
require_once __DIR__ . '/mailer.php';

function sendResidencySchedule($email, $fullname, $schedule){

$mail = createBarangayMailer();

try{

$mail->addAddress($email);
$mail->Subject = 'Barangay Residency Appointment Schedule';

$mail->Body = "
<h3>Barangay Residency Appointment</h3>

<p>Hello <b>$fullname</b>,</p>

<p>Your account requires residency verification.</p>

<p>Your appointment schedule is:</p>

<h2>$schedule</h2>

<p>Please visit the Barangay Office at the scheduled time and bring a valid ID.</p>

<p>Thank you.</p>

";

$mail->send();

}catch(Throwable $e){

echo "Mailer Error: " . $mail->ErrorInfo;

}

}
?>
