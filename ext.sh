#!/bin/sh
if [ $# -eq 0 ]
then
	echo "./ext.sh [http://uri] [-l] [-f] [-d] [-u] ext ..."
	exit
fi
uri=http://wikix.org
download=0
uninstall=0
for i in $@
do
	case $i in
	http://*)
		uri=$i
		;;
	-l|-f)
		j=`echo $uri | sed 's/^http:\\/\\//ext./; s/\\//_/g; s/$/.ls/'`
		lynx -useragent="wikiX/ext.sh - Lynx" -source $uri/ext.ls > $j
		head -1 $j | grep '^<' > /dev/null
		if [ $? -eq 0 ]
		then
			rm -f $j
			echo $uri/ext.ls does not exist
			exit
		fi
		echo $j created
		if [ $i = "-f" ]
		then
			./ext.sh -d `cat $j`
		fi
		exit
		;;
	-d)
		download=1
		;;
	-u)
		uninstall=1
		;;
	esac
done
modified=
skipped=
package=0
cp mywikix/package.php mywikix/package.php.bak
cp mywikix/package.php mywikix/ext.tmp
for i in $@
do
	case $i in
	http://*|-d|-u)
		continue
		;;
	esac
	i=`echo $i | sed 's/\\/\\/*/\\//g; s/\\/\\/*$//'`
	case $i in
	*.zip)
		;;
	*)
		i=$i.zip
		;;
	esac
	if [ $download -eq 1 -o ! -f $i ]
	then
		echo Downloading $uri/$i...
		lynx -useragent="wikiX/ext.sh - Lynx" -source $uri/$i > $i
		j=`echo $i | sed 's/\\//\\\\\\//g'`
		file $i | sed "s/$j//g" | grep -i zip > /dev/null
		if [ $? -ne 0 ]
		then
			rm -f $i
			continue
		fi
	fi
	e=`dirname $i`
	d=`echo $i | sed 's/\\.zip$//'`
	if [ $uninstall -eq 1 ]
	then
		echo Uninstalling $d...
		grep -v "^$d$" ext.ils > ext.tmp
		mv ext.tmp ext.ils
	else
		echo $d >> ext.ils
		echo Installing $d...
		unzip -o $i -d $e
	fi
	if [ -d $d ]
	then #######
	for j in `find $d | grep '\\.php$'`
	do
		echo $j
		t=`basename $j | sed 's/\\.php$//'`
		case $t in
		# mylib
		l)
			if [ $uninstall -eq 1 ]
			then ##################
			grep -v "include_once(\"$j\");$" mywikix/ext.tmp > \
				mywikix/_ext.tmp
			else ##################
			grep -v "include_once(\"$j\");$" mywikix/ext.tmp | \
			awk '{if($0 == "#mylib")
					print "include_once(\"'$j'\");";
				print $0;}' > mywikix/_ext.tmp
			fi ####################
			;;
		# myplugin, mysubs
		p|s)
			func=`grep '^function ' $j | head -1 | \
				sed 's/^function \\(.*)\\).*$/\\1/'`
			if [ $uninstall -eq 1 ]
			then ##################
			grep -v "include_once(\"$j\");$" mywikix/ext.tmp | \
			grep -v "\$str = $func;$" > mywikix/_ext.tmp
			else ##################
			type=myplugin
			if [ $t = "s" ]
			then
				type=mysubs
			fi
			grep -v "include_once(\"$j\");$" mywikix/ext.tmp | \
			grep -v "\$str = $func;$" | \
			awk '{if($0 == "#'$type'"){
					print "include_once(\"'$j'\");";
					ext = 1;
				}else
				if(ext && $0 == "	return $str;"){
					print "	$str = '$func';";
					ext = 0;
				}
				print $0;}' > mywikix/_ext.tmp
			fi ####################
			;;
		# mystage
		0|0s|1|1s|h|f)
			if [ $uninstall -eq 1 ]
			then ##################
			grep -v "include(\"$j\");$" mywikix/ext.tmp > \
				mywikix/_ext.tmp
			else ##################
			type=mystage$t
			grep -v "include(\"$j\");$" mywikix/ext.tmp | \
			awk '{if($0 == "#'$type'")
					print "	include(\"'$j'\");";
				print $0;}' > mywikix/_ext.tmp
			fi ####################
			;;
		# myrun
		r|r[0-9]*)
			grep '^<?#.*#' $j > /dev/null
			if [ $? -eq 0 ]
			then
				f=`head -1 $j | sed 's/^<?#\\(.*\\)#.*$/\\1/'`
			else
				f=mywikix/ext.tmp
			fi
			if [ $uninstall -eq 1 ]
			then ##################
			grep -v "include_once(\"$j\")" $f > mywikix/_ext.tmp
			else ##################
			echo "<?include_once(\"$j\")?>" > mywikix/_ext.tmp
			grep -v "include_once(\"$j\")" $f >> mywikix/_ext.tmp
			fi ####################
			mv mywikix/_ext.tmp $f
			if [ $f != "mywikix/ext.tmp" ]
			then
				echo "	$f modified"
				echo "$modified" | grep " $f " > /dev/null
				if [ $? -ne 0 ]
				then
					modified="$modified $f "
				fi
			fi
			continue
			;;
		*)
			echo "	skipped"
			skipped="$skipped $j"
			continue
			;;
		esac
		mv mywikix/_ext.tmp mywikix/ext.tmp
	done
	elif [ $uninstall -eq 0 -a -f $d ]
	then #######
	case $d in
	mywikix/package.php)
		package=1
		;;
	*)
		cp $d $d.bak
		;;
	esac
	echo $d
	echo "	skipped"
	skipped="$skipped $d"
	continue
	fi #########
done
mv mywikix/ext.tmp mywikix/package.php
if [ $uninstall -eq 0 -a $package -eq 1 ]
then
	unzip -o mywikix/package.php.zip -d mywikix
fi
if [ -f ext.ils ]
then
	sort -u ext.ils > ext.tmp
	mv ext.tmp ext.ils
fi
rm -f ext.log
if [ "$skipped" != "" ]
then
	echo "####### SKIPPED #######" >> ext.log
	for i in $skipped
	do
		j=
		grep '^<?#.*#' $i > /dev/null
		if [ $? -eq 0 ]
		then
			j=" => "`head -1 $i | sed 's/^<?#\\(.*\\)#.*$/\\1/'`
		fi
		echo $i$j >> ext.log
	done
fi
if [ "$modified" != "" ]
then
	echo "####### MODIFIED #######" >> ext.log
	for i in $modified
	do
		echo $i >> ext.log
	done
fi
if [ -f ext.log ]
then
	grep ' => ' ext.log > /dev/null
	if [ $? -eq 0 ]
	then
		echo "####### INSTALL MANUALLY #######"
		grep ' => ' ext.log
	fi
fi
