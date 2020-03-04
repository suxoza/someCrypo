create user if not exists 'cryptoUser'@'localhost' identified by 'cryptoUserPassword';
create database if not exists cryptoDB character set utf8 collate utf8_unicode_ci;
grant all on cryptoDB.* to 'cryptoUser'@'localhost';
flush privileges;

use cryptoDB;

drop table if exists currencyTemplate;
create table currencyTemplate(
	id tinyint(3) not null primary key auto_increment comment 'identifyer only',
	target tinyint(1) not null default 0 comment 'when 0 then crypo; when 1 then fiat',
	title varchar(10) not null default '',
	fullName varchar(30) not null default ''
) comment 'template only - anyway will be cached';
insert into currencyTemplate (target, title, fullName) values 
	(0, 'btc', 'Bitcoin'),
	(0, 'ltc', 'Litecoin'),
	(0, 'dash', 'Dash'),
	(0, 'zec', 'Zcash'),
	(0, 'bch', 'Bitcoin-cash'),

	(1, 'usd', 'US dolar'),
	(1, 'eur', 'Euro'),
	(1, 'gel', 'Gerogian lari'),
	(1, 'try', 'Turkish lira'),
	(1, 'rub', 'Russian Ruble');


drop table if exists exchangeRates;
create table exchangeRates(
	id bigint(20) not null primary key auto_increment,
	cripoID tinyint(3) not null default 0 comment 'id in currencyTemplate where target = 0',
	fiatID tinyint(3) not null default 0 comment 'id in currencyTemplate where target = 1',
	price decimal(10, 4) not null default 0.0000 comment 'crypo price in this time',
	insert_date timestamp default current_timestamp,
	key c_f(cripoID, fiatID),
	key tm(insert_date)
) comment 'transactions';

