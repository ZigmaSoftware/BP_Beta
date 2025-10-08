<?php

ini_set("error_display", 1);
error_reporting(E_ALL);
// testmail.php — place this in C:\xampp\htdocs\ and visit http://localhost/testmail.php

// --- CONFIGURE THESE ---
$from    = 'suswinalt@gmail.com';            // must match auth_username in sendmail.ini
$to      = 'psuswin00@gmail.com';            // the address you’re testing with
$subject = 'XAMPP Sendmail Test ' . date('Y-m-d H:i:s');

// --- BUILD MESSAGE ---
$html_body = <<<HTML
<html>
  <body>
    <h2>Sendmail Test</h2>
    <p>This is a <strong>test email</strong> sent from <em>XAMPP</em> using <code>sendmail.exe</code>.</p>
    <p>Date: {date('Y-m-d H:i:s')}</p>
  </body>
</html>
HTML;

$text_body = "Sendmail Test\n\n"
           . "This is a test email sent from XAMPP using sendmail.exe.\n\n"
           . "Date: " . date('Y-m-d H:i:s') . "\n";

// --- HEADERS (HTML) ---
$headers  = 'Date: ' . date('r') . "\r\n";
$headers .= 'Message-ID: <' . uniqid('', true) . '@zigmaglobal.in>' . "\r\n";
$headers .= 'From: ' . $from . "\r\n";
$headers .= 'Reply-To: ' . $from . "\r\n";
$headers .= 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-Type: multipart/alternative; boundary="=_Boundary123"' . "\r\n";

// --- BUILD MULTIPART BODY ---
$body  = "--=_Boundary123\r\n";
$body .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
$body .= $text_body . "\r\n";
$body .= "--=_Boundary123\r\n";
$body .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
$body .= $html_body . "\r\n";
$body .= "--=_Boundary123--\r\n";

// --- SEND MAIL ---
$success = mail(
    $to,
    $subject,
    $body,
    $headers,
    '-f ' . $from       // envelope sender flag
);

// --- OUTPUT RESULT ---
if ($success) {
    echo '<p style="color:green">Mail sent successfully to <strong>' 
         . htmlspecialchars($to) . '</strong>.</p>';
} else {
    echo '<p style="color:red">Mail sending FAILED.</p>';
}
