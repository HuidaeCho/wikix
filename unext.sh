#!/bin/sh
for i in mywikix mylib mythemelib mystage myplugin mysubs myphp mymisc themes
do
	cd $i
	for j in *
	do
		if [ ! -f $j.zip ]
		then
			continue
		fi
		echo Unpackaging $i/$j...
		unzip -o $j.zip
	done
	cd ..
done
