#!/bin/sh
for i in mylib mythemelib mystage myplugin mysubs myphp mymisc themes
do
	cd $i
	for j in *
	do
		if [ ! -f $j.zip ]
		then
			continue
		fi
		echo $i/$j...
		zip -r $j.zip $j
	done
	cd ..
done
