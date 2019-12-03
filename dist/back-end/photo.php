<?php

require_once __DIR__ . "/lib/Upload.php";
require_once __DIR__ . "/data.php";

class Photo {

    private static $width = 0;
    private static $height = 0;

    public static function setResizeValues($width = 640,$height = 960) {
        self::$width = $width;
        self::$height = $height;
    }

    public static function resize($filename) {
        if ( self::$width == 0 || self::$height == 0 ) {
            return true; //no resize
        }
        list($w, $h) = getimagesize($filename);
        $rate = sqrt( pow(self::$width,2) + pow(self::$height, 2) ) / sqrt( pow($w,2) + pow($h,2) );
        $nw = $rate*$w;
        $nh = $rate*$h;       
        $target = imagecreatetruecolor($nw, $nh);
        $source = @imagecreatefromjpeg($filename);
        if ( $source != FALSE ) {
            imagecopyresized($target, $source, 0, 0, 0, 0, $nw, $nh, $w, $h);
            return imagejpeg($target, $filename);
        } else {
            return false;
        }    
    }

    public static function save($folder) {
        $uye_id = ( isset($_GET["uye_id"]) ? intval(  trim($_GET["uye_id"]) ) : 0 );
        $up = new Upload();
        $up->addAllowedExtension("jpg");
        $up->addAllowedExtension("jpeg");
        $p = $up->save('file', $folder, true); //"assets/photos"
        if ($p != FALSE) {
            $fn = "$folder/$p";
            if (self::resize($fn)) {
                try {
                    $data = new Data();
                    $data->photo_save($uye_id,$p);
                    return "0 ".$p;
                } catch( Exception $ex ) {
                    return "3";
                }
            } else {
                return "2";
            }
        } else {
            return "1 ".$up->getLastError();
        }
    }
}