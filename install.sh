#!/bin/sh
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
