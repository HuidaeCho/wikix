drop table if exists admindb;
drop table if exists userdb;
drop table if exists site;
drop table if exists page;
drop table if exists data;
drop table if exists link;
drop table if exists taggedlink;

create table admindb(
	id varchar(100) binary primary key,
	pw varchar(32),
	sid varchar(32),
	btime varchar(25),
	cip varchar(20),
	ctime varchar(25),
	mip varchar(20),
	mtime varchar(25)
);

create table userdb(
	id varchar(100) binary primary key,
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
	name tinyblob,
	cauthor varchar(100) binary,
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
	author varchar(100) binary,
	ip varchar(20),
	mtime varchar(25),
	content longblob,
	primary key(id, version)
);

create table link(
	linkfrom int,
	linkto int,
	linktoname tinyblob
);

create table taggedlink(
	linkfrom int,
	linkto int,
	linktoname tinyblob
);

grant all on admindb to nobody;
grant all on userdb to nobody;
grant all on site to nobody;
grant all on page to nobody;
grant all on data to nobody;
grant all on link to nobody;
grant all on taggedlink to nobody;
