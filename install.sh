#!/bin/sh
if [ ! -d mywikix ]
then
	mkdir mywikix
	cp _mywikix/*.* mywikix
fi

if [ ! -d mytheme ]
then
	mkdir mytheme
	mkdir mytheme/images
	cp themes/wikix/README themes/wikix/*.* mytheme
	cp themes/wikix/images/*.* mytheme/images
fi

for i in bin mylib mythemelib mystage myplugin mysubs myphp mymisc myfile file file0 tmpfile
do
	if [ ! -d $i ]
	then
		mkdir $i
	fi
done
chmod 1777 file file0 tmpfile
