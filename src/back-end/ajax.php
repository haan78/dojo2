<?php
require_once  __DIR__ . '/abstracts/AAjax.php';
require_once __DIR__ . "/data/Data.php";
class ajax extends AAjax {
    public function pass($old,$new) {
        if (!User::setPassword($old,$new)) {
            throw new \WebMethod\WebMethodException(__METHOD__,"Password changing has been failed ");
        }
    }

    public function uyeler($search,$aktif,$siralama,$s,$l,&$maxrow) {
        $data = new Data();
        return $data->uyeler($search,$aktif,$siralama,$s,$l,$maxrow);
    }

    public function uye_detay($uye_id) {
        $data = new Data();
        return $data->uye_detay($uye_id);
    }

    public function uye($uye,$uye_tur,$dogum_tarihi,$uyelik_tarihi,$aktif,$cinsiyet,$eposta,$ekf_no,$uye_id = 0) {
        $data = new Data();
        return $data->uye($uye,$uye_tur,$dogum_tarihi,$uyelik_tarihi,$aktif,$cinsiyet,$eposta,$ekf_no,$uye_id);
    }
}