<?
function runTime(){
	global	$startTime;

	$m = explode(" ", microtime()); $endTime = $m[0] + $m[1];
	$runTime = sprintf("%.3f", $endTime - $startTime);
	return $runTime;
}

function geni_specialchars0($str){
	$str = preg_replace("/&(?!#[0-9]+;)/", "&amp;", $str);
	$str = str_replace("<", "&lt;", $str);
	$str = str_replace(">", "&gt;", $str);
	return $str;
}

function geni_specialchars($str){
	$str = preg_replace("/&(?!#[0-9]+;)/", "&amp;", $str);
	$str = str_replace("<", "&lt;", $str);
	$str = str_replace(">", "&gt;", $str);
	$str = str_replace("\t", "        ", $str);
	$str = str_replace(" ", "&nbsp;", $str);
	return $str;
}

function geni_unspecialchars($str){
	$str = str_replace("&nbsp;", " ", $str);
	$str = str_replace("&gt;", ">", $str);
	$str = str_replace("&lt;", "<", $str);
	$str = str_replace("&amp;", "&", $str);
	return $str;
}

function geni_trim($str){
	$str = preg_replace("/^[ \t\r\n]+\n/", "\n", $str);
	$str = preg_replace("/\n[ \t\r\n]+$/", "\n", $str);
	$str = preg_replace("/^[ \t]+$/", "", $str);
	return $str;
}

function geni_whitespaces($str){
	$str = str_replace("\t", "        ", $str);
	$str = str_replace(" ", "&nbsp;", $str);
	return $str;
}

function geni_urlencode($str){
	$str = rawurlencode($str);
	$str = str_replace("-", "%2D", $str);
	$str = str_replace("_", "%5F", $str);
	$str = str_replace("%2F", "/", $str);
	$str = str_replace("//", "/%2F", $str);
#urlencode:	/*
	$str = preg_replace("/%([8-9A-F].)/e", "chr(0x\\1)", $str);
#urlencode:	*/
	return $str;
}

function strleft($str, $n){
	$cut = substr($str, 0, $n);
#utf8:	/*
	preg_match('/^(&#[0-9]+;|[\x00-\x7f]|.{2})*/', $cut, $result);
#utf8:	*/
#utf8:	preg_match('/^([\x00-\x7e]|[\xc0-\xdf].|[\xe0-\xef].{2}|'.
#utf8:		'[\xf0-\xf7].{3}|[\xf8-\xfb].{4}|[\xfc\xfd].{5})*/',
#utf8:		$cut, $result);
	return $result[0];
}

function strright($str, $n){
	$cut = strrev(substr($str, -$n));
#utf8:	/*
	preg_match('/^([\x00-\x7f]|.{2})*/', $cut, $result);
#utf8:	*/
#utf8:	preg_match('/^([\x00-\x7e]|.[\xc0-\xdf]|.{2}[\xe0-\xef]|'.
#utf8:		'.{3}[\xf0-\xf7]|.{4}[\xf8-\xfb]|.{5}[\xfc\xfd])*/',
#utf8:		$cut, $result);
	return strrev($result[0]);
}

function array_stripslashes(&$array){
	while(list($key) = each($array)){
		if(is_array($array[$key]))
			array_stripslashes($array[$key]);
		else
			$array[$key] = stripslashes($array[$key]);
	}
}

function split_word($str){
#dontsplitword:	$ret[0] = $str;
#dontsplitword:	$ret[1] = "";
#dontsplitword:	return $ret;
#utf8:	$h = ord($str[0]);
#utf8:	$s = 0;
#utf8:	if(($h & 0xfe) == 0xfc)
#utf8:		$s = 6;
#utf8:	else
#utf8:	if(($h & 0xfc) == 0xf8)
#utf8:		$s = 5;
#utf8:	else
#utf8:	if(($h & 0xf8) == 0xf0)
#utf8:		$s = 4;
#utf8:	else
#utf8:	if(($h & 0xf0) == 0xe0)
#utf8:		$s = 3;
#utf8:	else
#utf8:	if(($h & 0xe0) == 0xc0)
#utf8:		$s = 2;
#utf8:	if($s){
#utf8:		$ret[0] = substr($str, 0, $s);
#utf8:		$ret[1] = substr($str, $s);
#utf8:	/*
	if(ord($str[0]) & 0x80){
		$ret[0] = $str[0].$str[1];
		$ret[1] = substr($str, 2);
#utf8:	*/
	}else{
		if(preg_match("/^(&#[0-9]+;)(.*)$/", $str, $m)){
			$ret[0] = $m[1];
			$ret[1] = $m[2];
		}else{
			$ret[0] = $str[0];
			$ret[1] = substr($str, 1);
		}
	}
	return $ret;
}

function escape_wikix($str){
	global	$wikiXword, $bs;

	$str = str_replace("\\", "\x03", $str);
	$pattern = array(
		"/$wikiXword/",
		"\x01(//+|/\*|(?<=\*)/|^[!*#;/]|(?<!^)>|[ \t]+|''+|__+|---+|%%%+|[|:<{}[\]])\x01",
		"'/$'",
	);
	$replace = array(
		"$bs`\\1",
		"$bs\\1",
		"/$bs,",
	);
	$str = preg_replace($pattern, $replace, $str);
	return $str;
}

function escape_doit($str){
	$str = escape_wikix($str);
	$str = str_replace("\x03", "\\\\", $str);
	$str = geni_urlencode($str);
	return $str;
}

function escape_html($str){
	$str = str_replace("&lt;", "\x02&lt;", $str);
	$str = str_replace("&gt;", "\x02&gt;", $str);
	return $str;
}

function escape_bracket($str){
	$str = str_replace("[", "\x02[", $str);
	$str = str_replace("]", "\x02]", $str);
	return $str;
}

function escape_misc($str){
	$str = str_replace("''", "\x02''", $str);
	$str = str_replace("__", "\x02__", $str);
	$str = str_replace("%%%", "\x02%%%", $str);
	$str = str_replace("---", "\x02---", $str);
	$str = str_replace(":", "\x02:", $str);
	$str = str_replace("|", "\x02|", $str);
	return $str;
}

function clean4bracket($str){
	$str = str_replace("\x02", "", $str);
	$str = str_replace(":\x05 ", "\x07", $str);
	$str = str_replace("\x05", "", $str);
	$str = str_replace(" \x06", "", $str);
	$str = str_replace("\x06", "", $str);
	$str = str_replace("\x07", ":\x05 ", $str);
	$str = str_replace("\\]", "]", $str);
	return $str;
}

function clean4plugin($str){
	$str = str_replace("\\", "", $str);
	$str = str_replace("\x02", "", $str);
	$str = str_replace("\x05", "", $str);
	$str = str_replace(" \x06", "", $str);
	$str = str_replace("&nbsp;\x06", "", $str);
	$str = str_replace("\x06", "", $str);
	$str = str_replace("\x10", "", $str);
	$str = str_replace("\x11", "", $str);
	$str = str_replace("\x03", "\\", $str);
	$str = geni_unspecialchars($str);
	return $str;
}

function warn($str){
	echo "<p class=\"emphasized\"><b>$str</b></p>\n";
}

function invalid_access(){
	global	$referer, $host, $scriptdir;

#noinvalidaccess:	return 0;

	if($referer == "-")
		return 1;
	if(preg_match("'^http://$host$scriptdir'", $referer))
		return 0;
	return 1;
}

function is_msie(){
	global	$agent;

	return (strpos($agent, "MSIE")===false?0:1);
}

function ndays($year, $month){
	for($day=31; !checkdate($month, $day, $year); $day--);

	return $day;
}

function month($year, $month, $prefix, $suffix, $flags, $opt, $attr){
	global	$admin, $path, $Y, $M, $D;

	$imonth = "$year-".($month>9?$month:"0$month");

	$mpage = "$prefix[0]$imonth$suffix[0]";
	if(($flags&0x1) && ($id = pageid($mpage)) &&
			($admin || !is_hidden($mpage))){
		$todo = "todo";
		$doit = "display";
	}else{
		$todo = "general";
		$doit = "goto";
	}

	if($attr == ""){
		$pmonth = $month - 1;
		$pmonth = ($pmonth?"$year-".($pmonth>9?$pmonth:"0$pmonth"):
				($year-1)."-12");
		$nmonth = $month + 1;
		$nmonth = ($nmonth<13?"$year-".($nmonth>9?$nmonth:"0$nmonth"):
				($year+1)."-01");
	}
	$str =
"<table class=\"calendar\"$attr>\n".
"<caption>".
($attr==""?"<a class=\"a\" href=\"index.php?1,doit=\x03Calendar$opt$pmonth\">&lt;</a>":"").
"<a class=\"a\" href=\"index.php?$doit=$prefix[1]$imonth$suffix[1]\"><span class=\"$todo\">$imonth</span></a>".
($attr==""?"<a class=\"a\" href=\"index.php?1,doit=\x03Calendar$opt$nmonth\">&gt;</a>":"").
"</caption>\n".
"<tr class=\"calendar_header\">".
"<td align=\"right\"><span class=\"sunday\">S</span></td>".
"<td align=\"right\"><span class=\"general\">M</span></td>".
"<td align=\"right\"><span class=\"general\">T</span></td>".
"<td align=\"right\"><span class=\"general\">W</span></td>".
"<td align=\"right\"><span class=\"general\">T</span></td>".
"<td align=\"right\"><span class=\"general\">F</span></td>".
"<td align=\"right\"><span class=\"saturday\">S</span></td>".
"</tr>\n".
"<tr>";

	$first = date("w", mktime(0, 0, 0, $month, 1, $year));

	for($i=0,$col=0; $i<$first; $i++,$col++)
		$str .= "<td>&nbsp;</td>";

	$today = 0;
	if($year == $Y && $month == $M)
		$today = $D;
	$ndays = ndays($year, $month);

	$imonth = ($month>9?$month:"0$month");

	$btodo = "";
	$etodo = "";
	$doit = "goto";
	$lunardate = "";

	for($i=1; $i<=$ndays; $i++,$col++){
		$rem = $col % 7;
		if($col && !$rem)
			$str .= "</tr>\n<tr>";
		$todo = ($rem==0?"sunday":($rem==6?"saturday":"general"));
		$class = "";
		if($today == $i)
			$class = " class=\"calendar_today\"";
		$date = "$year-$imonth-".($i>9?$i:"0$i");
		if($flags&0x1){
			$dpage = "$prefix[0]$date$suffix[0]";
			if(($id = pageid($dpage)) &&
				($admin || !is_hidden($dpage))){
				$todo = "todo";
				$doit = "display";
			}else
				$doit = "goto";
		}
		if($flags&0x2){
			$lunardate = exec("$path[hds2l] - $year $month $i");
			$lunardate = "<br /><small>".
					(substr($lunardate, 4, 2)+0).".".
					(substr($lunardate, 6)+0)."</small>";
		}
		$str .= "<td$class align=\"right\"><a class=\"$todo\" href=\"index.php?$doit=$prefix[1]$date$suffix[1]\">$i$lunardate</a></td>";
	}

	$n = $col % 7;
	$n = 7 - ($n?$n:7);
	for($i=0; $i<$n; $i++)
		$str .= "<td>&nbsp;</td>";

	$str .= "</tr>\n</table>";

	return $str;
}

function calendar($info){
	global	$calendarCols, $admin, $path, $Y, $M, $D;

	$str = "";
	if(!preg_match("/(@?\*?)(\{.*?(?<!\\\\)\}\{.*?(?<!\\\\)\})?([0-9]*)(?:-([0-9]*))?/", $info, $m))
		return $str;

	$opt = $m[1];
	$flags = 0;
	if(strpos($opt, "@") !== false && is_executable($path['hds2l']))
		$flags |= 0x2;
	if(strpos($opt, "*") !== false)
		$flags |= 0x1;
	$prefix[0] = $prefix[1] = "";
	$suffix[0] = $suffix[1] = "";
	if($m[2] != ""){
		$opt .= geni_urlencode($m[2]);
		preg_match("/\{(.*?)(?<!\\\\)\}\{(.*?)(?<!\\\\)\}/", $m[2], $_m);
		$prefix[0] = addslashes($_m[1]);
		$suffix[0] = addslashes($_m[2]);
		$prefix[1] = geni_urlencode($_m[1]);
		$suffix[1] = geni_urlencode($_m[2]);
	}
	$year = $m[3];
	if(isset($m[4]) && $m[4] != "")
		$month = $m[4];

	if($year <= 0)
		$year = $Y;

	if(isset($month) && $month <= 0)
		$month = $M;

	$year += 0;
	if(isset($month)){
		$month += 0;
		return month($year, $month, $prefix, $suffix,
				$flags, $opt, "");
	}else{
		$ypage = "$prefix[0]$year$suffix[0]";
		if(($flags&0x1) && ($id = pageid($ypage)) &&
				($admin || !is_hidden($ypage))){
			$todo = "todo";
			$doit = "display";
		}else{
			$todo = "general";
			$doit = "goto";
		}

		if($flags&0x2){
			$lunaryear = explode(" ",
					exec("$path[hds2l] $year 6 1"));
			$lunaryear = " $lunaryear[6]³â $lunaryear[7]¶ì";
		}else
			$lunaryear = "";

		$str .=
"<table class=\"calendar_year\">\n".
"<caption>".
"<a class=\"a\" href=\"index.php?1,doit=\x03Calendar$opt".($year-1)."\">&lt;</a>".
"<a class=\"a\" href=\"index.php?$doit=$prefix[1]$year$suffix[1]\"><span class=\"$todo\">$year$lunaryear</span></a>".
"<a class=\"a\" href=\"index.php?1,doit=\x03Calendar$opt".($year+1)."\">&gt;</a>".
"</caption>\n".
"<tr valign=\"top\">";

		for($i=1; $i<=12; $i++){
			$str .= "<td>";
			$str .= month($year, $i, $prefix, $suffix,
					$flags, $opt, " width=\"100%\"");
			$str .= "</td>";
			if(!($i%$calendarCols) && $i != 12)
				$str .= "</tr>\n<tr valign=\"top\">";
		}

		$str .= "</tr>\n</table>";
	}

	return $str;
}

function mkdirp($dir){
	if(!is_dir($dir)){
		$subdir = explode("/", $dir);
		$p = "";
		$oldumask = umask(0);
		foreach($subdir as $d){
			$p .= "/$d";
			if(is_dir($p))
				continue;
			mkdir($p, 0777);
#			exec("chmod 1777 $p");
		}
		umask($oldumask);
	}
}

function php($str, $mode = 0){
	global	$wikiXdir;

	if($str == "")
		return "";

	$script = "$wikiXdir/myphp/";
	if(($p = strpos($str, "?"))){
		$script .= substr($str, 0, $p);
		$param   = substr($str, $p+1);
	}else{
		$script .= $str;
		$param   = "";
	}
	$script .= ".php";

	if(strpos($script, "/../") !== false || !is_readable($script))
		return "";

	ob_start();
	include($script);
	$str = ob_get_contents();
	ob_end_clean();

	if($mode)
		$str = str_replace("\\", "\x03", $str);
	else
		$str = str_replace("\\\\", "\x03", $str);

	return $str;
}

function mafi($str, $mode = 0){
	global	$wikiXdir, $path;

	if(!preg_match("/;$/", $str))
		$str .= ";";
	$str = str_replace('\\', '\\\\', $str);
	$str = str_replace('"', '\\"', $str);
	$str = str_replace('$', '\\$', $str);

	if(($n = preg_match_all("/grfbegin\([ \t]*\\\\\"(.*?)\\\\\"[ \t]*,[ 0-9]+,[ 0-9]+\)[ \t]*;/", $str, $m))){
		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}
		for($i=0; $i<$n; $i++)
			mkdirp("$wikiXdir/".dirname($m[1][$i]));
	}

	ob_start();
	system("echo \"$str\" | $path[mafi]");
	$str = ob_get_contents();
	ob_end_clean();

	if($n){
		for($i=0; $i<$n; $i++){
			if(is_file("$wikiXdir/".$m[1][$i]))
				add_file($m[1][$i]);
		}
	}

	if($mode)
		$str = geni_specialchars($str);
	$str = str_replace("\\\\", "\x03", $str);
	if(preg_match("/\n$/", $str))
		$str = substr($str, 0, -1);

	return $str;
}

function tex($res, $file, $str, $opt, $tex = "tex"){
	global	$wikiXdir, $path, $bs;

	$alias = 1;
	$trans = 0;
	if($opt != ""){
		switch($opt[0]){
		case "@":
			$alias = 0;
			if($opt == "@*")
				$trans = 1;
			break;
		case "*":
			$trans = 1;
			break;
		}
	}

	$str = preg_replace("'\\\\/(?=[a-zA-Z])'", $bs, $str);
	$str = str_replace("\x03", "\\\\", $str);
	$str = str_replace("\\//", "//", $str);
	$str = str_replace("\\/*", "/*", $str);
	$str = str_replace('\\"', '"', $str);

	if($res <=0 || $res > 160)
		$res = 100;

	$fpath = tempnam("tmpfile", $tex);
	$fname = basename($fpath);

	$fp = fopen("$fpath.tex", "w");
	fwrite($fp, $str);
	fclose($fp);

	mkdirp("$wikiXdir/".dirname($file));

	exec("
	cd tmpfile;
	$path[$tex] $fname.tex;
	$path[dvips] -o - $fname.dvi |".
	$path[($alias?'gs_alias':'gs')].
		" -r${res}x$res -sDEVICE=ppmraw -sOutputFile=- -sNOPAUSE -q - |
	$path[pnmcrop] |
	$path[pnmtopng] ".($trans?"-transparent '#ffffff' ":"")."> '../$file';
	$path[rm] -f $fname $fname.*;
	");

	if(is_file("$wikiXdir/$file"))
		add_file($file);

	return "";
}

function latex($res, $file, $str, $opt){
	return tex($res, $file, $str, $opt, "latex");
}

function gnuplot($file, $str, $opt){
	global	$wikiXdir, $path;

	$trans = 0;
	if($opt == "*")
		$trans = 1;

	$str = str_replace("\x03", "\\\\", $str);
	$str = str_replace("\\//", "//", $str);
	$str = str_replace("\\/*", "/*", $str);
	$str = str_replace('\\\\"', '\\\\\\"', $str);
	$pattern = array(
		'/(?:^|[\n;])[ \t]*!/',
		'/(?:^|[\n;])[ \t]*(?:shell|call|load)[^a-zA-Z]/',
		'/(?:^|[\n;])[ \t]*set[ \t](?:terminal|output)[^a-zA-Z]/',
	);
	$replace = array(
		'#',
		'#',
		'#',
	);
	$str = preg_replace($pattern, $replace, $str);

	mkdirp("$wikiXdir/".dirname($file));

	if($trans){
		$fpath = tempnam("tmpfile", "gnuplot");
		$fname = basename($fpath);
		$str = "set terminal pbm color\nset output '$fname.pbm'\n$str";
		exec("
		cd tmpfile;
		echo \"$str\" | $path[gnuplot];
		$path[pnmtopng] -transparent '#ffffff' $fname.pbm > '../$file';
		$path[rm] -f $fname $fname.*;
		");
	}else{
		$str = "set terminal png color\nset output '../$file'\n$str";
		exec("
		cd tmpfile;
		echo \"$str\" | $path[gnuplot];
		");
	}

	if(is_file("$wikiXdir/$file"))
		add_file($file);

	return "";
}

function search_query(&$search, &$tc, &$ibegin, &$iend, &$order, &$regex,
		&$highlight){
	global	$backendDB, $db_, $admin, $caseinsensitiveSearch,
		$highlightedSearch;

	if($caseinsensitiveSearch){
		$ibegin = "lower(";
		$iend = ")";
	}else{
		$ibegin = "";
		$iend = "";
	}
	$use_highlight = $highlightedSearch;

	$tc = 0x0;
	$order = " order by ${db_}page.name asc";
	$regex = 0;
	$iregex = "";
	$highlight = "";
	$range = 0;
	if(preg_match("'(.*)/([-tcrih~RMP@]*)$'", $search, $m)){
		$search = $m[1];
		$l = strlen($m[2]);
		for($i=0; $i<$l; $i++){
			switch($m[2][$i]){
			case "t":
				$tc |= 0x1;
				break;
			case "c":
				$tc |= 0x2;
				break;
			case "r":
				$regex = 1;
				$range = 0;
				break;
			case "i":
				if($caseinsensitiveSearch){
					$ibegin = "";
					$iend = "";
				}else{
					$ibegin = "lower(";
					$iend = ")";
				}
				break;
			case "h":
				if($highlightedSearch)
					$use_highlight = 0;
				else
					$use_highlight = 1;
				break;
			case "~":
				$regex = 0;
				$range = 1;
				break;
			case "R":
				$order = " order by ${db_}data.mtime desc, ${db_}page.name asc";
				break;
			case "M":
				$order = " order by ${db_}page.hits desc, ${db_}page.name asc";
				break;
			case "P":
				$order = " order by ${db_}page.ctime desc, ${db_}page.name asc";
				break;
			case "-":
				if($order != ""){
					$order = str_replace("asc", "ASC", $order);
					$order = str_replace("desc", "asc", $order);
					$order = str_replace("ASC", "desc", $order);
				}
				break;
			case "@":
				$order = "";
				break;
			}
		}
	}
	if($regex && $backendDB != "mysql" && $ibegin != "")
		$iregex = "*";
	$where = $search;
	if(!$regex && !$range){
		$where = str_replace(" & ", " \x01 ", $where);
		$where = str_replace(" | ", " \x02 ", $where);
		$where = str_replace("(", "(\x03", $where);
		$where = str_replace(")", "\x03)", $where);
		$pattern = array(
			"/^(?!\()|(?<!\))$/",
			"/(?<!\)) \x01 /",
			"/(?<!\)) \x02 /",
			"/ \x01 (?!\()/",
			"/ \x02 (?!\()/",
			"/\x03~(.*?)\x03/",
		);
		$replace = array(
			"\x03",
			"\x03 \x01 ",
			"\x03 \x02 ",
			" \x01 \x03",
			" \x02 \x03",
			"\x07\\1\x07",
		);
		$where = preg_replace($pattern, $replace, $where);
		$where = str_replace("\x01", "and", $where);
		$where = str_replace("\x02", "or", $where);
	}
	$_where = $where;
	if($range)
		$where = str_replace(" ~ ", "\x02", $where);
	$where = preg_replace("/\\\\x([0-9a-f]{2})/e", "chr(0x\\1)", $where);
	if(!$regex)
		$where = str_replace("\\", "\\\\", $where);
	$where = addslashes($where);

	$tcop = "";
	if($tc == 0x3)
		$tcop = " and ";
	else
	if(!$tc){
		$tc = 0x7;
		$tcop = " or ";
	}
	if($regex){
		if($use_highlight)
			$highlight = $_where;
		if($backendDB == "mysql")
			$where = ($tc&0x1?"${ibegin}${db_}page.name$iend regexp
					$ibegin'$where'$iend":"").$tcop.
				($tc&0x2?"${ibegin}${db_}data.content$iend regexp
					$ibegin'$where'$iend":"");
		else
			$where = ($tc&0x1?"${db_}page.name ~$iregex '$where'":"").
				$tcop.
				($tc&0x2?"${db_}data.content ~$iregex '$where'":"");
	}else
	if($range){
		list($from, $to) = explode("\x02", $where);
		if($from != "")
			$where = ($tc&0x1?"${ibegin}${db_}page.name$iend >=
					$ibegin'$from'$iend":"").$tcop.
				($tc&0x2?"${ibegin}${db_}data.content$iend >=
					$ibegin'$from'$iend":"");
		if($to != "")
			$where = ($from==""?"":"($where) and (").
				($tc&0x1?"${ibegin}${db_}page.name$iend <
					$ibegin'$to'$iend":"").$tcop.
				($tc&0x2?"${ibegin}${db_}data.content$iend <
					$ibegin'$to'$iend":"").
				($from==""?"":")");
	}else{
		if($use_highlight){
			$queries = explode("\x03", $_where);
			$nqueries = count($queries);
			for($i=1;$i<$nqueries;$i+=2)
				$highlight .= ($highlight==""?"":"|").
					preg_quote($queries[$i]);
		}
		$where = str_replace("%", "\\%", $where);
		$where = str_replace("_", "\\_", $where);
		$where = preg_replace("/\x03(.*?)\x03/s",
				(($tc&0x3)==0x3?"(":"").
				($tc&0x1?"${ibegin}${db_}page.name$iend like
					$ibegin'%\\1%'$iend":"").$tcop.
				($tc&0x2?"${ibegin}${db_}data.content$iend like
					$ibegin'%\\1%'$iend":"").
				(($tc&0x3)==0x3?")":""), $where);
		$where = preg_replace("/\x07(.*?)\x07/s",
				(($tc&0x3)==0x3?"(":"").
				($tc&0x1?"${ibegin}${db_}page.name$iend not like
					$ibegin'%\\1%'$iend":"").
				(($tc&0x3)==0x3?" and ":"").
				($tc&0x2?"${ibegin}${db_}data.content$iend not like
					$ibegin'%\\1%'$iend":"").
				(($tc&0x3)==0x3?")":""), $where);
	}

	$query = "select ${db_}page.id, ${db_}page.name, ${db_}page.hits,
			${db_}page.ctime, ${db_}page.locked, ${db_}page.hidden,
			${db_}data.author, ${db_}data.ip, ${db_}data.mtime
			from ${db_}page, ${db_}data
			where ${db_}page.id=${db_}data.id and
			${db_}page.version=${db_}data.version ".
			($admin?"":"and ${db_}page.hidden=0 ").
			"and ${db_}data.content!='\x01'
			and ($where) \x02$order";

	return $query;
}

function macro($s, $content){
	global	$bs;

	if($s == "$"){
		$s = "\\$";
		$sstr = "\$";
	}else
		$sstr = $s;
	if(preg_match("/^\\\\def".$s."[0-9a-zA-Z_]*[ \t]*=(?:\{\n.*?\n\}|[^\n]*)$/ms", $content)){
		$content = preg_replace("/^(\\\\def".$s."[0-9a-zA-Z_]*)[ \t]*=(\{\n.*?\n\}|[^\n]*)$/ms", "\\1=\\2", $content);
#		if(preg_match("/^(\\\\def".$s."[0-9a-zA-Z_]*=)\{\n(.*?)\n\}$/ms", $content))
		$content = preg_replace("/^(\\\\def".$s."[0-9a-zA-Z_]*=)\{\n(.*?)\n\}$/mse", "'\\1'.str_replace('\n', '\x07', '\\2')", $content);
		$n = preg_match_all("/^\\\\def".$s."([0-9a-zA-Z_]*)=(.*)$/m", $content, $m);
		$line = explode("\n", $content);
		$nlines = count($line);
		for($i=$n-1; $i>=0; $i--){
			$var = $m[1][$i];
			$val = $m[2][$i];
			$def = "\\def$sstr$var=$val";
			if(!preg_match("/\\\\".$s.$var."(?![0-9a-zA-Z_])/",
						$content)){
				for($j=$nlines-1;
					$j>=0 && $line[$j]!=$def; $j--);
				$line[$j] = "//";
				continue;
			}
			$val = str_replace("\x07", "\n", $val);
			$val = str_replace("%+", "\x05", $val);
			$val = str_replace("%n", "\n", $val);
			$val = preg_replace("/^%-/m", "", $val);
			$val = addslashes($val);
			$val = str_replace("$", "\\$", $val);
			$pattern = array(
				"/\\\\".$s.$var."\\\\\{(.*?)\\\\\}/e",
				"/\\\\".$s.$var."\{(.*?)(?<!\\\\)\}/e",
				"/\\\\".$s.$var."(?![0-9a-zA-Z_])/e",
			);
			$replace = array(
				(strpos($val, "%s")===false?
					"str_replace('$bs\"', '\"',
					'$val$bs{\\1$bs}')"
				:
					"str_replace('%s',
					str_replace('$bs\"', '\"',
					'\\1'),
					str_replace('$bs\"', '\"',
					'$val'))"),
				(strpos($val, "%s")===false?
					"str_replace('$bs\"', '\"',
					'$val{\\1}')"
				:
					"str_replace('%s',
					str_replace('$bs\"', '\"',
					'\\1'),
					str_replace('$bs\"', '\"',
					'$val'))"),
				"str_replace('%s', '',
					str_replace('$bs\"', '\"',
					'$val'))",
			);
			for($j=$nlines-1; $j>=0 && $line[$j]!=$def; $j--){
				if(!preg_match("/\\\\".$s.$var."(?![0-9a-zA-Z_])/",
					$line[$j]))
					continue;
				$line[$j] = preg_replace($pattern, $replace,
					$line[$j]);
			}
			$line[$j] = "//";
		}
		$content = implode("\n", $line);
		$content = str_replace("\x05", "%", $content);
	}
	$content = preg_replace("/\\\\".$s."[0-9a-zA-Z_]*/", "", $content);
	return $content;
}

function smacro($content){
	global	$bs;

	if(preg_match("/^\\\\def=[0-9a-zA-Z_]*[ \t]*=(?:\{\n.*?\n\}|[^\n]*)$/ms", $content)){
		$content = preg_replace("/^(\\\\def=[0-9a-zA-Z_]*)[ \t]*=(\{\n.*?\n\}|[^\n]*)$/ms", "\\1=\\2", $content);
#		if(preg_match("/^(\\\\def=[0-9a-zA-Z_]*=)\{\n(.*?)\n\}$/ms", $content))
		$content = preg_replace("/^(\\\\def=[0-9a-zA-Z_]*=)\{\n(.*?)\n\}$/mse", "'\\1'.str_replace('\n', '\x07', '\\2')", $content);
		$n = preg_match_all("/^\\\\def=([0-9a-zA-Z_]*)=(.*)$/m", $content, $m);
		$line = explode("\n", $content);
		$nlines = count($line);
		for($i=$n-1; $i>=0; $i--){
			$var = $m[1][$i];
			$val = $m[2][$i];
			$def = "\\def=$var=$val";
			if(!preg_match("/\\\\=$var(?![0-9a-zA-Z_])/",
						$content)){
				for($j=$nlines-1;
					$j>=0 && $line[$j]!=$def; $j--);
				if($j >= 0)
					$line[$j] = str_replace("\\", "\x02",
							$line[$j]);
				continue;
			}
			$val = str_replace("\x07", "\n", $val);
			$val = str_replace("%+", "\x05", $val);
			$val = str_replace("%n", "\n", $val);
			$val = preg_replace("/^%-/m", "", $val);
			$val = addslashes($val);
			$val = str_replace("$", "\\$", $val);
			$pattern = array(
				"/\\\\=$var\\\\\{(.*?)\\\\\}/e",
				"/\\\\=$var\{(.*?)(?<!\\\\)\}/e",
				"/\\\\=$var(?![0-9a-zA-Z_])/e",
			);
			$replace = array(
				(strpos($val, "%s")===false?
					"str_replace('$bs\"', '\"',
					'$val$bs{\\1$bs}')"
				:
					"str_replace('%s',
					str_replace('$bs\"', '\"',
					'\\1'),
					str_replace('$bs\"', '\"',
					'$val'))"),
				(strpos($val, "%s")===false?
					"str_replace('$bs\"', '\"',
					'$val{\\1}')"
				:
					"str_replace('%s',
					str_replace('$bs\"', '\"',
					'\\1'),
					str_replace('$bs\"', '\"',
					'$val'))"),
				"str_replace('%s', '',
					str_replace('$bs\"', '\"',
					'$val'))",
			);
			for($j=$nlines-1; $j>=0 && $line[$j]!=$def; $j--){
				if(!preg_match("/\\\\=$var(?![0-9a-zA-Z_])/",
					$line[$j]))
					continue;
				$line[$j] = preg_replace($pattern, $replace,
					$line[$j]);
			}
			if($j >= 0)
				$line[$j] = str_replace("\\", "\x02", $line[$j]);
		}
		$content = implode("\n", $line);
		$content = str_replace("\x05", "%", $content);
	}
	$pattern = array(
		"/\\\\=[0-9a-zA-Z_]*/",
		"/^(\x02def=[0-9a-zA-Z_]*=)(.*\x07.*)$/me",
	);
	$replace = array(
		"",
		"'\\1{\n'.str_replace('\x07', '\n', '\\2').'\n}'",
	);
	$content = preg_replace($pattern, $replace, $content);
	$content = str_replace("\x07", "\n", $content);
	return $content;
}

function condition($content){
	global	$bs;

	while(preg_match("'^\\\\if([0-9]*)".
			"!?/.*?(?<!\\\\)/(?:.*?)(?<!\\\\)/.*?\n".
			"\\\\else\\1\n.*?^\\\\fi\\1$'ms", $content)){
		$content = preg_replace("'^\\\\if([0-9]*)".
				"(!?)/(.*?)(?<!\\\\)/(.*?)(?<!\\\\)/(.*?)\n".
				"(.*?)^\\\\else\\1\n(.*?)^\\\\fi\\1$'mse",
			"str_replace('$bs\"', '\"',
			(\\2preg_match('/'.
				str_replace('${bs}x5c', '$bs$bs',
				str_replace('\x03', '$bs$bs$bs$bs',
				str_replace('$bs\"', '\"', subs('\\3')))).
				'/'.str_replace('e', '', '\\5'),
				preg_replace('/$bs$bs$bs${bs}x([0-9a-f]{2})/e',
#php403:					/*
					'chr(0x$bs${bs}1)',
#php403:					*/
#php403:					'chr(0x$bs$bs'.'1)',
				str_replace('$bs/', '/',
				str_replace('\x03', '$bs$bs$bs$bs',
				str_replace('$bs\"', '\"', subs('\\4'))))))?
				'\\6':'\\7')).'\x07'",
			$content);
		$content = str_replace("\x07\n", "", $content);
		$content = str_replace("\n\x07", "", $content);
		$content = str_replace("\\\\", "\x03", $content);
	}
	return $content;
}

function replace($r, $content){
	global	$bs;

	while(preg_match("'^\\\\b$r([0-9]*)".
				"/.*?(?<!\\\\)/.*?(?<!\\\\)/.*?\n".
				".*?\n\\\\e$r\\1$'ms",
			$content)){
		$content = preg_replace("'^\\\\b$r([0-9]*)".
				"/(.*?)(?<!\\\\)/(.*?)(?<!\\\\)/(.*?)\n".
				"(.*?)\n\\\\e$r\\1$'mse",
			"preg_replace('/'.
				str_replace('${bs}x5c', '$bs$bs',
				str_replace('\x03', '$bs$bs$bs$bs',
				str_replace('$bs\"', '\"', '\\2'))).
				'/'.str_replace('e', '', '\\4'),
				preg_replace('/$bs$bs$bs${bs}x([0-9a-f]{2})/e',
#php403:					/*
					'chr(0x$bs${bs}1)',
#php403:					*/
#php403:					'chr(0x$bs$bs'.'1)',
				str_replace('$bs/', '/',
				str_replace('$bs\"', '\"', '\\3'))),
				str_replace('\x03', '$bs$bs$bs$bs',
				str_replace('$bs\"', '\"', '\\5')))",
			$content);
		$content = str_replace("\\\\", "\x03", $content);
	}
	return $content;
}

function rreplace($content){
	global	$bs;

	while(preg_match("'^\\\\brreplace([0-9]*)".
				"/.*?(?<!\\\\)/.*?(?<!\\\\)/.*?\n".
				".*?\n\\\\erreplace\\1$'ms",
			$content)){
		$content = preg_replace("'^(\\\\brreplace([0-9]*)".
				"/(.*?)(?<!\\\\)/(.*?)(?<!\\\\)/(.*?)\n)".
				"(.*?)(\n\\\\erreplace\\2)$'mse",
			"'\x07'.str_replace('$bs\"', '\"', '\\1').
			preg_replace('/'.
				str_replace('${bs}x5c', '$bs$bs',
				str_replace('\x03', '$bs$bs$bs$bs',
				str_replace('$bs\"', '\"', '\\3'))).
				'/'.str_replace('e', '', '\\5'),
				preg_replace('/$bs$bs$bs${bs}x([0-9a-f]{2})/e',
#php403:					/*
					'chr(0x$bs${bs}1)',
#php403:					*/
#php403:					'chr(0x$bs$bs'.'1)',
				str_replace('$bs/', '/',
				str_replace('$bs\"', '\"', '\\4'))),
				str_replace('\x03', '$bs$bs$bs$bs',
				str_replace('$bs\"', '\"', '\\6'))).
			'\\7\x07'",
			$content);
		$content = str_replace("\\\\", "\x03", $content);
	}
	$content = str_replace("\x07", "", $content);
	return $content;
}

function hidecode($content, $lock = 0, $src = 0){
	global	$admin;

	if($src && ($admin || !$lock))
		return $content;

	$content = str_replace("\\\\", "\x03", $content);
	$pattern = array(
		"/\\\\beginhide(?![a-zA-Z])(.*?)\\\\endhide(?![a-zA-Z])/s",
		"/^\\\\bhide\n(.*?)\n\\\\ehide$/ms",
	);
	$replace = array(
		($admin?"\\1":""),
		($admin?"\\1":($src?"":"\\1")),
	);
	$content = preg_replace($pattern, $replace, $content);
	$content = str_replace("\x03", "\\\\", $content);
	return $content;
}

function diff($content0, $content1){
	$line0 = explode("\n", $content0);
	$line1 = explode("\n", $content1);
	$nlines0 = count($line0);
	$nlines1 = count($line1);
	$str = "";
	for($i0=0,$i1=0; $i0<$nlines0; $i0++){
		for($i=$i1; $i<$nlines1&&$line0[$i0]!==$line1[$i]; $i++);
		if($i < $nlines1){
			for($j=$i1; $j<$i; $j++)
				$str .= "+$i0 $line1[$j]\n";
			$i1 = $i + 1;
		}else{
			$str .= "-$i0\n";
			$j=$i0+1;
			if($i1 < $nlines1)
				for(; $j<$nlines0&&$line0[$j]!==$line1[$i1]; $j++);
			if($j < $nlines0){
				for($i=$i0+1; $i<$j; $i++)
					$str .= "-$i\n";
				$i0 = $j - 1;
			}
		}
	}
	for($j=$i1; $j<$nlines1; $j++)
		$str .= "+$i0 $line1[$j]\n";
	return $str;
}

function patch($content0, $diff){
	$line0 = explode("\n", $content0);
	$lined = explode("\n", $diff);
	$nlines0 = count($line0);
	$nlinesd = count($lined);
	$line0p = "";
	for($id=0; $id<$nlinesd; $id++){
		switch($lined[$id][0]){
		case "+":
			$p = strpos($lined[$id], " ");
			$i = substr($lined[$id], 1, $p-1);
			$l = substr($lined[$id], $p+1);
			if($i)
				$line0[$i-1] .= "\n$l";
			else
				$line0p .= "$l\n";
			break;
		case "-":
			$line0[substr($lined[$id], 1)] = "\x01";
			break;
		}
	}
	$line0[0] = "$line0p$line0[0]";
	$str = implode("\n", $line0);
	$str = str_replace("\n\x01", "", $str);
	$str = str_replace("\x01\n", "", $str);
	return $str;
}
?>
