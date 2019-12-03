<?php

require_once __DIR__ . "/abstracts/AUser.php";
require_once __DIR__ . "/data.php";

class user extends AUser
{
    public static function resetPassword($resetCode,$newPass) {
        return false;
    }

    public static function sendPassworResetdCode($user_id) {
        return false;
    }

    public static function setPassword($old, $new)
    {
        $kullanici = self::checkData()["kullanici"];
        $data = new Data();
        $data->password($kullanici,$old,$new);
        return true;
    }

    public static function getUserData($user_id,$pass)
    {
        //Check user data from database and return an object or an array. if it fails return null.
        $data = new Data();
        $yetki = $data->yetkilendir($user_id,$pass);
        if ( $yetki !== false ) {
            return [
                "kullanici" => $user_id,
                "yetki" => $yetki
            ];
        } else {
            return null;
        }
    }    
}
