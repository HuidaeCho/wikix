#!/bin/sh
if [ $# -eq 0 ]
then
	echo "Usage: ./install.sh db_"
	echo ""
	echo "	db_	DB table prefix ('' for no prefix)"
	exit
fi
db_=`echo "$1" | sed 's/[0-9a-zA-Z_]//g'`
if [ "x$db_" != "x" ]
then
	echo "db_ should contain only 0-9, a-z, A-Z, and _ characters."
	exit
fi

echo "Generating schema files..."
for i in mysql pg idx_mysql idx_pg
do
	echo schema/$i.sql
	sed "s/\${db_}/$1/g" < schema/$i.db_ > schema/$i.sql
done

echo "Creating the basic structure..."
if [ ! -d mywikix ]
then
	echo mywikix
	mkdir mywikix
	cp _mywikix/*.* mywikix
fi

if [ ! -d mytheme ]
then
	echo mytheme
	mkdir mytheme
	mkdir mytheme/images
	cp themes/wikix/README themes/wikix/*.* mytheme
	cp themes/wikix/images/*.* mytheme/images
fi

for i in bin mylib myplugin mysubs mystage myphp mymisc mythemelib myfile file file0 tmpfile
do
	if [ ! -d $i ]
	then
		echo $i
		mkdir $i
	fi
done
chmod 1777 file file0 tmpfile
