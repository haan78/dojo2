<?php

require_once  __DIR__ . "/../definitions/Settings.php";

class AUser
{
    public static function checkData()
    {
        //Get user information from session if it is not success return null

        if (!isset($_SESSION)) {
            session_start();
        }

        return (  isset($_SESSION["DATA"]) && isset($_SESSION["APPID"]) && $_SESSION["APPID"] == SETTING_ID ? json_decode($_SESSION["DATA"],true) : null );
    }

    public static function saveData($data)
    {
        //Save user information in session. return void;
        if (!isset($_SESSION)) {
            session_start();
        }

        $_SESSION["DATA"] = json_encode($data);
        $_SESSION["APPID"] = SETTING_ID;
    }

    public static function removeData()
    {
        //Remove user information from session. return void;
        if (!isset($_SESSION)) {
            session_start();
        }

        session_destroy();
        
    }        
}
