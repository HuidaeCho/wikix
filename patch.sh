#!/bin/sh
usage="./patch.sh {php403|unlimitedpool|urlencode|dontsplitword|utf8|noinvalidaccess}"

if [ $# -eq 0 ]
then
	echo $usage
	exit
fi

for i in $@
do
case $i in
################################################################################

php403)
	files="lib/DisplayContent.php lib/misc.php";;

unlimitedpool)
	files="lib/DisplayContent.php lib/dbm.php";;

urlencode|dontsplitword|utf8|noinvalidaccess)
	files="lib/misc.php";;

################################################################################
*)
	echo $usage
	exit;;
esac

echo "##### $i"
for j in $files
do
	grep "^#$i:" $j > /dev/null
	if [ $? -eq 0 ]
	then
		echo "Patching $j"
		sed "s/^\(#$i:\)\(.*\)$/\2\1/" $j > tmpfile/$$
	else
		echo "Unpatching $j"
		sed "s/^\(.*\)\(#$i:\)$/\2\1/" $j > tmpfile/$$
	fi
	mv tmpfile/$$ $j
done
done
