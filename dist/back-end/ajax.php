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

    public function uye($uye,$uye_tur,$dogum_tarihi,$uyelik_tarihi,$aktif,$cinsiyet,$eposta,$ekf_no,$uye_id = 0) { //Admin
        $data = new Data();
        return $data->uye($uye,$uye_tur,$dogum_tarihi,$uyelik_tarihi,$aktif,$cinsiyet,$eposta,$ekf_no,$uye_id);
    }

    public function odeme_turleri() { //Admin
        $data = new Data();
        return $data->odeme_turleri();    
    }

    public function odeme_tur($odeme_tur,$tutar,$odeme_tur_id = false) { //Admin
        $data = new Data();
        return $data->odeme_tur($odeme_tur,$tutar,$odeme_tur_id);
    }

    public function aidat_eksigi($uye_id) {
        $data = new Data();
        return $data->aidat_eksigi($uye_id);
    }

    public function uye_odemeleri($uye_id,$odeme_tur_id,$s,$l,&$total,&$maxrow) {
        $data = new Data();
        return $data->uye_odemeleri($uye_id,$odeme_tur_id,$s,$l,$total,$maxrow);
    }

    public function  odeme($uye_id,$tarih,$yil,$ay,$tutar,$odeme_tur_id,$aciklama,$odeme_id = false) { //USER
        $data = new Data();
        return $data->odeme($uye_id,$tarih,$yil,$ay,$tutar,$odeme_tur_id,$aciklama,$odeme_id);
    }

    public function odeme_sil($odeme_id) { //USER
        $data = new Data();
        return $data->odeme_sil($odeme_id);
    }

    public function uyenin_sinavlari($uye_id) {
        $data = new Data();
        return $data->uyenin_sinavlari($uye_id);
    }

    public function seviye($uye_id,$tarih,$tanim,$detaylar,$seviye_id = false) { //USER
        $data = new Data();
        return $data->seviye($uye_id,$tarih,$tanim,$detaylar,$seviye_id);
    }

    public function sinav_sil($seviye_id) { //USER
        $data = new Data();
        return $data->sinav_sil($seviye_id);
    }

    public function uyenin_yoklamalari($uye_id,$s,$l,&$maxrow) {
        $data = new Data();
        return $data->uyenin_yoklamalari($uye_id,$s,$l,$maxrow);
    }

    public function uye_yoklama_ekle($uye_id,$tarih) { //USER
        $data = new Data();
        return $data->uye_yoklama_ekle($uye_id,$tarih);
    }

    public function uye_yoklama_sil($yoklama_id) { //USER
        $data = new Data();
        return $data->uye_yoklama_sil($yoklama_id);
    }

    public function kullanicilar() {
        $data = new Data();
        return $data->kullanicilar();
    }

    public function kullanici($kullanici,$yetki,$pass = null,$kullanici_id = false) {
        $data = new Data();
        return $data->kullanici($kullanici,$yetki,$pass,$kullanici_id);
    }

    public function kullanici_sil($kullanici_id) {
        $data = new Data();
        return $data->kullanici_sil($kullanici_id);
    }

}