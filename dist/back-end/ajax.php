<?php
require_once  __DIR__ . '/abstracts/AAjax.php';
require_once __DIR__ . "/data.php";
class ajax extends AAjax {

    private $yetki = null;

    public function __construct($yetki)
    {        
        $this->yetki = $yetki;
        parent::__construct();
    }

    protected function methodAuthorization($method, $params): bool {             
        require_once __DIR__ . "/methods.php";        
        return ( isset($methods[$method]) && in_array($this->yetki,$methods[$method]) );
    }

    public function pass($old,$new) { //OBSERVER
        if (!User::setPassword($old,$new)) {
            throw new \WebMethod\WebMethodException(__METHOD__,"Password changing has been failed ");
        }
    }

    public function uyeler($search,$aktif,$siralama,$s,$l,&$maxrow) { //OBSERVER
        $data = new Data();
        return $data->uyeler($search,$aktif,$siralama,$s,$l,$maxrow);
    }

    public function uye_detay($uye_id) { //OBSERVER
        $data = new Data();
        return $data->uye_detay($uye_id);
    }

    public function uye($uye,$uye_tur,$dogum_tarihi,$uyelik_tarihi,$aktif,$cinsiyet,$eposta,$ekf_no,$uye_id = 0) { //USER,ADMIN
        $data = new Data();
        return $data->uye($uye,$uye_tur,$dogum_tarihi,$uyelik_tarihi,$aktif,$cinsiyet,$eposta,$ekf_no,$uye_id);
    }

    public function odeme_turleri() { //OBSERVER
        $data = new Data();
        return $data->odeme_turleri();    
    }

    public function odeme_tur($odeme_tur,$tutar,$odeme_tur_id = false) { //USER,ADMIN
        $data = new Data();
        return $data->odeme_tur($odeme_tur,$tutar,$odeme_tur_id);
    }

    public function aidat_eksigi($uye_id) { //OBSERVER
        $data = new Data();
        return $data->aidat_eksigi($uye_id);
    }

    public function uye_odemeleri($uye_id,$odeme_tur_id,$s,$l,&$total,&$maxrow) { //OBSERVER
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

    public function uyenin_sinavlari($uye_id) { //OBSERVER
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

    public function uyenin_yoklamalari($uye_id,$s,$l,&$maxrow) { //OBSERVER
        $data = new Data();
        return $data->uyenin_yoklamalari($uye_id,$s,$l,$maxrow);
    }

    public function uye_yoklama_sil($yoklama_id) { //USER
        $data = new Data();
        return $data->uye_yoklama_sil($yoklama_id);
    }

    public function kullanicilar() { //OBSERVER
        $data = new Data();
        return $data->kullanicilar();
    }

    public function kullanici($kullanici,$yetki,$pass = null,$kullanici_id = false) { //ADMIN
        $data = new Data();
        return $data->kullanici($kullanici,$yetki,$pass,$kullanici_id);
    }

    public function kullanici_sil($kullanici_id) { //ADMIN
        $data = new Data();
        return $data->kullanici_sil($kullanici_id);
    }

    public function yoklamalar($tarih_s,$tarih_e,$s,$l,&$maxrow) {
        $data = new Data();
        return $data->yoklamalar($tarih_s,$tarih_e,$s,$l,$maxrow);
    }

    public function yoklamadaki_uyeler($tarih) {
        $data = new Data();
        return $data->yoklamadaki_uyeler($tarih);
    }

    public function yoklamaya_ekle($tarih,$uye_id) { //USER
        $data = new Data();
        return $data->yoklamaya_ekle($tarih,$uye_id);
    }

    public function yoklamadan_sil($tarih,$uye_id) { //USER
        $data = new Data();
        return $data->yoklamadan_sil($tarih,$uye_id);
    }

    public function uye_harcamalari($uye_id,$s,$l,&$maxrow,&$total) {
        $data = new Data();
        return $data->uye_harcamalari($uye_id,$s,$l,$maxrow,$total);
    }

    public function harcama($tarih,$uye_id,$tutar,$gider_tur_id,$belge,$aciklama,$gider_id = false) { //USER
        $data = new Data();
        return $data->harcama($tarih,$uye_id,$tutar,$gider_tur_id,$belge,$aciklama,$gider_id);
    }

    public function harcama_sil($gider_id) { //USER
        $data = new Data();
        return $data->harcama_sil($gider_id);
    }

    public function gider_turleri() {
        $data = new Data();
        return $data->gider_turleri();
    }

    public function gelirler($baslangic, $bitis, $s, $l) {
        $data = new Data();
        return $data->gelirler($baslangic, $bitis, $s, $l);
    }

    public function giderler($baslangic, $bitis, $s, $l) {
        $data = new Data();
        return $data->giderler($baslangic, $bitis, $s, $l);
    }

    public function genel_uye_raporu() {
        $data = new Data();
        return $data->genel_uye_raporu();
    }

    public function genel_durum($baslangic,$bitis) {
        $data = new Data();
        return $data->genel_durum($baslangic,$bitis);
    }

}