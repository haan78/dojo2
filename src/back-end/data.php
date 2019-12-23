<?php

require_once __DIR__ . "/lib/SQLite3Tool/SQLite3Ex.php";
require_once __DIR__ . "/lib/SQLite3Tool/SQLite3Paging.php";

class Data
{
    private $conn;
    public static function link(): SQLite3Tool\SQLite3Ex
    {
        $conn = new \SQLite3Tool\SQLite3Ex( SETTING_DBFILE );
        return $conn;
    }

    public function __construct()
    {
        $this->conn = self::link();
    }

    public function __destruct()
    {
        $this->conn->close();
    }

    public function photo_save($uye_id, $photo)
    {
        $stmt = $this->conn->prepare("UPDATE uye SET photo = :p WHERE uye_id = :id");
        $stmt->bindParam("p", $photo, SQLITE3_TEXT);
        $stmt->bindParam("id", $uye_id, SQLITE3_INTEGER);
        $stmt->execute();
    }

    public function uye($uye, $uye_tur, $dogum_tarihi, $uyelik_tarihi, $aktif, $cinsiyet, $eposta, $ekf_no, $uye_id = 0)
    {
        $row = [
            "uye" => $uye,
            "uye_tur" => $uye_tur,
            "dogum_tarihi" => $dogum_tarihi,
            "uyelik_tarihi" => $uyelik_tarihi,
            "aktif" => $aktif,
            "cinsiyet" => $cinsiyet,
            "eposta" => $eposta,
            "ekf_no" => $ekf_no
        ];
        if (intval($uye_id) != 0) $row["uye_id"] = $uye_id;
        return $this->conn->table("uye", $row);
    }

    public function uye_detay($uye_id)
    {
        $sql = "SELECT u.uye_id,u.uye,u.uye_tur,u.uyelik_tarihi,u.aktif,u.cinsiyet,u.eposta,u.dogum_tarihi,u.photo,u.ekf_no FROM uye u WHERE u.uye_id = :uid";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("uid", $uye_id, SQLITE3_INTEGER);
        $arr =  $this->conn->resultToArray($stmt->execute());
        if (isset($arr[0])) {
            return $arr[0];
        } else {
            throw new Exception("Uye bulunamadi");
        }
    }

    public function yoklamaya_ekle($tarih, $uye_id)
    {
        $stmt = $this->conn->prepare("INSERT INTO yoklama (uye_id,tarih) VALUES (:u,:t)");
        $stmt->bindParam("t", $tarih, SQLITE3_TEXT);
        $stmt->bindParam("u", $uye_id, SQLITE3_INTEGER);
        $stmt->execute();
        return true;
    }

    public function yoklamadan_sil($tarih, $uye_id)
    {
        $stmt = $this->conn->prepare("DELETE FROM yoklama WHERE tarih = :t AND uye_id = :u");
        $stmt->bindParam("t", $tarih, SQLITE3_TEXT);
        $stmt->bindParam("u", $uye_id, SQLITE3_INTEGER);
        $stmt->execute();
        return true;
    }

    public function uye_yoklama_sil($yoklama_id) {
        $stmt = $this->conn->prepare("DELETE FROM yoklama WHERE yoklama_id = :yid");        
        $stmt->bindParam("yid", $yoklama_id, SQLITE3_INTEGER);
        $stmt->execute();
        return true;
    }

    public function yoklamalar($tarih_s, $tarih_e, $s, $l, &$maxrow)
    {

        $ts = ($tarih_s == "" ? null : $tarih_s);
        $te = ($tarih_e == "" ? null : $tarih_e);

        $p = new \SQLite3Tool\SQLite3Paging($this->conn, "SELECT y.tarih,COUNT(1) AS sayi FROM yoklama y WHERE y.tarih BETWEEN COALESCE(:ts,y.tarih) AND COALESCE(:te,y.tarih) GROUP BY y.tarih ORDER BY y.tarih DESC", $s, $l);
        $p->bindParam("ts", $ts, SQLITE3_TEXT);
        $p->bindParam("te", $te, SQLITE3_TEXT);
        return $p->result($maxrow);
    }

    public function uyenin_yoklamalari($uye_id, $s, $l, &$maxrow)
    {
        $p = new \SQLite3Tool\SQLite3Paging($this->conn, "SELECT y.tarih,y.yoklama_id FROM yoklama y WHERE y.uye_id = :uid ORDER BY y.tarih DESC", $s, $l);
        $p->bindParam("uid", $uye_id, SQLITE3_INTEGER);
        return $p->result($maxrow);
    }

    public function yoklamadaki_uyeler($tarih)
    {
        $yoklamadaki_uyeler_stmt = $this->conn->prepare("SELECT y.yoklama_id,y.uye_id,u.uye,u.photo FROM yoklama y INNER JOIN uye u ON u.uye_id = y.uye_id WHERE y.tarih = :t ORDER BY u.uye ASC LIMIT 100");
        $yoklamadaki_uyeler_stmt->bindParam("t", $tarih, SQLITE3_TEXT);
        $yoklamadaki_uyeler = $this->conn->resultToArray($yoklamadaki_uyeler_stmt->execute());
        $secilebilecek_uyeler_stmt = $this->conn->prepare("SELECT u.uye_id,u.uye,u.photo FROM uye u LEFT JOIN yoklama y ON u.uye_id = y.uye_id AND y.tarih = :t WHERE u.aktif = 1 AND y.uye_id IS NULL ORDER BY u.uye ASC LIMIT 100");
        $secilebilecek_uyeler_stmt->bindParam("t", $tarih, SQLITE3_TEXT);
        $secilebilecek_uyeler = $this->conn->resultToArray($secilebilecek_uyeler_stmt->execute());
        return [
            "aktifler" => $secilebilecek_uyeler,
            "gelenler" => $yoklamadaki_uyeler
        ];
    }

    public function aktif_uyeler()
    {
        $sql = "SELECT u.uye_id,u.uye 
        
        ,(SELECT COUNT(1) FROM (
                        SELECT 
	CAST( STRFTIME('%Y',y.tarih) AS INTEGER) AS yil,
	CAST( STRFTIME('%m',y.tarih) AS INTEGER) AS ay
	FROM yoklama y 
	LEFT JOIN odeme o ON o.uye_id = y.uye_id AND o.yil = CAST( STRFTIME('%Y',y.tarih) AS INTEGER) AND o.ay = CAST( STRFTIME('%m',y.tarih) AS INTEGER)
		WHERE y.uye_id = u.uye_id AND o.odeme_id IS NULL
			GROUP BY CAST( STRFTIME('%Y',y.tarih) AS INTEGER),CAST( STRFTIME('%m',y.tarih) AS INTEGER)
				HAVING CAST(STRFTIME('%m',date('now')) AS INTEGER) + ( CAST(STRFTIME('%Y',date('now')) AS INTEGER) * 12) > (CAST( STRFTIME('%Y',y.tarih) AS INTEGER)*12)+CAST( STRFTIME('%m',y.tarih) AS INTEGER)
                ) q LIMIT 1) AS aidat_eksigi 

        FROM uye u WHERE u.aktif = 1 ORDER BY locale(u.uye,'tr_TR') ASC LIMIT 1000";
        $stmt = $this->conn->prepare($sql);
        return $this->conn->resultToArray($stmt->execute());
    }

    public function aidat_eksigi($uye_id)
    {
        $sql = "SELECT * FROM (
            SELECT 
	CAST( STRFTIME('%Y',y.tarih) AS INTEGER) AS yil,
	CAST( STRFTIME('%m',y.tarih) AS INTEGER) AS ay
	FROM yoklama y 
	LEFT JOIN odeme o ON o.uye_id = y.uye_id AND o.yil = CAST( STRFTIME('%Y',y.tarih) AS INTEGER) AND o.ay = CAST( STRFTIME('%m',y.tarih) AS INTEGER) AND o.odeme_tur_id IN (1,2)
		WHERE y.uye_id = :uid AND o.odeme_id IS NULL
			GROUP BY CAST( STRFTIME('%Y',y.tarih) AS INTEGER),CAST( STRFTIME('%m',y.tarih) AS INTEGER)
				HAVING CAST(STRFTIME('%m',date('now')) AS INTEGER) + ( CAST(STRFTIME('%Y',date('now')) AS INTEGER) * 12) > (CAST( STRFTIME('%Y',y.tarih) AS INTEGER)*12)+CAST( STRFTIME('%m',y.tarih) AS INTEGER)
                                ) q ORDER BY q.yil DESC,q.ay DESC
                                ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("uid", $uye_id, SQLITE3_INTEGER);
        return $this->conn->resultToArray($stmt->execute());
    }

    public function odeme_tur($odeme_tur, $tutar, $odeme_tur_id = false)
    {
        $row = [
            "odeme_tur" => $odeme_tur,
            "tutar" => $tutar
        ];
        if ($odeme_tur_id != false) $row["odeme_tur_id"] = $odeme_tur_id;
        return $this->conn->table("odeme_tur", $row);
    }

    public function odeme_turleri()
    {
        $sql = "SELECT ot.* FROM odeme_tur ot ORDER BY ot.odeme_tur ASC";
        $stmt = $this->conn->prepare($sql);
        return $this->conn->resultToArray($stmt->execute());
    }

    public function kullanicilar()
    {
        $sql = "SELECT kullanici_id,kullanici,yetki FROM kullanici ORDER BY LOCALE(kullanici,'tr_TR') ASC LIMIT 100";
        $stmt = $this->conn->prepare($sql);
        return $this->conn->resultToArray($stmt->execute());
    }

    public function kullanici($kullanici, $yetki, $pass = null, $kullanici_id = false)
    {
        $row = [
            "kullanici" => $kullanici,
            "yetki" => $yetki
        ];
        if (!is_null($pass)) $row["parola"] = hash('ripemd160', $pass);
        if ($kullanici_id != false) $row["kullanici_id"] = $kullanici_id;
        return $this->conn->table("kullanici", $row);
    }

    public function password($kullanici, $eski, $yeni)
    {
        $stmt = $this->conn->prepare("UPDATE kullanici SET parola = :y WHERE kullanici = :k AND parola = :e ");
        $e = hash('ripemd160', $eski);
        $y = hash('ripemd160', $yeni);
        $k = $kullanici;
        $stmt->bindParam("y", $y, SQLITE3_TEXT);
        $stmt->bindParam("e", $e, SQLITE3_TEXT);
        $stmt->bindParam("k", $k, SQLITE3_TEXT);
        $stmt->execute();
    }

    public function yetkilendir($kullanici, $pass)
    {
        $stmt = $this->conn->prepare("SELECT yetki FROM kullanici WHERE LOWER(kullanici) = LOWER( :kullanici ) AND parola = :parola");
        $stmt->bindValue("kullanici", $kullanici, SQLITE3_TEXT);
        $stmt->bindValue("parola", hash('ripemd160', $pass), SQLITE3_TEXT);
        $arr = $this->conn->resultToArray($stmt->execute());

        //print_r($arr); die();
        if (is_array($arr) && count($arr) == 1) {
            return $arr[0]["yetki"];
        } else {
            return false;
        }
    }

    public function kullanici_sil($kullanici_id)
    {
        return $this->conn->table("kullanici", $kullanici_id);
    }

    public function odeme($uye_id, $tarih, $yil, $ay, $tutar, $odeme_tur_id, $aciklama, $odeme_id = false)
    {
        $row = [
            "uye_id" => $uye_id,
            "tarih" => $tarih,
            "yil" => $yil,
            "ay" => $ay,
            "tutar" => $tutar,
            "odeme_tur_id" => $odeme_tur_id,
            "aciklama" => ($aciklama == "" ? null : $aciklama)
        ];
        //var_dump($odeme_id);
        if ($odeme_id != false) $row["odeme_id"] = $odeme_id;
        //var_dump($row);
        return $this->conn->table("odeme", $row);
    }

    public function odeme_sil($odeme_id)
    {
        return $this->conn->table("odeme", $odeme_id);
    }

    public function uye_odemeleri($uye_id, $odeme_tur_id, $s, $l, &$total, &$maxrow)
    {
        $sqlc = "SELECT COUNT(1) AS `MAXROW`,COALESCE(SUM(o.tutar),0) AS `TOTAL` 
                    FROM odeme o
                        WHERE 
                            o.uye_id = :uid
                            AND o.odeme_tur_id = COALESCE(:ot,o.odeme_tur_id)";

        $stmtc = $this->conn->prepare($sqlc);
        $stmtc->bindParam("uid", $uye_id, SQLITE3_INTEGER);
        $stmtc->bindParam("ot", $odeme_tur_id, SQLITE3_INTEGER);
        $summary = $this->conn->resultToArray($stmtc->execute())[0];

        $sql = "SELECT 
                    o.odeme_id,
                    o.tutar,
                    o.tarih,
                    o.ay,
                    o.yil,
                    o.odeme_tur_id,
                    ot.odeme_tur,
                    o.aciklama
                    FROM odeme o 
                    INNER JOIN odeme_tur ot ON ot.odeme_tur_id = o.odeme_tur_id 
                        WHERE 
                            o.uye_id = :uid
                            AND o.odeme_tur_id = COALESCE(:ot,o.odeme_tur_id) 
                                ORDER BY o.tarih DESC 
                                    LIMIT :s,:l";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("uid", $uye_id, SQLITE3_INTEGER);
        $stmt->bindParam("ot", $odeme_tur_id, SQLITE3_INTEGER);
        $stmt->bindParam("s", $s, SQLITE3_INTEGER);
        $stmt->bindParam("l", $l, SQLITE3_INTEGER);

        $total = $summary["TOTAL"];
        $maxrow = $summary["MAXROW"];

        return $this->conn->resultToArray($stmt->execute());
    }

    public function odemeler($uye, $odeme_tur_id, $tarih_s, $tarih_e, $s, $l, &$total, &$maxrow)
    {

        $ts = ($tarih_s == "" ? null : $tarih_s);
        $te = ($tarih_e == "" ? null : $tarih_e);

        $sqlc = "SELECT COUNT(1) AS `MAXROW`,COALESCE(ROUND(SUM(o.tutar),2),0) AS `TOTAL` 
            FROM odeme o INNER JOIN uye u ON u.uye_id = o.uye_id 
                WHERE 
                    u.uye LIKE '%'||COALESCE(:u,'')||'%' 
                    AND o.odeme_tur_id = COALESCE(:ot,o.odeme_tur_id) 
                    AND (o.tarih BETWEEN COALESCE(:ts,o.tarih) AND COALESCE(:te,o.tarih))";

        $stmtc = $this->conn->prepare($sqlc);
        $stmtc->bindParam("ts", $ts, SQLITE3_TEXT);
        $stmtc->bindParam("te", $te, SQLITE3_TEXT);
        $stmtc->bindParam("u", $uye, SQLITE3_TEXT);
        $stmtc->bindParam("ot", $odeme_tur_id, SQLITE3_INTEGER);
        $summary = $this->conn->resultToArray($stmtc->execute())[0];

        $sql = "SELECT
                    o.odeme_id,
                    o.ay,
                    o.yil,
                    o.uye_id,
                    o.tutar,
                    o.tarih,
                    u.uye,
                    ot.odeme_tur,
                    o.aciklama
                    FROM odeme o 
                    INNER JOIN odeme_tur ot ON ot.odeme_tur_id = o.odeme_tur_id 
                    INNER JOIN uye u ON u.uye_id = o.uye_id 
                        WHERE 
                            u.uye LIKE '%'||COALESCE(:u,'')||'%' 
                            AND o.odeme_tur_id = COALESCE(:ot,o.odeme_tur_id) 
                            AND (o.tarih BETWEEN COALESCE(:ts,o.tarih) AND COALESCE(:te,o.tarih))
                                ORDER BY o.tarih DESC 
                                    LIMIT :s,:l";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam("ts", $ts, SQLITE3_TEXT);
        $stmt->bindParam("te", $te, SQLITE3_TEXT);
        $stmt->bindParam("u", $uye, SQLITE3_TEXT);
        $stmt->bindParam("ot", $odeme_tur_id, SQLITE3_INTEGER);
        $stmt->bindParam("s", $s, SQLITE3_INTEGER);
        $stmt->bindParam("l", $l, SQLITE3_INTEGER);

        $total = $summary["TOTAL"];
        $maxrow = $summary["MAXROW"];

        return $this->conn->resultToArray($stmt->execute());
    }

    public function uyeler($search, $aktif, $siralama, $s, $l, &$maxrow)
    {

        $sqlc = "SELECT 
                    COUNT(1) AS `MAXROW`        
                    FROM uye u
                        WHERE (u.uye LIKE '%'||COALESCE(:search,'')||'%' OR u.eposta LIKE '%'||COALESCE(:search,'')||'%') AND u.aktif = COALESCE(:aktif,u.aktif)
                ";

        $stmtc = $this->conn->prepare($sqlc);
        $stmtc->bindParam("search", $search, SQLITE3_TEXT);
        $stmtc->bindParam("aktif", $aktif, SQLITE3_INTEGER);
        $maxrow = $this->conn->resultToArray($stmtc->execute())[0]["MAXROW"];

        $order = "locale(u.uye,'tr_TR') ASC";
        if ($siralama == "GECIKME") {
            $order = "aidat_eksigi DESC, locale(u.uye,'tr_TR') ASC";
        } elseif ($siralama == "SEVIYE") {
            $order = "s.seviye_deger DESC";
        } elseif ($siralama == "SON_KEIKO") {
            $order = "son_geldigi_keiko ASC";
        }

        $sql = "SELECT 
	u.uye_id,u.uye,u.uye_tur,u.uyelik_tarihi,u.aktif,u.cinsiyet,u.eposta,u.dogum_tarihi,u.photo,
                    
                    (SELECT MIN(tarih) FROM yoklama WHERE uye_id = u.uye_id LIMIT 1) AS ilk_geldigi_keiko,
                    (SELECT MAX(tarih) FROM yoklama WHERE uye_id = u.uye_id LIMIT 1) AS son_geldigi_keiko,
                    (SELECT COUNT(1) FROM (
                        SELECT 
	CAST( STRFTIME('%Y',y.tarih) AS INTEGER) AS yil,
	CAST( STRFTIME('%m',y.tarih) AS INTEGER) AS ay
	FROM yoklama y 
	LEFT JOIN odeme o ON o.uye_id = y.uye_id AND o.yil = CAST( STRFTIME('%Y',y.tarih) AS INTEGER) AND o.ay = CAST( STRFTIME('%m',y.tarih) AS INTEGER) AND o.odeme_tur_id IN (1,2)
		WHERE y.uye_id = u.uye_id AND o.odeme_id IS NULL
			GROUP BY CAST( STRFTIME('%Y',y.tarih) AS INTEGER),CAST( STRFTIME('%m',y.tarih) AS INTEGER)
				HAVING CAST(STRFTIME('%m',date('now')) AS INTEGER) + ( CAST(STRFTIME('%Y',date('now')) AS INTEGER) * 12) > (CAST( STRFTIME('%Y',y.tarih) AS INTEGER)*12)+CAST( STRFTIME('%m',y.tarih) AS INTEGER)
                ) q LIMIT 1) AS aidat_eksigi  
                    ,( SELECT ROUND( COALESCE(SUM(o.tutar),0) ,2) FROM odeme o WHERE o.uye_id = u.uye_id AND CAST(STRFTIME('%Y',DATE('now')) AS INTEGER) = o.yil ) AS toplam_odeme
                    ,s.tanim AS seviye
                    ,s.tarih AS son_seviye_tarihi
		    ,s.seviye_deger
                    FROM uye u
					LEFT JOIN ( --seviye baglantisi
					SELECT 
	uye_id,
	CASE WHEN INSTR(tanim,'DAN') THEN (cast(substr(tanim,1,instr(tanim,' ')-1) AS INTEGER) * 1)+(1/(1 + strftime('%J',tarih) / 10000000)) ELSE (cast(substr(tanim,1,instr(tanim,' ')-1) AS INTEGER)* -1)+(1/(1 + strftime('%J',tarih) / 10000000)) END AS seviye_deger
	,MAX(tarih) AS tarih
	,tanim	
FROM seviye GROUP BY uye_id HAVING tarih = MAX(tarih) ) s ON s.uye_id = u.uye_id
                        WHERE (u.uye LIKE '%'||COALESCE(:search,'')||'%' OR u.eposta LIKE '%'||COALESCE(:search,'')||'%') AND u.aktif = COALESCE(:aktif,u.aktif)
                        ORDER BY $order
                        LIMIT :s,:l
                ";


        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("search", $search, SQLITE3_TEXT);
        $stmt->bindParam("aktif", $aktif, SQLITE3_INTEGER);
        $stmt->bindParam("s", $s, SQLITE3_INTEGER);
        $stmt->bindParam("l", $l, SQLITE3_INTEGER);

        return $this->conn->resultToArray($stmt->execute());
    }

    public function seviye($uye_id, $tarih, $tanim, $detaylar, $seviye_id = false)
    {
        $row = [
            "uye_id" => $uye_id,
            "tarih" => $tarih,
            "tanim" => $tanim,
            "detaylar" => $detaylar
        ];
        if ($seviye_id != false) $row["seviye_id"] = $seviye_id;
        return $this->conn->table("seviye", $row);
    }

    public function uyenin_sinavlari($uye_id)
    {
        $sql = "SELECT s.seviye_id,s.tarih,s.tanim,s.detaylar FROM seviye s WHERE s.uye_id = :uid ORDER BY s.tarih DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("uid", $uye_id, SQLITE3_INTEGER);
        return $this->conn->resultToArray($stmt->execute());
    }

    public function sinav_sil($seviye_id)
    {
        return $this->conn->table("seviye", $seviye_id);
    }

    public function uye_harcamalari($uye_id, $s, $l, &$maxrow, &$total)
    {
        $sql = "select g.gider_id, g.tarih, g.tutar, g.aciklama, g.belge, gt.gider_tur, g.gider_tur_id,g.uye_id  from gider g inner join gider_tur gt on gt.gider_tur_id = g.gider_tur_id where g.uye_id = :uid ORDER BY g.tarih DESC";
        $p = new \SQLite3Tool\SQLite3Paging($this->conn, $sql, $s, $l, ["TOTAL" => "SUM(tutar)"]);
        $p->bindParam("uid", $uye_id, SQLITE3_INTEGER);
        $result = $p->result($maxrow);
        $total = $p->values["TOTAL"];
        return $result;
    }

    public function harcama($tarih, $uye_id, $tutar, $gider_tur_id, $belge, $aciklama, $gider_id = false)
    {
        $row = [
            "uye_id" => $uye_id,
            "tarih" => $tarih,
            "tutar" => $tutar,
            "gider_tur_id" => $gider_tur_id,
            "belge" => $belge,
            "aciklama" => $aciklama,
        ];
        if ($gider_id != false) $row["gider_id"] = $gider_id;
        return $this->conn->table("gider", $row);
    }

    public function harcama_sil($gider_id)
    {
        return $this->conn->table("gider", $gider_id);
    }

    public function gider_turleri()
    {
        $stmt = $this->conn->prepare("SELECT * FROM gider_tur");
        return $this->conn->resultToArray($stmt->execute());
    }

    public function gelirler($baslangic, $bitis, $s, $l)
    {
        $sqlGelir = "select 
        g.tarih,
        u.uye,
        g.tutar,
        t.odeme_tur as tur,
        CASE 
            WHEN t.odeme_tur LIKE '%AIDAT%' THEN g.ay || '/' || g.yil || ' ' || coalesce(g.aciklama,'')
            ELSE g.aciklama
        END as aciklama,
        null as belge
        
    from odeme g inner join uye u on u.uye_id = g.uye_id inner join odeme_tur t on t.odeme_tur_id = g.odeme_tur_id
    where ( g.tarih between :bas AND :bit )
    order by g.tarih ASC";

        $pgelir = new \SQLite3Tool\SQLite3Paging($this->conn, $sqlGelir, $s, $l, ["TOTAL" => "SUM(tutar)"]);        
        $pgelir->bindParam("bas", $baslangic, SQLITE3_TEXT);
        $pgelir->bindParam("bit", $bitis, SQLITE3_TEXT);


        $list_gelir = $pgelir->result($maxrow_gelir);
        $total_gelir = $pgelir->values["TOTAL"];

        return [
            "list" => $list_gelir,            
            "maxrow" => $maxrow_gelir,            
            "total" => $total_gelir            
        ];
    }

    public function giderler($baslangic, $bitis, $s, $l)
    {
        $sqlGider = "select 
    g.tarih,
    u.uye,
    g.tutar,
    t.gider_tur as tur,
    g.aciklama,
    g.belge
from gider g inner join uye u on u.uye_id = g.uye_id inner join gider_tur t on t.gider_tur_id = g.gider_tur_id
where ( g.tarih between :bas AND :bit )
order by g.tarih ASC";

        $pgider = new \SQLite3Tool\SQLite3Paging($this->conn, $sqlGider, $s, $l, ["TOTAL" => "SUM(tutar)"]);
        $pgider->bindParam("bas", $baslangic, SQLITE3_TEXT);
        $pgider->bindParam("bit", $bitis, SQLITE3_TEXT);

        $list_gider = $pgider->result($maxrow_gider);
        $total_gider = $pgider->values["TOTAL"];

        return [
            "list" => $list_gider,            
            "maxrow" => $maxrow_gider,            
            "total" => $total_gider            
        ];
    }

    public function genel_uye_raporu() {
        $sql = "select q.uye, q.seviye, q.cinsiyet,q.dogum_tarihi,q.ekf_no,q.uc_ayi_icinde_devam_sayisi,q.uc_aylik_devam_yuzdesi,q.ilk_keiko,q.son_keiko,q.photo

        ,(SELECT COUNT(1) FROM (
                                SELECT 
            CAST( STRFTIME('%Y',y.tarih) AS INTEGER) AS yil,
            CAST( STRFTIME('%m',y.tarih) AS INTEGER) AS ay
            FROM yoklama y 
            LEFT JOIN odeme o ON o.uye_id = y.uye_id AND o.yil = CAST( STRFTIME('%Y',y.tarih) AS INTEGER) AND o.ay = CAST( STRFTIME('%m',y.tarih) AS INTEGER) AND o.odeme_tur_id IN (1,2)
                WHERE y.uye_id = q.uye_id AND o.odeme_id IS NULL
                    GROUP BY CAST( STRFTIME('%Y',y.tarih) AS INTEGER),CAST( STRFTIME('%m',y.tarih) AS INTEGER)
                        HAVING CAST(STRFTIME('%m',date('now')) AS INTEGER) + ( CAST(STRFTIME('%Y',date('now')) AS INTEGER) * 12) > (CAST( STRFTIME('%Y',y.tarih) AS INTEGER)*12)+CAST( STRFTIME('%m',y.tarih) AS INTEGER)
                        ) q LIMIT 1) AS aidat_eksigi,q.uye_id
        
         from (
        select 
        uy.photo,uy.uye_id,uy.uye,uy.seviye_deger,uy.seviye,uy.ekf_no,uy.cinsiyet,'%'||printf('%.2f',( cast(count(1) as float) / 0.24 )) as uc_aylik_devam_yuzdesi,uy.dogum_tarihi,count(1) as uc_ayi_icinde_devam_sayisi,
        ( select min(yok.tarih) from yoklama yok where yok.uye_id = uy.uye_id ) as ilk_keiko,
        ( select max(yok.tarih) from yoklama yok where yok.uye_id = uy.uye_id ) as son_keiko
        from (
        SELECT 
        u.uye_id,u.uye,u.cinsiyet,u.dogum_tarihi,coalesce(s.tanim,'7 KYU') as seviye,u.ekf_no,u.photo
        ,CASE WHEN INSTR(s.tanim,'DAN') THEN (cast(substr(s.tanim,1,instr(s.tanim,' ')-1) AS INTEGER) * 1)+(1/(1 + strftime('%J',s.tarih) / 10000000)) ELSE (cast(substr(s.tanim,1,instr(s.tanim,' ')-1) AS INTEGER)* -1)+(1/(1 + strftime('%J',s.tarih) / 10000000)) END as seviye_deger
        FROM uye u 
        left join seviye s on s.uye_id = u.uye_id
        left join seviye _s on _s.uye_id = s.uye_id and _s.tarih > s.tarih
        WHERE _s.seviye_id is null 
        and u.aktif = 1 ) uy 
        left join yoklama y  on uy.uye_id = y.uye_id and y.tarih >= date(date('now'),'-3 month')
        group by uy.uye_id,uy.uye,uy.seviye_deger,uy.cinsiyet
        ) q  order by q.seviye_deger desc
            ";

        $stmt = $this->conn->prepare($sql);
        return $this->conn->resultToArray($stmt->execute());
    }

    public function genel_durum($baslangic,$bitis) {
        $sqlGelirler = "select q.donem as donem,sum(q.tutar) as deger from (select strftime('%Y',o.tarih)||'-'||strftime('%m',o.tarih) as donem,o.tutar from odeme o ) q where (q.donem between :bas AND :bit ) group by q.donem";
        $sqlGiderler = "select q.donem as donem,sum(q.tutar) as deger from (select strftime('%Y',g.tarih)||'-'||strftime('%m',g.tarih) as donem,g.tutar from gider g ) q where (q.donem between :bas AND :bit ) group by q.donem";
        $sqlKatilim = "select q.donem as donem, printf('%.2f',cast( sum(q.sayi) as float )  / count(1)) as deger from( select strftime('%Y',y.tarih)||'-'||strftime('%m',y.tarih) as donem,count(1) as sayi from yoklama y group by y.tarih ) q where (q.donem between :bas AND :bit ) group by q.donem";
        
        $stmtGelirler = $this->conn->prepare($sqlGelirler);
        $stmtGiderler = $this->conn->prepare($sqlGiderler);
        $stmtKatilim = $this->conn->prepare($sqlKatilim);

        $stmtGelirler->bindParam("bas", $baslangic, SQLITE3_TEXT);
        $stmtGelirler->bindParam("bit", $bitis, SQLITE3_TEXT);
        $stmtGiderler->bindParam("bas", $baslangic, SQLITE3_TEXT);
        $stmtGiderler->bindParam("bit", $bitis, SQLITE3_TEXT);
        $stmtKatilim->bindParam("bas", $baslangic, SQLITE3_TEXT);
        $stmtKatilim->bindParam("bit", $bitis, SQLITE3_TEXT);
        return [
            "gelirler"=>$this->conn->resultToArray($stmtGelirler->execute()),
            "giderler"=>$this->conn->resultToArray($stmtGiderler->execute()),
            "katilim"=>$this->conn->resultToArray($stmtKatilim->execute())
        ];
    }

    public function gecikme_bildirimleri() {
        $sqlUyeListesi = "select * from (
            select u.uye_id,u.uye,u.eposta,
            (SELECT COUNT(1) FROM (
                                            SELECT 
                        CAST( STRFTIME('%Y',y.tarih) AS INTEGER) AS yil,
                        CAST( STRFTIME('%m',y.tarih) AS INTEGER) AS ay
                        FROM yoklama y 
                        LEFT JOIN odeme o ON o.uye_id = y.uye_id AND o.yil = CAST( STRFTIME('%Y',y.tarih) AS INTEGER) AND o.ay = CAST( STRFTIME('%m',y.tarih) AS INTEGER) AND o.odeme_tur_id IN (1,2)
                            WHERE y.uye_id = u.uye_id AND o.odeme_id IS NULL
                                GROUP BY CAST( STRFTIME('%Y',y.tarih) AS INTEGER),CAST( STRFTIME('%m',y.tarih) AS INTEGER)
                                    HAVING CAST(STRFTIME('%m',date('now')) AS INTEGER) + ( CAST(STRFTIME('%Y',date('now')) AS INTEGER) * 12) > (CAST( STRFTIME('%Y',y.tarih) AS INTEGER)*12)+CAST( STRFTIME('%m',y.tarih) AS INTEGER)
                                    ) q) AS aidat_eksigi
            from uye u where aktif = 1 ) q2 WHERE q2.aidat_eksigi > 0 order by q2.aidat_eksigi desc";
        $sqlUyeEKsikler = "SELECT
            CAST( STRFTIME('%Y',y.tarih) AS INTEGER) AS yil,
            CAST( STRFTIME('%m',y.tarih) AS INTEGER) AS ay,
            GROUP_CONCAT( STRFTIME('%d.%m.%Y',y.tarih),',' ) as gelinenler
                FROM yoklama y
                LEFT JOIN odeme o ON o.uye_id = y.uye_id AND o.yil = CAST( STRFTIME('%Y',y.tarih) AS INTEGER) AND o.ay = CAST( STRFTIME('%m',y.tarih) AS INTEGER) AND o.odeme_tur_id IN (1,2)
                    WHERE y.uye_id = :uid AND o.odeme_id IS NULL
                        GROUP BY CAST( STRFTIME('%Y',y.tarih) AS INTEGER),CAST( STRFTIME('%m',y.tarih) AS INTEGER)
                            HAVING CAST(STRFTIME('%m',date('now')) AS INTEGER) + ( CAST(STRFTIME('%Y',date('now')) AS INTEGER) * 12) > (CAST( STRFTIME('%Y',y.tarih) AS INTEGER)*12)+CAST( STRFTIME('%m',y.tarih) AS INTEGER)";
        $sqlUyeAidatlari = "select o.yil,o.ay,STRFTIME('%d.%m.%Y',o.tarih) as tarih,ot.odeme_tur,o.tutar from odeme o inner join odeme_tur ot on ot.odeme_tur_id = o.odeme_tur_id where o.uye_id = :uid and o.odeme_tur_id in (1,2)";
        $sqlBilgi = "select 
        u.uye_tur,
        CASE
            WHEN u.uye_tur = 'TAM' THEN ( select tutar from odeme_tur where odeme_tur_id = 1 )
            ELSE ( select tutar from odeme_tur where odeme_tur_id = 2 )
        END as aidat
    from uye u where u.uye_id = :uid";
        $stmtUyeListesi = $this->conn->prepare($sqlUyeListesi);
        $list = $this->conn->resultToArray($stmtUyeListesi->execute());
        for ($i=0; $i<count($list); $i++ ) {            
            $uid = $list[$i]["uye_id"];
            $stmtUyeEKsikler = $this->conn->prepare($sqlUyeEKsikler);
            $stmtUyeEKsikler->bindParam("uid", $uid, SQLITE3_INTEGER);
            $list[$i]["eksikler"] = $this->conn->resultToArray($stmtUyeEKsikler->execute());
            $stmtUyeAidatlari = $this->conn->prepare($sqlUyeAidatlari);
            $stmtUyeAidatlari->bindParam("uid", $uid, SQLITE3_INTEGER);
            $list[$i]["aidatlar"] = $this->conn->resultToArray($stmtUyeAidatlari->execute());
            $stmtBilgi = $this->conn->prepare($sqlBilgi);
            $stmtBilgi->bindParam("uid", $uid, SQLITE3_INTEGER);
            $list[$i]["bilgi"] = $this->conn->resultToArray($stmtBilgi->execute());
        }
        return $list;
    }

}
