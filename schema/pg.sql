drop table admindb;
drop table userdb;
drop table site;
drop table page;
drop table data;
drop table link;
drop table taggedlink;

create table admindb(
	id varchar(100) primary key,
	pw varchar(32),
	sid varchar(32),
	btime varchar(25),
	cip varchar(20),
	ctime varchar(25),
	mip varchar(20),
	mtime varchar(25)
);

create table userdb(
	id varchar(100) primary key,
	pw varchar(32),
	sid varchar(32),
	btime varchar(25),
	cip varchar(20),
	ctime varchar(25),
	mip varchar(20),
	mtime varchar(25)
);

create table site(
	locked char,
	hidden char
);
insert into site values(0, 0);

create table page(
	id int default 0 primary key,
	name varchar(100),
	cauthor varchar(100),
	cip varchar(20),
	ctime varchar(25),
	hits int,
	locked char,
	hidden char,
	tag int,
	version int
);

create table data(
	id int not null,
	version int not null,
	author varchar(100),
	ip varchar(20),
	mtime varchar(25),
	content text,
	primary key(id, version)
);

create table link(
	linkfrom int,
	linkto int,
	linktoname varchar(100)
);

create table taggedlink(
	linkfrom int,
	linkto int,
	linktoname varchar(100)
);

grant all on admindb to nobody;
grant all on userdb to nobody;
grant all on site to nobody;
grant all on page to nobody;
grant all on data to nobody;
grant all on link to nobody;
grant all on taggedlink to nobody;
