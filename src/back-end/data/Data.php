<?php

require_once __DIR__ . "/../lib/SQLite3Ex.php";

class Data {
    private $conn;
    public static function link():SQLite3Ex {
        $conn = new SQLite3Ex( __DIR__ . "/dojo.db" );
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

    public function  uye($uye,$uye_tur,$dogum_tarihi,$uyelik_tarihi,$aktif,$cinsiyet,$eposta,$uye_id = false) {
        $row = [
            "uye" => $uye,
            "uye_tur" => $uye_tur,
            "dogum_tarihi" => $dogum_tarihi,
            "uyelik_tarihi" => $uyelik_tarihi,
            "aktif" => $aktif,
            "cinsiyet" => $cinsiyet,
            "eposta" => $eposta
        ];
        if ($uye_id!=false) $row["uye_id"] = $uye_id;
        return $this->conn->table("uye", $row);
    }

    public function yoklama($tarih,$uye_ids,&$incount) {

        $this->conn("BEGIN");        
        try {
            
        
            $stmt = $this->conn->prepare("DELETE FROM yoklama WHERE tarih =:tarih");
            $stmt->bindParam("tarih",$tarih, SQLITE3_TEXT);
            $stmt->execute();
        
        
            //var_dump($uye_ids);
            for($i=0; $i<count($uye_ids); $i++) {
                $stmt = $this->conn->prepare("INSERT INTO yoklama (uye_id,tarih) VALUES (:u,:t)");
                $stmt->bindParam("t",$tarih, SQLITE3_TEXT);
                $stmt->bindParam("u",$uye_ids[$i], SQLITE3_INTEGER);
                $stmt->execute();
            }

            $incount = $i;
        } catch ( Exception $ex ) {
            $this->exec("ROLLBACK");
            throw $ex;
        }

        $this->exec("COMMIT");
    }

    public function yoklamalar($tarih_s,$tarih_e,$s,$l,&$maxrow) {
        
        $ts = ($tarih_s == "" ? null : $tarih_s);
        $te = ($tarih_e == "" ? null : $tarih_e);

        $p = new SQLite3Paging( $this->conn,"SELECT y.tarih,COUNT(1) AS sayi FROM yoklama y WHERE y.tarih BETWEEN COALESCE(:ts,y.tarih) AND COALESCE(:te,y.tarih) GROUP BY y.tarih ORDER BY y.tarih DESC",$s,$l);
        $p->bindParam("ts",$ts, SQLITE3_TEXT);
        $p->bindParam("te",$te, SQLITE3_TEXT);
        return $p->result($maxrow);
    }

    public function uyenin_yoklamalari($uye_id,$s,$l,&$maxrow) {
        $p = new SQLite3Paging( $this->conn,"SELECT y.tarih,y.yoklama_id FROM yoklama y WHERE y.uye_id = :uid ORDER BY y.tarih DESC",$s,$l);
        $p->bindParam("uid",$uye_id, SQLITE3_INTEGER);
        return $p->result($maxrow);
    }

    public function uye_yoklama_ekle($uye_id,$tarih) {        
        return $this->conn->table("yoklama", [ "uye_id"=>$uye_id,"tarih"=>$tarih ]);
    }

    public function uye_yoklama_sil($yoklama_id) {        
        return $this->conn->table("yoklama", $yoklama_id);
    }

    public function yuklamadaki_uyeler($tarih) {
        $sql = "SELECT y.uye_id,u.uye FROM yoklama y INNER JOIN uye u ON u.uye_id = y.uye_id WHERE y.tarih = :t ORDER BY u.uye ASC LIMIT 100";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("t",$tarih, SQLITE3_TEXT);
        return $this->conn->resultToArray($stmt->execute());
    }

    public function aktif_uyeler() {
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

    public function aidat_eksigi($uye_id) {        
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
        $stmt->bindParam("uid",$uye_id, SQLITE3_INTEGER);
        return $this->conn->resultToArray($stmt->execute());
    }

    public function odeme_tur($odeme_tur,$tutar,$odeme_tur_id = false) {
        $row = [
            "odeme_tur" => $odeme_tur,
            "tutar" => $tutar            
        ];
        if ($odeme_tur_id !=false) $row["odeme_tur_id"] = $odeme_tur_id;
        return $this->conn->table("odeme_tur", $row);
    }

    public function odeme_turleri() {
        $sql = "SELECT ot.* FROM odeme_tur ot ORDER BY ot.odeme_tur ASC";
        $stmt = $this->conn->prepare($sql);
        return $this->conn->resultToArray($stmt->execute());
    }

    public function kullanicilar() {
        $sql = "SELECT kullanici_id,kullanici,NULL AS pass,yetki FROM kullanici ORDER BY LOCALE(kullanici,'tr_TR') ASC LIMIT 100";
        $stmt = $this->conn->prepare($sql);
        return $this->conn->resultToArray($stmt->execute());
    }

    public function kullanici($kullanici,$yetki,$pass = null,$kullanici_id = false) {
        $row = [
            "kullanici" => $kullanici,
            "yetki" => $yetki
        ];
        if (!is_null($pass)) $row["parola"] = hash('ripemd160', $pass);        
        if ($kullanici_id!=false) $row["kullanici_id"] = $kullanici_id;
        return $this->conn->table("kullanici", $row);
    }

    public function password($kullanici,$eski,$yeni) {
        $stmt = $this->conn->prepare("UPDATE kullanici SET parola = :y WHERE kullanici = :k AND parola = :e ");
        $e = hash('ripemd160', $eski);
        $y = hash('ripemd160', $yeni);
        $k = $kullanici;
        $stmt->bindParam("y",$y, SQLITE3_TEXT);
        $stmt->bindParam("e",$e, SQLITE3_TEXT);
        $stmt->bindParam("k",$k, SQLITE3_TEXT);
        $stmt->execute();
    }

    public function yetkilendir($kullanici,$pass) {
        $stmt = $this->conn->prepare("SELECT yetki FROM kullanici WHERE LOWER(kullanici) = LOWER( :kullanici ) AND parola = :parola");
        $stmt->bindValue("kullanici", $kullanici, SQLITE3_TEXT);
        $stmt->bindValue("parola", hash('ripemd160', $pass), SQLITE3_TEXT);
        $arr = $this->conn->resultToArray( $stmt->execute() );
        //print_r($arr); die();
        if (is_array($arr) && count($arr)==1 ) {
            return $arr[0]["yetki"];
        } else {
            return false;
        }
    }

    public function kullanici_sil($kullanici_id) {        
        return $this->conn->table("kullanici", $kullanici_id);
    }

    public function odeme($uye_id,$tarih,$yil,$ay,$tutar,$odeme_tur_id,$aciklama,$odeme_id = false) {
        $row = [
            "uye_id" => $uye_id,
            "tarih" => $tarih,
            "yil" => $yil,
            "ay" => $ay,
            "tutar" => $tutar,
            "odeme_tur_id" => $odeme_tur_id,
            "aciklama" => ($aciklama == "" ? null : $aciklama )
        ];
        if ($odeme_id!=false) $row["odeme_id"] = $odeme_id;
        //var_dump($row);
        return $this->conn->table("odeme", $row);
    }

    public function odeme_sil($odeme_id) {
        return $this->conn->table("odeme", $odeme_id);
    }

    public function uye_odemeleri($uye_id,$odeme_tur_id,$s,$l,&$total,&$maxrow) {
        $sqlc = "SELECT COUNT(1) AS `MAXROW`,COALESCE(SUM(o.tutar),0) AS `TOTAL` 
                    FROM odeme o
                        WHERE 
                            o.uye_id = :uid
                            AND o.odeme_tur_id = COALESCE(:ot,o.odeme_tur_id)";
        
        $stmtc = $this->conn->prepare($sqlc);
        $stmtc->bindParam("uid",$uye_id, SQLITE3_INTEGER);
        $stmtc->bindParam("ot",$odeme_tur_id, SQLITE3_INTEGER);
        $summary =$this->conn->resultToArray($stmtc->execute())[0];
        
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
        $stmt->bindParam("uid",$uye_id, SQLITE3_INTEGER);
        $stmt->bindParam("ot",$odeme_tur_id, SQLITE3_INTEGER);
        $stmt->bindParam("s",$s, SQLITE3_INTEGER);
        $stmt->bindParam("l",$l, SQLITE3_INTEGER);

        $total = $summary["TOTAL"];
        $maxrow = $summary["MAXROW"];
        
        return $this->conn->resultToArray($stmt->execute());
    }

    public function odemeler($uye,$odeme_tur_id,$tarih_s,$tarih_e,$s,$l,&$total,&$maxrow) {
        
        $ts = ($tarih_s == "" ? null : $tarih_s);
        $te = ($tarih_e == "" ? null : $tarih_e);
        
        $sqlc = "SELECT COUNT(1) AS `MAXROW`,COALESCE(ROUND(SUM(o.tutar),2),0) AS `TOTAL` 
            FROM odeme o INNER JOIN uye u ON u.uye_id = o.uye_id 
                WHERE 
                    u.uye LIKE '%'||COALESCE(:u,'')||'%' 
                    AND o.odeme_tur_id = COALESCE(:ot,o.odeme_tur_id) 
                    AND (o.tarih BETWEEN COALESCE(:ts,o.tarih) AND COALESCE(:te,o.tarih))";
        
        $stmtc = $this->conn->prepare($sqlc);
        $stmtc->bindParam("ts",$ts, SQLITE3_TEXT);
        $stmtc->bindParam("te",$te, SQLITE3_TEXT);
        $stmtc->bindParam("u",$uye, SQLITE3_TEXT);
        $stmtc->bindParam("ot",$odeme_tur_id, SQLITE3_INTEGER);      
        $summary =$this->conn->resultToArray($stmtc->execute())[0];
        
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
        
        $stmt->bindParam("ts",$ts, SQLITE3_TEXT);
        $stmt->bindParam("te",$te, SQLITE3_TEXT);
        $stmt->bindParam("u",$uye, SQLITE3_TEXT);
        $stmt->bindParam("ot",$odeme_tur_id, SQLITE3_INTEGER);
        $stmt->bindParam("s",$s, SQLITE3_INTEGER);
        $stmt->bindParam("l",$l, SQLITE3_INTEGER);

        $total = $summary["TOTAL"];
        $maxrow = $summary["MAXROW"];
        
        return $this->conn->resultToArray($stmt->execute());
    }

    public function uyeler($search,$aktif,$siralama,$s,$l,&$maxrow) {
        
        $sqlc = "SELECT 
                    COUNT(1) AS `MAXROW`        
                    FROM uye u
                        WHERE (u.uye LIKE '%'||COALESCE(:search,'')||'%' OR u.eposta LIKE '%'||COALESCE(:search,'')||'%') AND u.aktif = COALESCE(:aktif,u.aktif)
                ";
        
        $stmtc = $this->conn->prepare($sqlc);
        $stmtc->bindParam("search",$search, SQLITE3_TEXT);
        $stmtc->bindParam("aktif",$aktif, SQLITE3_INTEGER);        
        $maxrow = $this->conn->resultToArray($stmtc->execute())[0]["MAXROW"];
        
        $order = "locale(u.uye,'tr_TR') ASC";
        if ($siralama == "GECIKME") {
            $order = "aidat_eksigi DESC, locale(u.uye,'tr_TR') ASC";
        } elseif ( $siralama == "SEVIYE"  ) {
            $order = "s.seviye_deger DESC";
        } elseif ( $siralama == "SON_KEIKO") {
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
        $stmt->bindParam("search",$search, SQLITE3_TEXT);
        $stmt->bindParam("aktif",$aktif, SQLITE3_INTEGER);
        $stmt->bindParam("s",$s, SQLITE3_INTEGER);
        $stmt->bindParam("l",$l, SQLITE3_INTEGER);
        
        return $this->conn->resultToArray($stmt->execute());
    }

    public function seviye($uye_id,$tarih,$tanim,$detaylar,$ekc_no,$seviye_id = false) {
        $row = [
            "uye_id" => $uye_id,
            "tarih" => $tarih,
            "tanim" => $tanim,
            "detaylar" =>$detaylar,
            "ekc_no" => $ekc_no
        ];
        if ($seviye_id!=false) $row["seviye_id"] = $seviye_id;
        return $this->conn->table("seviye", $row);
    }

    public function uyenin_sinavlari($uye_id) {
        $sql = "SELECT s.seviye_id,s.tarih,s.tanim,s.detaylar,s.ekc_no FROM seviye s WHERE s.uye_id = :uid ORDER BY s.tarih DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("uid", $uye_id, SQLITE3_INTEGER);
        return $this->conn->resultToArray($stmt->execute());
    }

    public function sinav_sil($seviye_id) {        
        return $this->conn->table("seviye", $seviye_id);
    }

}