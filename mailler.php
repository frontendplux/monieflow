<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include_once  __DIR__ . '/vendor/autoload.php';

function sendMail(
    string $to,
    string $subject,
    string $body
): bool {

    $mail = new PHPMailer(true);

    try {

        // SMTP configuration
        $mail->isSMTP();

        $mail->Host       = 'server143.web-hosting.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'info@air9ja.com';
        $mail->Password   = 'Samuel252.';

        // Usually 465 for SSL or 587 for TLS
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Sender
        $mail->setFrom(
            'info@air9ja.com',
            'MonieFlow'
        );

        // Recipient
        $mail->addAddress($to);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        // Optional plain-text version
        $mail->AltBody = strip_tags($body);

        return $mail->send();

    } catch (Exception $e) {

        error_log('Mailer Error: ' . $mail->ErrorInfo);

        return false;
    }
}
