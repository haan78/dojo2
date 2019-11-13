<?php

require_once  __DIR__ . "/abstracts/AUser.php";

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
        //Change user password. return bool
        return false;
    }

    public static function getUserData($user_id,$pass)
    {
        //Check user data from database and return an object or an array. if it fails return null.
        return [
            "name"=>"Ali Barış Öztürk"
        ];
    }    
}
