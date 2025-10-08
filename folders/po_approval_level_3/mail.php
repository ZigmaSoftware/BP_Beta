<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php'; // adjust path as needed

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'zigma.in'; // Change if using Gmail: smtp.gmail.com
    $mail->SMTPAuth   = true;
    $mail->Username   = 'test@zigma.in';  // Your email
    $mail->Password   = 'r{AOAhKdRyIX';   // Your email password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Timeout to prevent infinite hang
    $mail->Timeout = 15;

    // Recipients
    $mail->setFrom('test@zigma.in', 'Zigma Mail Test');
    $mail->addAddress('psuswin00@gmail.com', 'Suswin');

    // Content
    $mail->isHTML(true);
    $mail->Subject = '✅ Test Mail - PHPMailer SMTP';
    $mail->Body    = '<h3>This is a test email from PHPMailer using SMTP.</h3><p>If you see this, SMTP works!</p>';
    $mail->AltBody = 'This is a test email from PHPMailer using SMTP.';

    // Send
    $mail->send();
    echo "✅ Test email sent successfully.";
} catch (Exception $e) {
    echo "❌ Mail Error: {$mail->ErrorInfo}";
}
