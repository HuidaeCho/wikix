#!/bin/sh
if [ $# -eq 0 ]
then
	echo ./mksub.sh subname
	exit
fi

for i in $@
do
	echo "##### $i"
	if [ -e $i ]
	then
		echo "Already exists!"
		continue
	fi
	mkdir $i
	cd $i
	ln -sf ../*.php ../*.map ../my* ../action ../lib ../bin ../js ../themes ../wikiXpages .
	rm -f mywikix myfile
	mkdir mywikix myfile file file0 tmpfile
	chmod 1777 file file0 tmpfile
	cd mywikix
	cp ../../_mywikix/*.* .
	touch wikix.header wikix.footer
	rm -f config.php package.php
	ln -sf ../../mywikix/config.php ../../mywikix/package.php .
	cd ../..
	echo "Done."
done
