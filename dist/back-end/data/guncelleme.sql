ALTER TABLE uye ADD ekf_no TEXT;
update uye set ekf_no = (select s.ekc_no from seviye s where s.uye_id = uye.uye_id and s.ekc_no is not null limit 1 );

ALTER TABLE seviye RENAME TO seviye_;

CREATE TABLE `seviye` (
	`seviye_id`	INTEGER PRIMARY KEY AUTOINCREMENT,
	`uye_id`	INTEGER NOT NULL,
	`tarih`	TEXT NOT NULL,
	`tanim`	TEXT NOT NULL,
	`detaylar`	TEXT
);

insert into seviye ( seviye_id,uye_id,tarih,tanim,detaylar )
	select s_.seviye_id,s_.uye_id,s_.tarih,s_.tarih,s_.detaylar from seviye_ s_;

DROP TABLE seviye_;