select q.uye,q.seviye,q.dogum_tarihi,q.cinsiyet,q.tarih,q.ekc_no from (
SELECT u.uye_id,u.uye,u.cinsiyet,u.dogum_tarihi,coalesce(s.tanim,'7 KYU') as seviye,s.ekc_no,s.tarih
,CASE WHEN INSTR(s.tanim,'DAN') THEN (cast(substr(s.tanim,1,instr(s.tanim,' ')-1) AS INTEGER) * 1)+(1/(1 + strftime('%J',s.tarih) / 10000000)) ELSE (cast(substr(s.tanim,1,instr(s.tanim,' ')-1) AS INTEGER)* -1)+(1/(1 + strftime('%J',s.tarih) / 10000000)) END as seviye_deger
FROM "uye" u left join "seviye" s on s.uye_id = u.uye_id
left join "seviye" _s on _s.uye_id = s.uye_id and _s.tarih > s.tarih
WHERE _s.seviye_id is null and u.aktif = 1
) as q  order by q.seviye_deger desc;
