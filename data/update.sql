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
	select s_.seviye_id,s_.uye_id,s_.tarih,s_.tanim,s_.detaylar from seviye_ s_;

DROP TABLE seviye_;

CREATE TABLE `gider_tur` (
	`gider_tur_id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	`gider_tur`	TEXT NOT NULL UNIQUE
);

DROP TABLE gider;

CREATE TABLE `gider` (
	`gider_id`	INTEGER PRIMARY KEY AUTOINCREMENT,
	`tarih`	TEXT,
	`tutar`	NUMERIC ( 12 , 2 ),
	`aciklama`	TEXT,
	`gider_tur_id`	INTEGER NOT NULL,
	`uye_id` INTEGER,
	`belge`	TEXT
);

INSERT INTO gider_tur (gider_tur_id,gider_tur) VALUES 
	(1,'Salona Kirası'),
	(2,'Takım Harcamalar'),
	(3,'Organizasyon Harcamaları'),
	(4,'Ekipman Temini'),
	(5,'Ödeme İade'),
	(6,'Diğer Harcamalar');

CREATE UNIQUE INDEX uni_yoklama ON yoklama(uye_id,tarih);