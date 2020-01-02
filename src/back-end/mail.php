<?php

require_once __DIR__ . "/PHPMailer/src/PHPMailer.php";
require_once __DIR__ . "/PHPMailer/src/SMTP.php";
require_once __DIR__ . "/PHPMailer/src/Exception.php";


use \PHPMailer\PHPMailer\PHPMailer;

class Mail {
    public static function smtp() {
        $mail = new PHPMailer();
    
        $mail->IsSMTP();
        $mail->SMTPDebug = 0;
        $mail->CharSet = "UTF-8";
    
        $mail->Host = "tls://smtp.gmail.com:587";
        /*$mail->SMTPOptions = array(
            'ssl' => array(
              'verify_peer' => false,
              'verify_peer_name' => false,
              'allow_self_signed' => true
             )
         );*/
        $mail->Username = "ankarakendo2007@gmail.com";
        $mail->Password = "akendo@@77";
        $mail->SMTPAuth = true;
    
        $mail->From = $mail->Username;
        $mail->FromName = 'Ankara Kendo Kulübü';
        $mail->IsHTML(true);
        return $mail;
    }
    
    public static function to($eposta,$name,$title,$body,&$err) {
        $mail = self::smtp();
        $mail->Subject = $title;
        $mail->MsgHTML($body);
        $mail->AddAddress($eposta,$name);
        if ($mail->Send()) {
            $err = null;
            return true;
        } else {
            $err = $mail->ErrorInfo;
            return false;
        }
    }
}