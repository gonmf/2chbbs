create database maindb character set sjis collate sjis_japanese_ci;
use maindb;

create table boards(
	id int unsigned primary key auto_increment,
	name varchar(32) not null,
	notice varchar(1500)
);
create table threads(
	id int unsigned primary key auto_increment,
	boardid int unsigned,
	subject varchar(48) not null
);
create table posts(
	id int unsigned primary key auto_increment,
	threadid int unsigned,
	boardid int unsigned,
	author varchar(40) not null,
	msg varchar(1500) not null,
	dat datetime
);

insert into boards(name, notice) values("A", "<center><img src='http://i.imgur.com/BTZ427I.png'></center>");
insert into boards(name, notice) values("B", "<center><img src='http://i.imgur.com/BTZ427I.png'></center>");
insert into boards(name, notice) values("C", "<center><img src='http://i.imgur.com/BTZ427I.png'></center>");
insert into boards(name, notice) values("D", "<center><img src='http://i.imgur.com/BTZ427I.png'></center>");