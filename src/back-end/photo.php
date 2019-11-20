<?php

require_once __DIR__ . "/lib/Upload.php";
require_once __DIR__ . "/data/Data.php";

class Photo {

    public static function resize($filename) {
        list($w, $h) = getimagesize($filename);
        $rate = sqrt( pow(640,2) + pow(960, 2) ) / sqrt( pow($w,2) + pow($h,2) );
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

    public static function save() {
        $uye_id = ( isset($_GET["uye_id"]) ? intval(  trim($_GET["uye_id"]) ) : 0 );
        $up = new Upload();
        $up->addAllowedExtension("jpg");
        $up->addAllowedExtension("jpeg");
        $p = $up->save('file', "assets/photos", true);
        if ($p != FALSE) {
            $fn = "assets/photos/$p";
            if (self::resize($fn)) {
                try {
                    $data = new Data();
                    $data->photo_save($uye_id,$p);
                    echo "0 ".$p;
                } catch( Exception $ex ) {
                    echo "3";
                }
            } else {
                echo "2";
            }
        } else {
            echo "1 ".$up->getLastError();
        }
    }
}