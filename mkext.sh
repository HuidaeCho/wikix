#!/bin/sh
rm ext.ls
for i in mywikix mylib mythemelib mystage myplugin mysubs myphp mymisc themes
do
	cd $i
	for j in *
	do
		if [ ! -f $j.zip ]
		then
			continue
		fi
		echo Packaging $i/$j...
		rm $j.zip
		zip -r $j.zip $j
		echo $i/$j >> ../ext.ls
	done
	cd ..
done
