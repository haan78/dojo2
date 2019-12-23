<?php

class Mail {
    public static function smtp() {
        $mail = new PHPMailer();
    
        $mail->IsSMTP();
        $mail->SMTPDebug = 0;
        $mail->CharSet = "UTF-8";
    
        $mail->Host = "tls://smtp.gmail.com:587";
        $mail->Username = "ankarakendoiaido@gmail.com";
        $mail->Password = "";
        $mail->SMTPAuth = true;
    
        $mail->From = $mail->Username;
        $mail->FromName = 'Rehber Eczanem Sistemi';
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