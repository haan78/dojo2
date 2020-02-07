select q.uye,q.eposta, q.seviye, q.cinsiyet,q.dogum_tarihi,q.ekf_no,q.uc_ayi_icinde_devam_sayisi,q.uc_aylik_devam_yuzdesi,q.ilk_keiko,q.son_keiko,q.photo

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
uy.photo,uy.eposta,uy.uye_id,uy.uye,uy.seviye_deger,uy.seviye,uy.ekf_no,uy.cinsiyet,'%'||printf('%.2f',( cast(count(1) as float) / 0.24 )) as uc_aylik_devam_yuzdesi,uy.dogum_tarihi,count(1) as uc_ayi_icinde_devam_sayisi,
( select min(yok.tarih) from yoklama yok where yok.uye_id = uy.uye_id ) as ilk_keiko,
( select max(yok.tarih) from yoklama yok where yok.uye_id = uy.uye_id ) as son_keiko
from (
SELECT 
u.uye_id,u.uye,u.eposta,u.cinsiyet,u.dogum_tarihi,coalesce(s.tanim,'7 KYU') as seviye,u.ekf_no,u.photo
,CASE WHEN INSTR(s.tanim,'DAN') THEN (cast(substr(s.tanim,1,instr(s.tanim,' ')-1) AS INTEGER) * 1)+(1/(1 + strftime('%J',s.tarih) / 10000000)) ELSE (cast(substr(s.tanim,1,instr(s.tanim,' ')-1) AS INTEGER)* -1)+(1/(1 + strftime('%J',s.tarih) / 10000000)) END as seviye_deger
FROM "uye" u 
left join "seviye" s on s.uye_id = u.uye_id
left join "seviye" _s on _s.uye_id = s.uye_id and _s.tarih > s.tarih
WHERE _s.seviye_id is null 
and u.aktif = 1 ) uy 
left join yoklama y  on uy.uye_id = y.uye_id and y.tarih >= date(date('now'),'-3 month')
group by uy.uye_id,uy.uye,uy.seviye_deger,uy.cinsiyet
) q  order by q.seviye_deger desc