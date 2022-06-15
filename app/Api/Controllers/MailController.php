<?php

namespace Api\Controllers;

use PHPMailer\PHPMailer\PHPMailer;

class MailController
{
    private $mail;

    public function __construct()
    {
        $ini = parse_ini_file(__DIR__."/../Config/mail.ini");
        $this->mail = new PHPMailer();
        $this->mail->isSMTP();
        $this->mail->Host = "smtp.gmail.com";
        $this->mail->Port = 587;
        $this->mail->SMTPAuth = true;
        $this->mail->SMTPSecure = "tls";
        $this->mail->Username = $ini['Username'];
        $this->mail->Password = $ini['Password'];
        $this->mail->setFrom($ini['Username'], $ini['Title']);
    }

    public function sendMail($message, $altMessage, $isHTMl, $subject, $toMail)
    {
        $this->mail->addAddress($toMail);
        $this->mail->isHTML($isHTMl);
        $this->mail->Body = $message;
        $this->mail->AltBody = $altMessage;
        $this->mail->Subject = $subject;
        $this->mail->AltBody = $message;
        return $this->mail->send();
    }
    public function sendOtpMail($otp, $toMail)
    {
        
        $mailHTML = str_replace("\$\$OTP\$\$",$otp,file_get_contents("Mail-Template.html"));

        return $this->sendMail(
            $mailHTML,
            "Your OTP code is : $otp",
            true,
            "OTP Code",
            $toMail
        );
    }
}
