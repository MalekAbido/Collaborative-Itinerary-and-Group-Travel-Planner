<?php
namespace App\Services;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    public static function send($toEmail, $subject, $htmlBody)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host        = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth    = true;

                                                                // UNCOMMENT THESE TWO LINES:
            $mail->AuthType   = 'LOGIN';                        // Forces the method Brevo expects
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Required for Port 587

            $mail->Username = $_ENV['MAIL_USERNAME'];
            $mail->Password = $_ENV['MAIL_PASSWORD'];
            $mail->Port     = $_ENV['MAIL_PORT'];

            $mail->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);
            $mail->addAddress($toEmail);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;

            $mail->send();
            return true;
        } catch (Exception $e) {
            die("Email failed to send. Error: {$mail->ErrorInfo}");
        }
    }
}
