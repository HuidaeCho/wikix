<?
# \x00		EOL
# \x01		PCRE separator
# \x02		parsed
# \x03		\\
# \x04		TableOfContents
# \x05 \x06	WordBreak
# \x07		temporary character
# \x08		temporary character
# \x09		\t
# \x0a		\n
# \x0b
# \x0c
# \x0d		\r
# \x0e
# \x0f
# \x10 \x11	wikiXword
# \x12 ~ \x1f	reserved unprintable characters

function DisplayContent($content, $mode = 1, $group = 0, $reset = 0){
	global	$db, $bs, $wikiXversion, $wikiXword, $admin, $author,
		$ip, $host, $scriptdir, $script, $action, $euri,
		$pagename0, $pagename, $dpagename, $pageName, $pagenamE,
		$npages, $Y, $M, $D, $today, $now, $timestamp, $btime,
		$tableAttr;
	static	$once = 1, $depth = 0, $DisplayPool = array(),
		$_dohtml, $_dobracket, $_donetlink, $_dointerwiki, $_dowikiword,
		$_dotable, $_dopre, $_dolisting, $_domultilinelisting,
		$_doheading, $_dolinebreak, $_dofont, $_dowhitespaces, $_donbsp,
		$_domyplugin, $_doplugin, $_dosubs, $_domysubs, $_dodot, $_dop,
		$_dobr, $_htmli, $_htmlp, $_table, $_ntdattrs,
		$_listingi, $_listingp;

#echo "r1: ".runTime()."<br />";
	$content = str_replace("\r", "", $content);

	if($reset)
		include_page("\x01", "\x01");
	$content = include_page($pagename0, $content);

	$content = mystage0($content, $mode);

	$content = str_replace("\\\\", "\x03", $content);
	$pattern = array(
		"/^(?:.*\n)?\\\\begin\n/s",
		"/\n\\\\end(?:\n.*)?$/s",
		"/\\\\beginhide(?![a-zA-Z])[ \t]?(.*?)\\\\endhide(?![a-zA-Z])/s",
		"/^\\\\basis\n(.*?)\n\\\\easis$/mse",
		"/^\\\\bnobs\n(.*?)\n\\\\enobs$/mse",
		"/^\\\\bnos\n(.*?)\n\\\\enos$/mse",
		"/^\x03bhide\n(.*?)\n(\x03|\\\\)ehide$/ms",
		"/^(\x03|\\\\)bhide\n(.*?)\n\x03ehide$/ms",
		"/^\\\\bhide\n(.*?)\n\\\\ehide$/ms",
	);
	$replace = array(
		"",
		"",
		($admin?"\\1":""),
		"str_replace('/', '/\x06',
			str_replace('$bs$bs', '\x03',
			str_replace('$bs\"', '\"', '\\1')))",
		"str_replace('$bs$bs', '\x03',
			str_replace('$bs\"', '\"', '\\1'))",
		"str_replace('/', '/\x06',
			str_replace('$bs\"', '\"', '\\1'))",
		($admin?"\\1":""),
		($admin?"\\1":""),
		"\\1",
	);
	$content = preg_replace($pattern, $replace, $content);

	$content = preg_replace("/\\\\myphp\{(.*?)(?<!\\\\)\}/e",
		"php(str_replace('\x03', '$bs$bs', stripslashes('\\1')))",
		$content);

	$content = str_replace("\\x09", "\x09", $content);
	$content = str_replace("\\x0a", "\x0a", $content);
	$content = str_replace("\\\n", "", $content);
	$content = str_replace("\\^\n", "", $content);
	$content = str_replace("\n\\^", "", $content);
	$content = str_replace("\\+\n", "", $content);
	$content = str_replace("\n\\+", "", $content);

	$content = preg_replace("/\\\\\{(.*?)\\\\\}/se",
			"'$bs{'.str_replace('\n', '\x08',
				str_replace('$bs\"', '\"', '\\1')).'$bs}'",
			$content);
	$content = macro("=", $content);
#echo "r2: ".runTime()."<br />";
	$content = macro("$", $content);
#echo "r3: ".runTime()."<br />";
	$content = macro("@", $content);
#echo "r4: ".runTime()."<br />";
	$content = str_replace("\x08", "\n", $content);

	$content = condition($content);

	$content = replace("sreplace", $content);
	$content = replace("rreplace", $content);
	$content = replace("replace", $content);

	$content = str_replace("\\x5c", "\x5c", $content);
#	$content = str_replace("\\\\", "\x03", $content);

	$content = mystage1($content, $mode);

	$epagename = escape_wikix($pagename0);
	$dpagename = str_replace("\\", "\x03", $pagename);

	$npostits = 0;
	$postitvisibility = (strpos($content, "//hiddenpostit\n")===false?
			"visible":"hidden");

#echo "r5: ".runTime()."<br />";
	$pattern = array(
		"'^\\\\j([0-9]*)([=+-])([+-]?[0-9]*)(?:\n|$)|\\\\j([0-9]*)(?![a-zA-Z])'me",
		"'^\\\\k([0-9]*)([=+-])([+-]?[0-9]*)(?:\n|$)|\\\\k([0-9]*)(?![a-zA-Z])'me",
		"'^[ \t]*//.*\n'm",
		"'://'",
		"'(?<![\\\\/])//.*'",
		"'\x07'",
		"'(?<!\\\\)/\*.*?\*/'s",
		"'^[ \t]*\x07[ \t]*\n|\x07'm",
		"'\\\\begincomment(?![a-zA-Z]).*?\\\\endcomment(?![a-zA-Z])'s",
		"'\\\\[pP]agename0(?![a-zA-Z])'",
		"'\\\\[pP]ageName(?![a-zA-Z])'",
		"'\\\\[pP]agenamE(?![a-zA-Z])'",
		"'\\\\n[pP]ages(?![a-zA-Z])'",
		"'\\\\y(?![a-z])'i",
		"'\\\\m(?![a-z])'i",
		"'\\\\d(?![a-z])'i",
		"'\\\\t(?![a-z])'i",
		"'\\\\s(?![a-z])'i",
		"'^\\\\i([0-9]*)([=+-])([+-]?[0-9]*)(?:\n|$)|\\\\i([0-9]*)(?![a-zA-Z])'me",
		"'\\\\php\{(.*?)(?<!\\\\)\}'e",
		"'\\\\begin(s?)mafi(?![a-zA-Z])[ \t]?(.*?)\\\\end\\1mafi(?![a-zA-Z])'se",
		"'\\\\begintex(@?\*?)([0-9]*)\{(.*?)(?<!\\\\)\}(.*?)\\\\endtex(?![a-zA-Z])'se",
		"'\\\\beginlatex(@?\*?)([0-9]*)\{(.*?)(?<!\\\\)\}(.*?)\\\\endlatex(?![a-zA-Z])'se",
		"'\\\\begingnuplot(\*?)\{(.*?)(?<!\\\\)\}(.*?)\\\\endgnuplot(?![a-zA-Z])'se",
		"'(\\\\bgroup([0-9]*)\n.*?\n\\\\egroup\\2)(?![0-9a-zA-Z])'se",
		"'\\\\sign(?:[ \t]+(.+))?[ \t]*$'me",
		"'\\\\n(?![a-zA-Z])'",
		"'\\\\p(?![a-zA-Z])'",
		"'\\\\[aA]uthor(?![a-zA-Z])'",
		"'\\\\[pP]agename(?![a-zA-Z])'",
		"'\\\\[vV]ersion(?![a-zA-Z])'",
		"'\\\\[tT]oday(?![a-zA-Z])'",
		"'\\\\[nN]ow(?![a-zA-Z])'",
		"'\\\\[tT]imestamp(?![a-zA-Z])'",
		"'\\\\[bB]time(?![a-zA-Z])'",
		"'\\\\(?:smalltoday|SmallToday)(?![a-zA-Z])'",
		"'\\\\(?:smallnow|SmallNow)(?![a-zA-Z])'",
		"'\\\\(?:ip|IP)(?![a-zA-Z])'",
		"'\\\\begin(left|center|right|justify)(?![a-zA-Z])[ \t]?'",
		"'\\\\end(?:left|center|right|justify)(?![a-zA-Z])'",
		"'\\\\(left|center|right|justify)(?![a-zA-Z])[ \t]?(.*)'",
		"'\\\\beginpostit([0-9]*%?)(#[0-9a-zA-Z]*(?![0-9a-zA-Z])|[a-zA-Z]*(?![a-zA-Z]))[ \t]?'e",
		"'\\\\endpostit(?![a-zA-Z])'",
		"'\\\\`'",
		"'\\\\[+^]'",
		"'(?<=^|[ \t]|\\\\\.)([^ \t\r\n]+?)\\\\\.'m",
		"'(?<=[^\r\n])\\\\[,.]|\\\\[,.](?=[^\r\n])'",
		"'$wikiXword'",
		"'(\\\\mafi\{.*?(?<!\\\\)\})'e",
		"'(\\\\IncludeFile\{.*?(?<!\\\\)\})'e",
		"'(\\\\SearchPages@?\{.*?(?<!\\\\)\})'e",
		"'(\\\\RecentChangesTo@?\*?\{.*?(?<!\\\\)\})'e",
		"'(\\\\RecentChangesFrom@?\*?\{.*?(?<!\\\\)\})'e",
		"'(\\\\MostPopularTo@?\{.*?(?<!\\\\)\})'e",
		"'(\\\\MostPopularFrom@?\{.*?(?<!\\\\)\})'e",
		"'(\\\\AllPagesTo@?\{.*?(?<!\\\\)\})'e",
		"'(\\\\AllPagesFrom@?\{.*?(?<!\\\\)\})'e",
		"'(\\\\RecentPagesTo@?\*?\{.*?(?<!\\\\)\})'e",
		"'(\\\\RecentPagesFrom@?\*?\{.*?(?<!\\\\)\})'e",
		"'(\\\\RandomPagesTo\{.*?(?<!\\\\)\})'e",
		"'(\\\\RandomPagesFrom\{.*?(?<!\\\\)\})'e",
		"'(\\\\RandomPageTo\{.*?(?<!\\\\)\})'e",
		"'(\\\\RandomPageFrom\{.*?(?<!\\\\)\})'e",
		"'(\\\\UploadedFiles\{.*?(?<!\\\\)\})'e",
		"'(\\\\LinkUp\{.*?(?<!\\\\)\})'e",
		"'(\\\\LinkDown\{.*?(?<!\\\\)\})'e",
		"'(\\\\Calendar@?\*?\{.*?(?<!\\\\)\}\{.*?(?<!\\\\)\})'e",
		"'(\\\\DisplayPage\{.*?(?<!\\\\)\})'e",
		"'(\\\\iphp\{.*?(?<!\\\\)\})'e",
		"'^\\\\([0-9]*)>'me",
		"'^\\\\([0-9]+)(\*|#|;)'me",
		"'^\\\\([0-9]+),$'me",
		"'&(?!#[0-9]+;)'",
		"'://host(?![.0-9a-zA-Z])'",
		"'://wikix(?![.0-9a-zA-Z])'",
		"'://myfile(?![.0-9a-zA-Z])'",
		"'://file(?![.0-9a-zA-Z])'",
		"'://self(?![.0-9a-zA-Z])'",
		"'://uri(?![.0-9a-zA-Z])'",
	);
	$replace = array(
		"('\\2'==''?j(\\4+0):j(\\1+0, '\\2', \\3+0))",
		"('\\2'==''?k(\\4+0):k(\\1+0, '\\2', \\3+0))",
		"",
		"\x07",
		"",
		"://",
		"\x07",
		"",
		"",
		$pagename0,
		$pageName,
		$pagenamE,
		$npages,
		$Y,
		$M,
		$D,
		$today,
		$timestamp,
		"('\\2'==''?i(\\4+0):i(\\1+0, '\\2', \\3+0))",
		"php(str_replace('\x03', '$bs$bs', stripslashes('\\1')))",
		"mafi(str_replace('\x03', '$bs$bs', stripslashes('\\2')))",
		"tex(\\2+0, '\\3', '\\4', '\\1')",
		"latex(\\2+0, '\\3', '\\4', '\\1')",
		"gnuplot('\\2', '\\3', '\\1')",
		"str_replace('\n', '\r\r', str_replace('$bs\"', '\"', '\\1'))",
		"'${bs}right -- ['.('\\1'==''?'$author':'\\1').'] ${bs}smallnow'",
		"<br class=\"br\" />",
#		"<p class=\"p\"></p>",
		"<p class=\"p\">",
		$author,
		$epagename,
		$wikiXversion,
		$today,
		$now,
		$timestamp,
		$btime,
		"<small class=\"small\">$today</small>",
		"<small class=\"small\">$now</small>",
		$ip,
		"<div align=\"\\1\">",
		"</div>",
		"<div align=\"\\1\">\\2\x05 \x06</div>",
		"'<sup class=\"postit\"><a href=\"JavaScript://\" onMouseDown=\"toggle(\'wikiXpostit'.(++\$npostits).'\');\">\\[P]</a><span id=\"wikiXpostit'.(\$npostits).'\" style=\"position:absolute; width:'.('\\1'==''?'300':'\\1').'px; height:0px; visibility:$postitvisibility; z-index:'.(100000000-\$npostits).';\"><table border=\"0px\" cellspacing=\"1px\" cellpadding=\"3px\"><tr><td bgcolor=\"'.('\\2'==''?'yellow':'\\2').'\">'",
		"</td></tr></table></span></sup>",
		"\x02",
		"",
		"[\\1]",
		"\x05 \x06",
		"\x10\\1\x11",
		"str_replace('\x10', '', str_replace('\x11', '', '\\1'))",
		"str_replace('\x10', '', str_replace('\x11', '', '\\1'))",
		"str_replace('\x10', '', str_replace('\x11', '', '\\1'))",
		"str_replace('\x10', '', str_replace('\x11', '', '\\1'))",
		"str_replace('\x10', '', str_replace('\x11', '', '\\1'))",
		"str_replace('\x10', '', str_replace('\x11', '', '\\1'))",
		"str_replace('\x10', '', str_replace('\x11', '', '\\1'))",
		"str_replace('\x10', '', str_replace('\x11', '', '\\1'))",
		"str_replace('\x10', '', str_replace('\x11', '', '\\1'))",
		"str_replace('\x10', '', str_replace('\x11', '', '\\1'))",
		"str_replace('\x10', '', str_replace('\x11', '', '\\1'))",
		"str_replace('\x10', '', str_replace('\x11', '', '\\1'))",
		"str_replace('\x10', '', str_replace('\x11', '', '\\1'))",
		"str_replace('\x10', '', str_replace('\x11', '', '\\1'))",
		"str_replace('\x10', '', str_replace('\x11', '', '\\1'))",
		"str_replace('\x10', '', str_replace('\x11', '', '\\1'))",
		"str_replace('\x10', '', str_replace('\x11', '', '\\1'))",
		"str_replace('\x10', '', str_replace('\x11', '', '\\1'))",
		"str_replace('\x10', '', str_replace('\x11', '', '\\1'))",
		"str_replace('\x10', '', str_replace('\x11', '', '\\1'))",
		"str_replace('\x10', '', str_replace('\x11', '', '\\1'))",
		"';'.(\\1+0>1?str_repeat(';', \\1-1):'').':'",
		"str_repeat('\\2', \\1)",
		"str_repeat('$bs,\n', \\1-1).'$bs,'",
		"&amp;",
		"://$host",
		"://$host$scriptdir",
		"://$host$scriptdir/myfile",
		"://$host$scriptdir/file",
		"://$host$script",
		"://$host$euri",
	);
	$content = preg_replace($pattern, $replace, $content);

	$content = str_replace("/\x06", "/", $content);
	$content = str_replace("<", "&lt;", $content);
	$content = str_replace(">", "&gt;", $content);

	$content = str_replace("\\~\n", "", $content);
	$content = str_replace("\n\\~", "", $content);
	$content = str_replace("\\~", "", $content);

	$content = geni_trim($content);

	$hastoc = 0;
	if($mode){
		if(preg_match("'\\\\TableOfContents(?:\{[0-9]*\})?[0-9]*".
					"(?![a-zA-Z])'", $content)){
			$hastoc = 1;
			$pattern = array(
				"'\\\\TableOfContents\{([0-9]*)\}([0-9]*)".
					"(?![a-zA-Z])'",
				"'\\\\TableOfContents([0-9]*)(?![a-zA-Z])'",
			);
			$replace = array(
				"\x04(\\2-\\1)\x04",
				"\x04(\\1)\x04",
			);
			$content = preg_replace($pattern, $replace, $content);
			ob_start();
		}
		mystageh($content);
	}

	if($once || $reset){
		$depth = 0;
		$DisplayPool = array();
		if($action != "doit")
			$DisplayPool[0] = $pagename0;
		$tableAttr = array(" class=\"table\"", "", "");
		$once = 0;
	}

	$displaypool = array();
	$line = explode("\n", $content); $nlines = count($line);
	$iclose = 0; $close = array();
	$link = array();
	$isblock = 0; $blankline = 0; $done = 0;
	$idircmd = 0; $dircmd = array();

	if($group){
		$dohtml = $_dohtml;
		$dobracket = $_dobracket;
		$donetlink = $_donetlink;
		$dointerwiki = $_dointerwiki;
		$dowikiword = $_dowikiword;
		$dotable = $_dotable;
		$dopre = $_dopre;
		$dolisting = $_dolisting;
		$domultilinelisting = $_domultilinelisting;
		$doheading = $_doheading;
		$dolinebreak = $_dolinebreak;
		$dofont = $_dofont;
		$dowhitespaces = $_dowhitespaces;
		$donbsp = $_donbsp;
		$domyplugin = $_domyplugin;
		$doplugin = $_doplugin;
		$dosubs = $_dosubs;
		$domysubs = $_domysubs;
		$dodot = $_dodot;
		$dop = $_dop;
		$dobr = $_dobr;

		$htmli = $_htmli;
		$htmlp = $_htmlp;
		$table = $_table;
		$ntdattrs = $_ntdattrs;
		$listingi = $_listingi;
		$listingp = $_listingp;
	}else{
		$dohtml = 1;
		$dobracket = 1;
		$donetlink = 1;
		$dointerwiki = 1;
		$dowikiword = 1;
		$dotable = 1;
		$dopre = 1;
		$dolisting = 1;
		$domultilinelisting = 0;
		$doheading = 1;
		$dolinebreak = 1;
		$dofont = 1;
		$dowhitespaces = 1;
		$donbsp = 0;
		$domyplugin = 1;
		$doplugin = 1;
		$dosubs = 1;
		$domysubs = 1;
		$dodot = 1;
		$dop = 1;
		$dobr = "";

		$htmli = 0;
		$htmlp = array();
		$table = $tableAttr;
		$ntdattrs = 1;
		$listingi = 0;
		$listingp = array();
	}

#echo "r6: ".runTime()."<br />";
	if($mode == 1 && !$group)
		echo "<p class=\"p\" style=\"margin:0px;\">\n";
	if($npostits && $mode){
		if($postitvisibility == "hidden")
			echo "<script language=\"JavaScript\">postit = 0;</script>\n";
		echo "<div align=\"right\"><sup class=\"postit\"><a href=\"JavaScript://\" onMouseDown=\"if(postit){for(i=1;i<=$npostits;i++)hide('wikiXpostit'+i);postit=0;}else{for(i=1;i<=$npostits;i++)show('wikiXpostit'+i);postit=1;}\">[P]</a></sup></div>";
	}
	$depth++;
	for($i=0; $i<$nlines; $i++){
		$iline = $line[$i];
		$cont = 0;
		switch($iline){
		case "":
			if($mode){
				$iline = $dobr;
				if(!$dop)
					$iline .= "\n";
				else{
					$blankline = ($blankline && !$iclose);
					for(; --$iclose>=0; ){
						if($close[$iclose] == 1)
							$iline .= table("", $isblock, $table, $ntattrs, $done);
						else
						if($close[$iclose] == 2)
							$iline .= pre("", $isblock, $done);
						else
						if($close[$iclose] == 3)
							$iline .= listing("", $listingi, $listingp, $done);
					}
					$iclose++;
					if(!$blankline){
#						$iline .= "</p>\n<p class=\"p\">\n";
						$iline .= "<p class=\"p\">\n";
						$blankline = 1;
					}
				}
				if($domysubs)
					$iline = mysubs($iline);
				echo $iline;
			}
			$cont = 1;
			break;
		case ",":
			if($dodot){
				if($mode){
					$iline = "";
					for(; --$iclose>=0; ){
						if($close[$iclose] == 1)
							$iline .= table("", $isblock, $table, $ntattrs, $done);
						else
						if($close[$iclose] == 2)
							$iline .= pre("", $isblock, $done);
						else
						if($close[$iclose] == 3)
							break;
					}
					$iclose++;
					$iline .= "<br class=\"br\" />\n";
					if($domysubs)
						$iline = mysubs($iline);
					echo $iline;
					$blankline = 0;
				}
				$cont = 1;
			}
			break;
		case ".":
			if($dodot){
				if($mode && !$blankline){
					$iline = "";
					for(; --$iclose>=0; ){
						if($close[$iclose] == 1)
							$iline .= table("", $isblock, $table, $ntattrs, $done);
						else
						if($close[$iclose] == 2)
							$iline .= pre("", $isblock, $done);
						else
						if($close[$iclose] == 3)
							break;
					}
					$iclose++;
#					$iline .= "<p class=\"p\"></p>\n";
					$iline .= "<p class=\"p\">\n";
					if($domysubs)
						$iline = mysubs($iline);
					echo $iline;
					$blankline = 1;
				}
				$cont = 1;
			}
			break;
		}
		if($cont)
			continue;
		$blankline = 0;
		if($iline[0] == "\\"){
		$cont = 0;
		switch($iline){
		case "\\,":
			if($mode){
				$iline = "";
				for(; --$iclose>=0; ){
					if($close[$iclose] == 1)
						$iline .= table("", $isblock, $table, $ntattrs, $done);
					else
					if($close[$iclose] == 2)
						$iline .= pre("", $isblock, $done);
					else
					if($close[$iclose] == 3)
						break;
				}
				$iclose++;
				$iline .= listing("", $listingi, $listingp, $done, 0);
				if($domysubs)
					$iline = mysubs($iline);
				echo $iline;
			}
			$cont = 1;
			break;
		case "\\.":
			if($mode){
				$iline = "";
				for(; --$iclose>=0; ){
					if($close[$iclose] == 1)
						$iline .= table("", $isblock, $table, $ntattrs, $done);
					else
					if($close[$iclose] == 2)
						$iline .= pre("", $isblock, $done);
					else
					if($close[$iclose] == 3)
						$iline .= listing("", $listingi, $listingp, $done);
				}
				$iclose++;
				if($domysubs)
					$iline = mysubs($iline);
				echo $iline;
			}
			$cont = 1;
			break;
		case "\\noDefault":
			$dohtml = 0;
			$dobracket = 0;
			$donetlink = 0;
			$dointerwiki = 0;
			$dowikiword = 0;
			$dotable = 0;
			$dopre = 0;
			$dolisting = 0;
			$domultilinelisting = 1;
			$doheading = 0;
			$dolinebreak = 0;
			$dofont = 0;
			$dowhitespaces = 0;
			$donbsp = 1;
			$domyplugin = 0;
			$doplugin = 0;
			$dosubs = 0;
			$domysubs = 0;
			$dodot = 0;
			$dop = 0;
			$dobr = "<br class=\"br\" />";
			$cont = 1;
			break;
		case "\\doDefault":
			$dohtml = 1;
			$dobracket = 1;
			$donetlink = 1;
			$dointerwiki = 1;
			$dowikiword = 1;
			$dotable = 1;
			$dopre = 1;
			$dolisting = 1;
			$domultilinelisting = 0;
			$doheading = 1;
			$dolinebreak = 1;
			$dofont = 1;
			$dowhitespaces = 1;
			$donbsp = 0;
			$domyplugin = 1;
			$doplugin = 1;
			$dosubs = 1;
			$domysubs = 1;
			$dodot = 1;
			$dop = 1;
			$dobr = "";
			$cont = 1;
			break;
		case "\\noAll":
			$dohtml = 0;
			$dobracket = 0;
			$donetlink = 0;
			$dointerwiki = 0;
			$dowikiword = 0;
			$dotable = 0;
			$dopre = 0;
			$dolisting = 0;
			$domultilinelisting = 0;
			$doheading = 0;
			$dolinebreak = 0;
			$dofont = 0;
			$dowhitespaces = 0;
			$donbsp = 0;
			$domyplugin = 0;
			$doplugin = 0;
			$dosubs = 0;
			$domysubs = 0;
			$dodot = 0;
			$dop = 0;
			$dobr = "";
			$cont = 1;
			break;
		case "\\doAll":
			$dohtml = 1;
			$dobracket = 1;
			$donetlink = 1;
			$dointerwiki = 1;
			$dowikiword = 1;
			$dotable = 1;
			$dopre = 1;
			$dolisting = 1;
			$domultilinelisting = 1;
			$doheading = 1;
			$dolinebreak = 1;
			$dofont = 1;
			$dowhitespaces = 1;
			$donbsp = 1;
			$domyplugin = 1;
			$doplugin = 1;
			$dosubs = 1;
			$domysubs = 1;
			$dodot = 1;
			$dop = 1;
			$dobr = "<br class=\"br\" />";
			$cont = 1;
			break;
		case "\\nohtml":
			$dohtml = 0;
			$cont = 1;
			break;
		case "\\dohtml":
			$dohtml = 1;
			$cont = 1;
			break;
		case "\\nobracket":
			$dobracket = 0;
			$cont = 1;
			break;
		case "\\dobracket":
			$dobracket = 1;
			$cont = 1;
			break;
		case "\\nonetlink":
			$donetlink = 0;
			$cont = 1;
			break;
		case "\\donetlink":
			$donetlink = 1;
			$cont = 1;
			break;
		case "\\nointerwiki":
			$dointerwiki = 0;
			$cont = 1;
			break;
		case "\\dointerwiki":
			$dointerwiki = 1;
			$cont = 1;
			break;
		case "\\nowikiword":
			$dowikiword = 0;
			$cont = 1;
			break;
		case "\\dowikiword":
			$dowikiword = 1;
			$cont = 1;
			break;
		case "\\notable":
			$dotable = 0;
			$cont = 1;
			break;
		case "\\dotable":
			$dotable = 1;
			$cont = 1;
			break;
		case "\\nopre":
			$dopre = 0;
			$cont = 1;
			break;
		case "\\dopre":
			$dopre = 1;
			$cont = 1;
			break;
		case "\\nolisting":
			$dolisting = 0;
			$cont = 1;
			break;
		case "\\dolisting":
			$dolisting = 1;
			$cont = 1;
			break;
		case "\\nomultilinelisting":
			$domultilinelisting = 0;
			$cont = 1;
			break;
		case "\\domultilinelisting":
			$domultilinelisting = 1;
			$cont = 1;
			break;
		case "\\noheading":
			$doheading = 0;
			$cont = 1;
			break;
		case "\\doheading":
			$doheading = 1;
			$cont = 1;
			break;
		case "\\nolinebreak":
			$dolinebreak = 0;
			$cont = 1;
			break;
		case "\\dolinebreak":
			$dolinebreak = 1;
			$cont = 1;
			break;
		case "\\nofont":
			$dofont = 0;
			$cont = 1;
			break;
		case "\\dofont":
			$dofont = 1;
			$cont = 1;
			break;
		case "\\nowhitespaces":
			$dowhitespaces = 0;
			$cont = 1;
			break;
		case "\\dowhitespaces":
			$dowhitespaces = 1;
			$cont = 1;
			break;
		case "\\nonbsp":
			$donbsp = 0;
			$cont = 1;
			break;
		case "\\donbsp":
			$donbsp = 1;
			$cont = 1;
			break;
		case "\\nomyplugin":
			$domyplugin = 0;
			$cont = 1;
			break;
		case "\\domyplugin":
			$domyplugin = 1;
			$cont = 1;
			break;
		case "\\noplugin":
			$doplugin = 0;
			$cont = 1;
			break;
		case "\\doplugin":
			$doplugin = 1;
			$cont = 1;
			break;
		case "\\nosubs":
			$dosubs = 0;
			$cont = 1;
			break;
		case "\\dosubs":
			$dosubs = 1;
			$cont = 1;
			break;
		case "\\nomysubs":
			$domysubs = 0;
			$cont = 1;
			break;
		case "\\domysubs":
			$domysubs = 1;
			$cont = 1;
			break;
		case "\\nodot":
			$dodot = 0;
			$cont = 1;
			break;
		case "\\dodot":
			$dodot = 1;
			$cont = 1;
			break;
		case "\\nop":
			$dop = 0;
			$cont = 1;
			break;
		case "\\dop":
			$dop = 1;
			$cont = 1;
			break;
		case "\\nobr":
			$dobr = "";
			$cont = 1;
			break;
		case "\\dobr":
			$dobr = "<br class=\"br\" />";
			$cont = 1;
			break;
		case "\\dcSave":
			$dircmd[$idircmd++] = array(
				'dohtml' => $dohtml,
				'dobracket' => $dobracket,
				'donetlink' => $donetlink,
				'dointerwiki' => $dointerwiki,
				'dowikiword' => $dowikiword,
				'dotable' => $dotable,
				'dopre' => $dopre,
				'dolisting' => $dolisting,
				'domultilinelisting' => $domultilinelisting,
				'doheading' => $doheading,
				'dolinebreak' => $dolinebreak,
				'dofont' => $dofont,
				'dowhitespaces' => $dowhitespaces,
				'donbsp' => $donbsp,
				'domyplugin' => $domyplugin,
				'doplugin' => $doplugin,
				'dosubs' => $dosubs,
				'domysubs' => $domysubs,
				'dodot' => $dodot,
				'dop' => $dop,
				'dobr' => $dobr,
			);
			$cont = 1;
			break;
		case "\\dcRestore":
			if($idircmd > 0){
				$idircmd--;
				$dohtml = $dircmd[$idircmd]['dohtml'];
				$dobracket = $dircmd[$idircmd]['dobracket'];
				$donetlink = $dircmd[$idircmd]['donetlink'];
				$dointerwiki = $dircmd[$idircmd]['dointerwiki'];
				$dowikiword = $dircmd[$idircmd]['dowikiword'];
				$dotable = $dircmd[$idircmd]['dotable'];
				$dopre = $dircmd[$idircmd]['dopre'];
				$dolisting = $dircmd[$idircmd]['dolisting'];
				$domultilinelisting = $dircmd[$idircmd]['domultilinelisting'];
				$doheading = $dircmd[$idircmd]['doheading'];
				$dolinebreak = $dircmd[$idircmd]['dolinebreak'];
				$dofont = $dircmd[$idircmd]['dofont'];
				$dowhitespaces = $dircmd[$idircmd]['dowhitespaces'];
				$donbsp = $dircmd[$idircmd]['donbsp'];
				$domyplugin = $dircmd[$idircmd]['domyplugin'];
				$doplugin = $dircmd[$idircmd]['doplugin'];
				$dosubs = $dircmd[$idircmd]['dosubs'];
				$domysubs = $dircmd[$idircmd]['domysubs'];
				$dodot = $dircmd[$idircmd]['dodot'];
				$dop = $dircmd[$idircmd]['dop'];
				$dobr = $dircmd[$idircmd]['dobr'];
			}
			$cont = 1;
			break;
		case "\\dcReset":
			$idircmd = 0;
			$dircmd = array();
			$cont = 1;
			break;
		default:
			if(preg_match("/^\\\\dcSave:(.+)$/", $iline, $m)){
				$dircmd[$m[1]] = array(
					'dohtml' => $dohtml,
					'dobracket' => $dobracket,
					'donetlink' => $donetlink,
					'dointerwiki' => $dointerwiki,
					'dowikiword' => $dowikiword,
					'dotable' => $dotable,
					'dopre' => $dopre,
					'dolisting' => $dolisting,
					'domultilinelisting' => $domultilinelisting,
					'doheading' => $doheading,
					'dolinebreak' => $dolinebreak,
					'dofont' => $dofont,
					'dowhitespaces' => $dowhitespaces,
					'donbsp' => $donbsp,
					'domyplugin' => $domyplugin,
					'doplugin' => $doplugin,
					'dosubs' => $dosubs,
					'domysubs' => $domysubs,
					'dodot' => $dodot,
					'dop' => $dop,
					'dobr' => $dobr,
				);
				$cont = 1;
			}else
			if(preg_match("/^\\\\dcRestore:(.+)$/", $iline, $m)){
				if(isset($dircmd[$m[1]])){
					$dohtml = $dircmd[$m[1]]['dohtml'];
					$dobracket = $dircmd[$m[1]]['dobracket'];
					$donetlink = $dircmd[$m[1]]['donetlink'];
					$dointerwiki = $dircmd[$m[1]]['dointerwiki'];
					$dowikiword = $dircmd[$m[1]]['dowikiword'];
					$dotable = $dircmd[$m[1]]['dotable'];
					$dopre = $dircmd[$m[1]]['dopre'];
					$dolisting = $dircmd[$m[1]]['dolisting'];
					$domultilinelisting = $dircmd[$m[1]]['domultilinelisting'];
					$doheading = $dircmd[$m[1]]['doheading'];
					$dolinebreak = $dircmd[$m[1]]['dolinebreak'];
					$dofont = $dircmd[$m[1]]['dofont'];
					$dowhitespaces = $dircmd[$m[1]]['dowhitespaces'];
					$donbsp = $dircmd[$m[1]]['donbsp'];
					$domyplugin = $dircmd[$m[1]]['domyplugin'];
					$doplugin = $dircmd[$m[1]]['doplugin'];
					$dosubs = $dircmd[$m[1]]['dosubs'];
					$domysubs = $dircmd[$m[1]]['domysubs'];
					$dodot = $dircmd[$m[1]]['dodot'];
					$dop = $dircmd[$m[1]]['dop'];
					$dobr = $dircmd[$m[1]]['dobr'];
				}
				$cont = 1;
			}else
			if(preg_match("/^\\\\dcReset:(.+)$/", $iline, $m)){
				if(isset($dircmd[$m[1]]))
					unset($dircmd[$m[1]]);
				$cont = 1;
			}
			break;
		}
		if($cont)
			continue;
		}
		if(!$domultilinelisting && $iclose > 0 && $close[0] == 3 &&
				strpos("*#;", $iline[0]) === false){
			if($mode){
				$endblock = "";
				for(; --$iclose>=0; ){
					if($close[$iclose] == 1)
						$endblock .= table("", $isblock, $table, $ntattrs, $done);
					else
					if($close[$iclose] == 2)
						$endblock .= pre("", $isblock, $done);
					else
					if($close[$iclose] == 3)
						$endblock .= listing("", $listingi, $listingp, $done);
				}
				$iclose++;
				if($domysubs)
					$endblock = mysubs($endblock);
				echo $endblock;
			}
		}
		if(($n = preg_match_all("'\\\\bgroup([0-9]*)\r\r(.*?)\r\r".
			"\\\\egroup\\1(?![0-9a-zA-Z])'", $iline, $m))){
			if($n > 1){
				$m[2] = array_values(array_unique($m[2]));
				$n = count($m[2]);
			}
			for($j=0; $j<$n; $j++){
				$_dohtml = $dohtml;
				$_dobracket = $dobracket;
				$_donetlink = $donetlink;
				$_dointerwiki = $dointerwiki;
				$_dowikiword = $dowikiword;
				$_dotable = $dotable;
				$_dopre = $dopre;
				$_dolisting = $dolisting;
				$_domultilinelisting = $domultilinelisting;
				$_doheading = $doheading;
				$_dolinebreak = $dolinebreak;
				$_dofont = $dofont;
				$_dowhitespaces = $dowhitespaces;
				$_donbsp = $donbsp;
				$_domyplugin = $domyplugin;
				$_doplugin = $doplugin;
				$_dosubs = $dosubs;
				$_domysubs = $domysubs;
				$_dodot = $dodot;
				$_dop = $dop;
				$_dobr = $dobr;
				$_htmli = 0;
				$_htmlp = array();
				$_table = $table;
				$_ntdattrs = $ntdattrs;
				$_listingi = 0;
				$_listingp = array();
				$c = str_replace("\r\r", "\n", $m[2][$j]);
				$c = str_replace("&lt;", "<", $c);
				$c = str_replace("&gt;", ">", $c);
				$c = str_replace("&amp;", "&", $c);
				ob_start();
				DisplayContent($c, 1, 1);
				$r = ob_get_contents();
				ob_end_clean();
				$r = " \x06".escape_html(escape_bracket(
					escape_misc(str_replace("!", "\x02!",
					str_replace("\n", "\r\r",
					str_replace("\\", "\x03",
					substr($r, 0, -1)))))))." \x06";
				$iline = preg_replace("\x01\\\\bgroup([0-9]*)\r\r".preg_quote($m[2][$j])."\r\r\\\\egroup\\1(?![0-9a-zA-Z])\x01", $r, $iline);
			}
			$iline = preg_replace("/^ \x06/", "", $iline);
		}

		if($dohtml)
			$iline = html($iline, $htmli, $htmlp);
		if($dobracket)
			$iline = bracket($iline, $link, $dointerwiki);
		if($donetlink)
			$iline = netlink($iline);
		if($dointerwiki)
			$iline = interwiki($iline);
		if($dowikiword)
			$iline = wikiword($iline, $link);

		if($mode){
			if($dowhitespaces)
				$iline = whitespaces($iline);
			if($donbsp)
				$iline = nbsp($iline);
			if($dofont)
				$iline = font($iline);
			if($doheading)
				$iline = heading($iline, $hastoc);
			if($dolinebreak)
				$iline = linebreak($iline);
			$done = 0;
			$endblock = "";
			if($dotable || $close[$iclose-1] == 1){
				if($dotable){
					$endblock .= table($iline, $isblock, $table, $ntattrs, $done);
					if($done){
						if($endblock == "")
							continue;
						if($done == 3)
							$iclose--;
						if($done > 1)
							$close[$iclose++] = 1;
					}else
					if($endblock != "")
						$iclose--;
				}else{
					$endblock .= table("", $isblock, $table, $ntattrs, $done);
					$iclose--;
				}
			}
			if(($dopre || $close[$iclose-1] == 2) && !$done){
				if($dopre){
					$endblock0 = pre($iline, $isblock, $done);
					if($done){
						if($done > 1)
							$close[$iclose++] = 2;
					}else
					if($endblock0 != "")
						$iclose--;
				}else{
					$endblock0 = pre("", $isblock, $done);
					$iclose--;
				}
				$endblock .= $endblock0;
			}
			if($dolisting && !$done){
				$endblock .= listing($iline, $listingi, $listingp, $done);
				if($done)
					$close[$iclose++] = 3;
			}
			if($done){
				$iline = $endblock;
				$endblock = "";
			}
			if($domyplugin)
				$iline = myplugin($iline);
			if($doplugin)
				$iline = plugin($iline, $DisplayPool, $displaypool);
			if($dosubs)
				$iline = subs($iline);

			$iline = str_replace("\\", "", $iline);
			$iline = str_replace("\x02", "", $iline);
			$iline = str_replace("\x05", "", $iline);
			$iline = str_replace(" \x06", "", $iline);
			$iline = str_replace("&nbsp;\x06", "", $iline);
			$iline = str_replace("\x06", "", $iline);
			$iline = str_replace("\x10", "", $iline);
			$iline = str_replace("\x11", "", $iline);
			$iline = str_replace("\x03", "\\", $iline);
			$iline = str_replace("\r\r", "\n", $iline);
			$br = $dobr;
			if($domysubs){
				if($endblock != "")
					$endblock = mysubs($endblock);
				$iline = mysubs($iline);
				if($br != "")
					$br = mysubs($br);
			}
			if(substr($iline, -1) == "\n")
				$iline = substr($iline, 0, -1);
			echo "$endblock$iline$br\n";
		}
	}
	$depth--;
	if($mode){
		$iline = "";
		for(; --$iclose>=0; ){
			if($close[$iclose] == 1)
				$iline .= table("", $isblock, $table, $ntattrs, $done);
			else
			if($close[$iclose] == 2)
				$iline .= pre("", $isblock, $done);
			else
			if($close[$iclose] == 3)
				$iline .= listing("", $listingi, $listingp, $done);
		}
		if(!$depth)
			$iline .= html("", $htmli, $htmlp);
#		if($mode == 1)
#			$iline .= "</p>\n";
#			$iline .= "<p class=\"p\">\n";

		if($domysubs)
			$iline = mysubs($iline);
		echo $iline;

		$DisplayPool = array_values(array_unique(array_merge($DisplayPool, $displaypool)));

		mystagef($content);

		if($hastoc){
			$page = ob_get_contents();
			ob_end_clean();
			echo replace_toc($page);
		}
	}

#echo "<br />r7: ".runTime()."<br />";
	return $link;
}

function i($var, $op = "", $val = 0){
	static	$wikiXi = array(), $d = array(), $D = array();

	$ret = "";
	switch($op){
	case "":
		$ret = $wikiXi[$var] + 0;
		$D[$var] = (isset($d[$var])?$d[$var]:1);
		$wikiXi[$var] += $D[$var];
		break;
	case "=":
		$wikiXi[$var] = $val;
		$D[$var] = 0;
		break;
	default:
		if($val < 0){
			$op = ($op=="+"?"-":"+");
			$val = -$val;
		}
		$d[$var] = "$op$val";
		$wikiXi[$var] += $d[$var] - $D[$var];
		$D[$var] = $d[$var];
		break;
	}

	return $ret;
}

function j($var, $op = "", $val = 0){
	static	$wikiXj = array(), $d = array(), $D = array();

	$ret = "";
	switch($op){
	case "":
		$ret = $wikiXj[$var] + 0;
		$D[$var] = (isset($d[$var])?$d[$var]:1);
		$wikiXj[$var] += $D[$var];
		break;
	case "=":
		$wikiXj[$var] = $val;
		$D[$var] = 0;
		break;
	default:
		if($val < 0){
			$op = ($op=="+"?"-":"+");
			$val = -$val;
		}
		$d[$var] = "$op$val";
		$wikiXj[$var] += $d[$var] - $D[$var];
		$D[$var] = $d[$var];
		break;
	}

	return $ret;
}

function k($var, $op = "", $val = 0){
	static	$wikiXk = array(), $d = array(), $D = array();

	$ret = "";
	switch($op){
	case "":
		$ret = $wikiXk[$var] + 0;
		$D[$var] = (isset($d[$var])?$d[$var]:1);
		$wikiXk[$var] += $D[$var];
		break;
	case "=":
		$wikiXk[$var] = $val;
		$D[$var] = 0;
		break;
	default:
		if($val < 0){
			$op = ($op=="+"?"-":"+");
			$val = -$val;
		}
		$d[$var] = "$op$val";
		$wikiXk[$var] += $d[$var] - $D[$var];
		$D[$var] = $d[$var];
		break;
	}

	return $ret;
}

function whitespaces($str){
	if(strpos($str, "\\ ") === false && strpos($str, "\\\t") === false)
		return $str;
	if(!($n = preg_match_all("/\\\\([ \t]+)/", $str, $m)))
		return $str;

	if($n > 1){
		$m[1] = array_values(array_unique($m[1]));
		$n = count($m[1]);
	}

	for($i=$n-1; $i>=0; $i--){
		$r = "";
		$l = strlen($m[1][$i]);
		for($j=0; $j<$l; $j++){
			if($m[1][$i][$j] == " ")
				$r .= "&nbsp;";
			else
				$r .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		}
		$str = str_replace("\\".$m[1][$i], $r, $str);
	}
	return $str;
}

function nbsp($str){
	global	$bs;

	if(strpos($str, " ") === false && strpos($str, "\t") === false)
		return $str;
	$str = preg_replace("/(<[^>]+>)/e",
			"str_replace(' ', '\x07', str_replace('\t', '\x08',
			str_replace('$bs\"', '\"', '\\1')))", $str);
	$str = str_replace(" ", "&nbsp;", str_replace("\t", "        ", $str));
	$str = str_replace("\x07", " ", str_replace("\x08", "\t", $str));

	return $str;
}

function heading($str, $hastoc){
	static	$nheadings = array();

	if($str == "" || $str[0] != "!")
		return $str;

	preg_match("/^(!+)([0-9]*)(&lt;|\^|&gt;)?(\x05(?: |&nbsp;)\x06)?/",
			$str, $m);

	$pattern = $m[1];
	$h = strlen($m[1]);
	$h = "h".($h>6?6:$h);

	$pattern .= $m[2];

	$align = "";
	switch($m[3]){
	case "&lt;":
		$pattern .= "&lt;";
		$align = " align=\"left\"";
		break;
	case "^":
		$pattern .= "\^";
		$align = " align=\"center\"";
		break;
	case "&gt;":
		$pattern .= "&gt;";
		$align = " align=\"right\"";
		break;
	}

	$pattern .= $m[4];
	$top = "";
	if($hastoc && $m[4] == "")
		$top = "<a name=\"wikiXheading$m[2]_".(++$nheadings[$m[2]]).
		"\" class=\"totoc0\" href=\"#wikiXtoc$m[2]_\">&nbsp;</a>".
		"<a class=\"totoc\" href=\"#wikiXtoc\">&nbsp;</a>";

	$str = preg_replace("/^${pattern}[ \t]*(.*?)[ \t]*$/",
			"<$h class=\"heading\"$align>\\1$top</$h>", $str);

	# force to input a blankline immediately before and after a heading
	# regardless of the css file?
#	$str = "</p>\n$str\n<p class=\"p\">";

	return $str;
}

function linebreak($str){
	if(strpos($str, "%%%") === false && strpos($str, "---") === false)
		return $str;

	$pattern = array(
		"/(?<![\x02\\\\%])%%%+/",
		"/(?<![\x02\\\\-])----+([0-9]+%?)-+(&lt;|\^|&gt;)?/e",
		"/(?<![\x02\\\\-])----+(&lt;|\^|&gt;)?/e",
		"/(?<![\x02\\\\-])---([0-9]+%?)-+(&lt;|\^|&gt;)?/e",
		"/(?<![\x02\\\\-])---(&lt;|\^|&gt;)?/e",
	);
	$replace = array(
		"<br class=\"br\" />",
		"'<hr class=\"hr\" '.
			str_replace('&lt;', 'align=\"left\" ',
			str_replace('^', 'align=\"center\" ',
			str_replace('&gt;', 'align=\"right\" ',
			'\\2'))).'width=\"\\1\" />'",
		"'<hr class=\"hr\" '.
			str_replace('&lt;', 'align=\"left\" ',
			str_replace('^', 'align=\"center\" ',
			str_replace('&gt;', 'align=\"right\" ',
			'\\1'))).'/>'",
		"'<hr class=\"hr0\" '.
			str_replace('&lt;', 'align=\"left\" ',
			str_replace('^', 'align=\"center\" ',
			str_replace('&gt;', 'align=\"right\" ',
			'\\2'))).'size=\"0px\" width=\"\\1\" />'",
		"'<hr class=\"hr0\" '.
			str_replace('&lt;', 'align=\"left\" ',
			str_replace('^', 'align=\"center\" ',
			str_replace('&gt;', 'align=\"right\" ',
			'\\1'))).'size=\"0px\" />'",
	);
	$str = preg_replace($pattern, $replace, $str);

	return $str;
}

function font($str){
	if(strpos($str, "''") === false && strpos($str, "__") === false)
		return $str;

	$pattern = array(
		"/(?<![\x02\\\\'])''+(.*?[^\\\\\x02'])''+/e",
		"/(?<![\x02\\\\_])__+(.*?[^\\\\\x02_])__+/e",
	);
	$replace = array(
		"'<i class=\"i\">'.
			str_replace(':', '\x02:',
			str_replace('|', '\x02|', '\\1')).'</i>'",
		"'<b class=\"b\">'.
			str_replace(':', '\x02:',
			str_replace('|', '\x02|', '\\1')).'</b>'",
	);
	$str = preg_replace($pattern, $replace, $str);

	return $str;
}

function html($str, &$ipair, &$pair){
	global	$htmlDtag, $htmlStag, $netLink, $bs;

	if($htmlDtag == "" && $htmlStag == "")
		return $str;

	if($str != ""){
		if(strpos($str, "&lt;") === false ||
		   strpos($str, "&gt;") === false)
			return $str;
#php403:		$str = preg_replace("'(?<![\x02\\\\])&lt;(/?(?:$htmlDtag))(?<![\x02\\\\])&gt;'i", "&lt;\\1 &gt;", $str);
		$n = preg_match_all("'(?<![\x02\\\\])&lt;(/?(?:$htmlDtag))([ \t]+.*?)?(?<![\x02\\\\])&gt;'i", $str, $m);
		for($i=0; $i<$n; $i++){
			$str = preg_replace("\x01(?<![\x02\\\\])&lt;(".preg_quote($m[1][$i].$m[2][$i]).")&gt;\x01", "<\\1>", $str);
			$m[1][$i] = strtolower($m[1][$i]);
			if($m[1][$i][0] == "/"){
				if($ipair > 0 &&
				   $pair[$ipair-1] == $m[1][$i]){
					if($m[1][$i] == "/a"){
						$str = preg_replace(
							"\x01<(a[ \t]+.*?)>(.*?)<(".preg_quote($m[1][$i].$m[2][$i]).")>\x01ie",
							"'<\\1>'.
							str_replace(':', '\x02:',
							str_replace('|', '$bs|',
							str_replace('[', '${bs}[',
							str_replace(']', '$bs]',
							str_replace('\x10', '',
							str_replace('\x11', '',
							'\\2')))))).
							'<\\3>'",
							$str);
					}
					$ipair--;
#					array_splice($pair, $ipair);
				}
			}else
				$pair[$ipair++] = "/".$m[1][$i];
		}
#Php403:		$str = preg_replace("'(?<![\x02\\\\])&lt;($htmlStag)(?<![\x02\\\\])&gt;'i", "&lt;\\1 &gt;", $str);
		$n = preg_match_all("'(?<![\x02\\\\])&lt;((?:$htmlStag)(?:[/ \t]+.*?)?)(?<![\x02\\\\])&gt;'i", $str, $m);
		$m[1] = array_values(array_unique($m[1]));
		$n = count($m[1]);
		for($i=0; $i<$n; $i++)
			$str = preg_replace(
				"\x01(?<![\x02\\\\])&lt;(".
				preg_quote($m[1][$i]).")&gt;\x01",
				"<\\1>", $str);
		$str = preg_replace("'<([^>]+)>'e",
			"escape_bracket(escape_misc(
			str_replace('\x10', '', str_replace('\x11', '',
			str_replace('$bs\"', '\"', '<\\1>')))))", $str);
	}else
	if($ipair > 0){
		for(; --$ipair>=0; )
			$str .= "<$pair[$ipair]>\n";
	}
	if($ipair < 0)
		$ipair = 0;
	return $str;
}

function bracket($str, &$link, $dointerwiki){
	global	$netLink, $imgExt;

	if(strpos($str, "[") === false || strpos($str, "]") === false)
		return $str;

	$str = preg_replace("/(?<![\x02\\\\])\[[ \t]*(?<![\x02\\\\])\]/",
			"", $str);
	if(!($n = preg_match_all("/(?<![\x02\\\\])\[(.+?)(?<![\x02\\\\])\]/",
			$str, $m)))
		return $str;

	if($n > 1){
		$m[1] = array_values(array_unique($m[1]));
		$n = count($m[1]);
	}

	for($i=0; $i<$n; $i++){
		$pagename = $m[1][$i];
		$p = trim($pagename);
		if($p[strlen($p)-1] == "\\")
			$pagename = ltrim($pagename);
		else
			$pagename = $p;
		$pagename = str_replace("\x10", "", $pagename);
		$pagename = str_replace("\x11", "", $pagename);
		if(preg_match("/^(.+?)([ \t]*)(?<![\x02\\\\])\|[ \t]*(.+?)$/",
				$pagename, $_m)){
			if($_m[1][strlen($_m[1])-1] == "\\")
				$linkname = $_m[1].$_m[2];
			else
				$linkname = $_m[1];
			$pagename = $_m[3];
		}else
			$linkname = $pagename;
		if($dointerwiki){
			$l = str_replace("\t", "%09",
					str_replace(" ", "%20", $linkname));
			$url = interwiki($l);
			if($url !== $l && preg_match("'<img class=\"interwiki\" src=\"(.*?)\" .*? />'", $url, $_m))
				$linkname = str_replace("\x02:", ":", $_m[1]);
		}
		$linkimg = preg_match("'^(?:$netLink).*?\.(?:$imgExt)$'i",
				$linkname);
		$linkname = str_replace("\x05 \x06", "", $linkname);
		$linkname = stripslashes($linkname);
		$linkname = geni_whitespaces($linkname);

		if(preg_match("'^(?:$netLink)'i", $pagename)){
			$pageimg = preg_match("/\.(?:$imgExt)$/i", $pagename);
			$pagename = stripslashes($pagename);
			$pagename = str_replace('"', '&quot;', $pagename);
			if($linkimg){
				$linkname = str_replace('"', '&quot;', $linkname);
				$r = "<a href=\"$pagename\"><img class=\"netlink\" src=\"$linkname\" alt=\"$pagename\" /></a>";
			}else
			if($pageimg){
				$linkname = str_replace('"', '&quot;', $linkname);
				$r = "<img class=\"netlink\" src=\"$pagename\" alt=\"$linkname\" />";
			}else{
				$linkname = preg_replace("/^mailto:/i", "", $linkname);
				$r = "<a class=\"netlink\" href=\"$pagename\">$linkname</a>";
			}
		}else{
			$url = $p = str_replace("\t", "%09", str_replace(" ", "%20", $pagename));
			if($dointerwiki)
				$url = interwiki($p);
			if($url === $p){
				$pagename = clean4bracket($pagename);
				$pagename = stripslashes($pagename);
				$pagename = geni_unspecialchars($pagename);
				$pagename = str_replace("\x03", "\\", $pagename);

				$Pagename = addslashes($pagename);
				if(($id = pageid($Pagename))){
					$Pagename = geni_urlencode($pagename);
					if($linkimg){
						$linkname = str_replace('"', '&quot;', $linkname);
						$pagename = str_replace('"', '&quot;', $pagename);
						$r = "<a href=\"index.php?display=$Pagename\"><img class=\"wikiword_display\" src=\"$linkname\" alt=\"$pagename\" /></a>";
					}else
						$r = "<a class=\"wikiword_display\" href=\"index.php?display=$Pagename\">$linkname</a>";
					$link[] = $id;
				}else{
					if($linkimg){
						$Pagename = geni_urlencode($pagename);
						$linkname = str_replace('"', '&quot;', $linkname);
						$pagename = str_replace('"', '&quot;', $pagename);
						$r = "<a href=\"index.php?goto=$Pagename\"><img class=\"wikiword_goto\" src=\"$linkname\" alt=\"$pagename\" /></a>";
					}else{
						$linkname = clean4bracket($linkname);
						$linkname = geni_unspecialchars($linkname);
						$w = split_word($linkname);
						$w[0] = geni_specialchars($w[0]);
						$w[1] = geni_specialchars($w[1]);
						$Pagename = geni_urlencode($pagename);
						$r = "<a class=\"wikiword_goto\" href=\"index.php?goto=$Pagename\">$w[0]</a>$w[1]";
					}
					$link[] = "-$pagename";
				}
			}else{
				$pageimg = (substr($url, 0, 5)=="<img "?1:0);
				if($linkimg){
					$linkname = str_replace('"', '&quot;', $linkname);
					$pagename = str_replace('"', '&quot;', $pagename);
					if($pageimg)
						$r = preg_replace(
							"'<img class=\"interwiki\" src=\"(.*?)\" .*? />'",
							"<a href=\"\\1\"><img class=\"interwiki\" src=\"$linkname\" alt=\"$pagename\" /></a>", $url);
					else
						$r = preg_replace(
							"'<a class=\"interwiki\" href=\"(.*?)\" .*?>.+?</a>'",
							"<a href=\"\\1\"><img class=\"interwiki\" src=\"$linkname\" alt=\"$pagename\" /></a>", $url);
				}else{
					if($pageimg){
						$linkname = str_replace('"', '&quot;', $linkname);
						$r = preg_replace(
							"'(<img class=\"interwiki\" src=\".*?\" alt=).*? />'",
							"\\1\"$linkname\" />", $url);
					}else{
						$pagename = str_replace('"', '&quot;', $pagename);
						$r = preg_replace(
							"'(<a class=\"interwiki\" href=\".*?\" title=).*?>.+?</a>'",
							"\\1\"$pagename\">$linkname</a>", $url);
					}
				}
			}
		}
		$r = escape_misc($r);
		$str = preg_replace("\x01(?<![\x02\\\\])\[".preg_quote($m[1][$i])."\]\x01", $r, $str);
	}
	return $str;
}

function netlink($str){
	global	$netLink, $imgExt;

	if(strpos($str, ":") === false)
		return $str;
	if(!($n = preg_match_all(
		"'(?<![\x02\\\\a-z])((?:$netLink)[^\x05 \t]+)'i", $str, $m)))
		return $str;

	if($n > 1){
		$m[1] = array_values(array_unique($m[1]));
		$n = count($m[1]);
	}

	for($i=0; $i<$n; $i++){
		$pagename = $m[1][$i];
		$pagename = str_replace("\x10", "", $pagename);
		$pagename = str_replace("\x11", "", $pagename);
#		$pagename = stripslashes($pagename);
		$linkname = $pagename;
		$pagename = str_replace('"', '&quot;', $pagename);
		if(preg_match("/\.(?:$imgExt)$/i", $pagename)){
			$linkname = str_replace('"', '&quot;', $linkname);
			$r = "<img class=\"netlink\" src=\"$pagename\" alt=\"$linkname\" />";
		}else{
			$linkname = preg_replace("/^mailto:/i", "", $linkname);
			$r = "<a class=\"netlink\" href=\"$pagename\">$linkname</a>";
		}
		$r = escape_misc($r);
		$str = preg_replace("\x01(?<![\x02\\\\])".preg_quote($m[1][$i]).
				"(?=[\x05 \t]|$)\x01", $r, $str);
	}
	return $str;
}

function interwiki($str){
	global	$interWikiMap, $Interwikimap0, $InterWikimap, $netLink, $imgExt;
	static	$once = 1, $interWiki;

	if(strpos($str, ":") === false)
		return $str;
	if($once){
		$data = "";
		$interWiki['@count'] = 0;
		if($InterWikimap != "" && ($id = pageid($InterWikimap))){
			$data .= page_content($id, "page.version")."\n";
			$data = include_page($Interwikimap0, $data);
		}
		if(file_exists($interWikiMap)){
			$fp = fopen($interWikiMap, "r");
			$data .= fread($fp, filesize($interWikiMap));
			fclose($fp);
		}
		if($data != ""){
			$ninterWikis = preg_match_all(
				"/^([0-9a-zA-Z\x80-\xff]+)[ \t]+([^ \t\n]+)/m",
				$data, $m) - 1;
			$interWiki['@match'] =
				"/(?<![\x02\\\\0-9a-zA-Z\x80-\xff])(\x10?(?:";
			for($i=0; $i<$ninterWikis; $i++){
				$interWiki['@match'] .= $m[1][$i]."|";
				$interWiki[$m[1][$i]] =
					str_replace("\\`", "\x02",
					str_replace("\\\\", "\x03", $m[2][$i]));
			}
			$interWiki['@match'] .= $m[1][$i];
			$interWiki[$m[1][$i]] = str_replace("\\`", "\x02",
					str_replace("\\\\", "\x03", $m[2][$i]));
			$interWiki['@match'] .= ")\x11?:[^ \t\n]+)/";
			$interWiki['@count'] = $ninterWikis + 1;
		}
		$once = 0;
	}

	if($interWiki['@count'] &&
	   ($n = preg_match_all($interWiki['@match'], $str, $m))){
	   	if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}
		for($i=0; $i<$n; $i++){
			$p = strpos($m[1][$i], ":");
			$wikiname = substr($m[1][$i], 0, $p);
			$pagename = substr($m[1][$i], $p+1);

			$wikiname = str_replace("\x10", "", $wikiname);
			$wikiname = str_replace("\x11", "", $wikiname);
			$wikiname = stripslashes($wikiname);
			$wikiurl = $interWiki[$wikiname];

			$pagename = str_replace("\x10", "", $pagename);
			$pagename = str_replace("\x11", "", $pagename);
			$pagename = stripslashes($pagename);
			$Pagename = str_replace('"', '&quot;', $pagename);

			if($Pagename == "\x05" || $Pagename == "\x05%20\x06"){
				$wikiurl = str_replace("/index.php?display=",
						"/", $wikiurl);
				$pagename = $wikiname;
				$Pagename = "";
			}
			if(strpos($wikiurl, "%s") === false)
				$url = $wikiurl.$Pagename;
			else
				$url = str_replace("%s", $Pagename, $wikiurl);
			if(preg_match("'^(?:$netLink).*?\.(?:$imgExt)$'i", $url))
				$r = "<img class=\"interwiki\" src=\"$url\" alt=\"$wikiname:$Pagename\" />";
			else
				$r = "<a class=\"interwiki\" href=\"$url\" title=\"$wikiname:$Pagename\">$pagename</a>";
			$r = escape_misc($r);
			$str = preg_replace(
				"\x01(?<![\x02\\\\0-9a-zA-Z\x80-\xff])".
				preg_quote($m[1][$i])."(?![^ \t\n])\x01",
				$r, $str);
		}
	}
	return $str;
}

function wikiword($str, &$link){
	global	$wikiXword;

	if(strpos($str, "\x10") === false)
		return $str;
	if(!($n=preg_match_all("'\x10$wikiXword\x11'", $str, $m)))
		return $str;

	if($n > 1){
		$m[1] = array_values(array_unique($m[1]));
		$n = count($m[1]);
	}

	for($i=0; $i<$n; $i++){
		$Pagename = addslashes($m[1][$i]);
		if(($id = pageid($Pagename))){
			$str = preg_replace("\x01\x10(?<![\\\\0-9a-zA-Z])".preg_quote($m[1][$i])."(?![0-9a-zA-Z])\x11\x01", "<a class=\"wikiword_display\" href=\"index.php?display=".$m[1][$i]."\">".$m[1][$i]."</a>", $str);
			$link[] = $id;
		}else{
			$w = split_word($m[1][$i]);
			$str = preg_replace("\x01\x10(?<![\\\\0-9a-zA-Z])".preg_quote($m[1][$i])."(?![0-9a-zA-Z])\x11\x01", "<a class=\"wikiword_goto\" href=\"index.php?goto=".$m[1][$i]."\">$w[0]</a>$w[1]", $str);
			$link[] = "-".$m[1][$i];
		}
	}
	return $str;
}

function listing($str, &$ipair, &$pair, &$done, $closeall = 1){
	$done = 0;
	if($str == ""){
		if($closeall){
			for(; --$ipair>=0; ){
				if(substr($pair[$ipair], -1) == "-"){
					$pair[$ipair] = substr($pair[$ipair], 0, 2);
					if($pair[$ipair] == "dl")
						$str .= "</dd>\n";
					else
						$str .= "</li>\n";
				}
				$str .= "</$pair[$ipair]>\n";
			}
			$ipair++;
			$pair = array();
		}else
		if($ipair > 0){
			$ipair--;
			if(substr($pair[$ipair], -1) == "-"){
				$pair[$ipair] = substr($pair[$ipair], 0, 2);
				if($pair[$ipair] == "dl")
					$str .= "</dd>\n";
				else
					$str .= "</li>\n";
			}
			$str .= "</$pair[$ipair]>\n";
#			array_splice($pair, $ipair);
		}
		return $str;
	}

	if(strpos("*#;", $str[0]) === false)
		return "";
	$listyle = "";
	if(preg_match("/^(\*+)[ \t]*(.*)$/", $str, $m))
		$tag = "ul";
	else
	if(preg_match("/^(#+)([1iIaA](?:[1-9][0-9]*)?)?[ \t]*(.*)$/", $str, $m)){
		$tag = "ol";
		if($m[2] != ""){
			$listyle .= " type=\"".$m[2][0]."\"";
			if(strlen($m[2]) > 1)
				$listyle .= " value=\"".substr($m[2], 1)."\"";
		}
		$m[2] = $m[3];
		$m[3] = "";
	}else
	if(preg_match("/^(;+)(.*?)(?<![\x02\\\\]):(.*)$/", $str, $m))
		$tag = "dl";
	else
		return "";
	$done = 1;

	$t = $tag[0];

	$str = "";
	$n = strlen($m[1]);
	if($n > $ipair){
		for(; $ipair<$n; $ipair++){
			$str .= "<$tag class=\"$tag\">\n";
			$pair[$ipair] = $tag;
		}
	}else
	if($n < $ipair){
		for(; --$ipair>=$n; ){
			if(substr($pair[$ipair], -1) == "-"){
				$pair[$ipair] = substr($pair[$ipair], 0, 2);
				if($pair[$ipair] == "dl")
					$str .= "</dd>\n";
				else
					$str .= "</li>\n";
			}
			$str .= "</$pair[$ipair]>\n";
		}
		if(preg_match("/$tag-?/", $pair[$ipair])){
			if(substr($pair[$ipair], -1) == "-"){
				$pair[$ipair] = substr($pair[$ipair], 0, 2);
				if($pair[$ipair] == "dl")
					$str .= "</dd>\n";
				else
					$str .= "</li>\n";
			}
			$ipair++;
		}else{
			for(; $ipair>=0&&$pair[$ipair][0]!=$t; $ipair--){
				if(substr($pair[$ipair], -1) == "-"){
					$pair[$ipair] = substr($pair[$ipair], 0, 2);
					if($pair[$ipair] == "dl")
						$str .= "</dd>\n";
					else
						$str .= "</li>\n";
				}
				$str .= "</$pair[$ipair]>\n";
			}
			for(; ++$ipair<$n; ){
				$str .= "<$tag class=\"$tag\">\n";
				$pair[$ipair] = $tag;
			}
		}
#		array_splice($pair, $ipair);
	}else
	if($pair[$ipair-1][0] != $t){
		if(substr($pair[$ipair-1], -1) == "-"){
			$pair[$ipair-1] = substr($pair[$ipair-1], 0, 2);
			if($pair[$ipair-1] == "dl")
				$str .= "</dd>\n";
			else
				$str .= "</li>\n";
		}
		$str .= "</".$pair[$ipair-1].">\n<$tag class=\"$tag\">\n";
		$pair[$ipair-1] = $tag;
	}else
	if($tag == "dl")
		$str .= "</dd>\n";
	else
		$str .= "</li>\n";

	$pair[$ipair-1] .= "-";
	if($tag == "dl")
		$str .= "<dt>$m[2]</dt>\n<dd>$m[3]";
	else
		$str .= "<li$listyle>$m[2]";

	return $str;
}

function plugin($str, $DisplayPool, &$displaypool){
	global	$db, $backendDB, $wikiXdir, $wikiXFrontpage, $action,
		$mytheme, $today, $admin, $adminAuthor, $author, $btime,
		$pagename0, $wikiXheader, $wikiXfooter, $LinkPool, $limitlist;

	if(strpos($str, "\\") === false)
		return $str;

	if(($n = preg_match_all("/\\\\mafi\{(.*?)(?<!\\\\)\}/", $str, $m))){
		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}
		for($i=0; $i<$n; $i++){
			$r = mafi(clean4plugin($m[1][$i]), 1);
			$str = preg_replace("\x01\\\\mafi\{".preg_quote($m[1][$i])."\}\x01", $r, $str);
		}
		if(strpos($str, "\\") === false)
			return $str;
	}
	if(($n = preg_match_all("/\\\\IncludeFile\{(.*?)(?<!\\\\)\}/", $str, $m))){
		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}

		for($i=0; $i<$n; $i++){
			$r = "";
			$file = "$wikiXdir/".clean4plugin($m[1][$i]);
			if(preg_match("'^$wikiXdir/(?:my)?file/'", $file) &&
				strpos($file, "/../") === false && is_readable($file)){
				$fp = fopen($file, "r");
				$r = fread($fp, filesize($file));
				$r = geni_specialchars0($r);
				$r = str_replace("\\", "\x03", $r);
				fclose($fp);
			}
			$str = preg_replace("\x01\\\\IncludeFile\{".preg_quote($m[1][$i])."\}\x01", $r, $str);
		}
		if(strpos($str, "\\") === false)
			return $str;
	}
	if(preg_match("'^\\\\LimitList:(!?L)?(!?H)?(?:(?:(!)?([0-9]{4}(?:-[0-9]{2}(?:-[0-9]{2})?)?(?: [0-9]{2}(?::[0-9]{2}(?::[0-9]{2})?)?)?))?~(?:(!)?([0-9]{4}(?:-[0-9]{2}(?:-[0-9]{2})?)?(?: [0-9]{2}(?::[0-9]{2}(?::[0-9]{2})?)?)?))?(/P)?)?$'", $str, $m)){
		$limitlist[0] = "";
		$limitlist[1] = "";
		if($m[1] == "L")
			$limitlist[0] .= "and page.locked=1 ";
		else
		if($m[1] == "!L")
			$limitlist[0] .= "and page.locked=0 ";
		if($admin){
			if($m[2] == "H")
				$limitlist[0] .= "and page.hidden=1 ";
			else
			if($m[2] == "!H")
				$limitlist[0] .= "and page.hidden=0 ";
		}
		$op = " and ";
		$_m = array();
		if($m[4] != "")
			$_m[] = "\x02>".($m[3]==""?"=":"")."'$m[4]'";
		if($m[6] != ""){
			$_m[] = "\x02<".($m[5]==""?"=":"")."'$m[6]'";
			if($m[4] != "" && $m[4] > $m[6])
				$op = " or ";
		}
		if(count($_m)){
			$_m2 = "(".implode($op, $_m).") ";
			$limitlist[0] .= "and ".str_replace("\x02", ($m[7]==""?"data.mtime":"page.ctime"), $_m2);
			$limitlist[1] = "where ".str_replace("\x02", ($m[7]==""?"mtime":"ctime"), $_m2);
		}
		return "";
	}
	if(($n = preg_match_all("/\\\\SearchPages(@?\{.*?(?<!\\\\)\}[0-9]*(?:-[0-9]*)?)(?![a-zA-Z])/", $str, $m))){
		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}

		for($i=0; $i<$n; $i++){
			$porder = 0;
			$start = 0;
			$ninfos = $m[1][$i];
			if($ninfos[0] == "@"){
				$porder = 2;
				$ninfos = substr($ninfos, 1);
			}
			preg_match("/^\{(.*)(?<!\\\\)\}(.*)$/", $ninfos, $_m);
			$search = clean4plugin($_m[1]);
			$ninfos = $_m[2];
			if(preg_match("/^([0-9]*)-([0-9]*)$/", $ninfos, $_m)){
				$start = $_m[1];
				$ninfos = $_m[2];
			}
			$query = search_query($search, $tc,
						$ibegin, $iend, $order, $regex);
			$porder = ($order==""?1:$porder);
			$color = (strpos($order, " order by data.mtime ")===false?0:1);
			$r = pagelist($query, $start, $ninfos, $porder, $color);
			$str = preg_replace("\x01\\\\SearchPages".preg_quote($m[1][$i])."(?![0-9a-zA-Z])\x01", $r, $str);
		}
		if(strpos($str, "\\") === false)
			return $str;
	}
	if(($n = preg_match_all("/\\\\SearchPages([0-9]*)(?![a-zA-Z])/", $str, $m))){
		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}

		for($i=0; $i<$n; $i++){
			$size = $m[1][$i];
			if($size == "")
				$size = 30;
			$r = "<form action=\"index.php\" method=\"get\" style=\"display:inline;\"><input name=\"search\" class=\"text\" type=\"text\" size=\"$size\"></form>";
			$str = preg_replace("/\\\\SearchPages".$m[1][$i]."(?![0-9a-zA-Z])/", $r, $str);
		}
		if(strpos($str, "\\") === false)
			return $str;
	}
	if(($n = preg_match_all("/\\\\GoTo([0-9]*)(?![a-zA-Z])/", $str, $m))){
		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}

		for($i=0; $i<$n; $i++){
			$size = $m[1][$i];
			if($size == "")
				$size = 30;
			$r = "<form action=\"index.php\" method=\"get\" style=\"display:inline;\"><input name=\"goto\" class=\"text\" type=\"text\" size=\"$size\"></form>";
			$str = preg_replace("/\\\\GoTo".$m[1][$i]."(?![0-9a-zA-Z])/", $r, $str);
		}
		if(strpos($str, "\\") === false)
			return $str;
	}
	if(($n = preg_match_all("/\\\\RecentChanges(@?\*?[0-9]*(?:-[0-9]*)?)(?![a-zA-Z])/", $str, $m))){
		$Query = "select page.id, page.name, page.hits, page.ctime,
				page.locked, page.hidden,
				data.author, data.ip, data.mtime,
				data.content='\x01' as deleted
				from page, data
				where page.id=data.id
				and page.version=data.version ".
				($admin?"":"and page.hidden=0 ").
				"\x02order by data.mtime desc, page.name";

		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}

		for($i=0; $i<$n; $i++){
			$porder = 0;
			$ndays = 0;
			$start = 0;
			$ninfos = $m[1][$i];
			$query = $Query;
			if($ninfos[0] == "@"){
				$porder = 2;
				$ninfos = substr($ninfos, 1);
			}
			if($ninfos[0] == "*"){
				$ndays = 1;
				$ninfos = substr($ninfos, 1);
			}
			if(preg_match("/^([0-9]*)-([0-9]*)$/", $ninfos, $_m)){
				$start = $_m[1];
				$ninfos = $_m[2];
			}
			if($ndays){
				$from = strtotime($today)-$ninfos*24*60*60;
				$from = date("Y-m-d", $from);
				$ninfos = preg_replace("/^(0*)(?:[1-9][0-9]*)?$/", "\\1", $ninfos);
				$query = str_replace("\x02",
						"and data.mtime>='$from' \x02",
						$query);
				if($start){
					$to = strtotime($today)-($start-1)*24*60*60;
					$to = date("Y-m-d", $to);
					$query = str_replace("\x02",
						"and data.mtime<'$to' \x02",
						$query);
					$start = 0;
				}
			}
			$r = pagelist($query, $start, $ninfos, $porder, 1);
			$str = preg_replace("\x01\\\\RecentChanges".preg_quote($m[1][$i])."(?![0-9a-zA-Z])\x01", $r, $str);
		}
		if(strpos($str, "\\") === false)
			return $str;
	}
	if(($n = preg_match_all("/\\\\RecentChangesTo(@?\*?(?:\{.*?(?<!\\\\)\})?[0-9]*(?:-[0-9]*)?)(?![a-zA-Z])/", $str, $m))){
		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}

		for($i=0; $i<$n; $i++){
			$ipagename = $pagename0;
			$porder = 0;
			$ndays = 0;
			$start = 0;
			$ninfos = $m[1][$i];
			if($ninfos[0] == "@"){
				$porder = 2;
				$ninfos = substr($ninfos, 1);
			}
			if($ninfos[0] == "*"){
				$ndays = 1;
				$ninfos = substr($ninfos, 1);
			}
			if(preg_match("/^\{(.*)(?<!\\\\)\}(.*)$/", $ninfos, $_m)){
				$ipagename = clean4plugin($_m[1]);
				$ninfos = $_m[2];
			}
			if(preg_match("/^([0-9]*)-([0-9]*)$/", $ninfos, $_m)){
				$start = $_m[1];
				$ninfos = $_m[2];
			}
			$r = "";
			$iPagename = addslashes($ipagename);
			$id = pageid($iPagename);
			$query = "select page.id, page.name, page.hits,
				page.ctime, page.locked, page.hidden,
				data.author, data.ip, data.mtime,
				data.content='\x01' as deleted
				from page, data, link
				where page.id=data.id
				and page.version=data.version ".
				($admin?"":"and page.hidden=0 ").
				"and link.linkfrom=page.id
				and ".($id?"link.linkto=$id ":
				"link.linktoname='$iPagename' ").
				"\x02order by data.mtime desc, page.name";
			if($ndays){
				$from = strtotime($today)-$ninfos*24*60*60;
				$from = date("Y-m-d", $from);
				$ninfos = preg_replace("/^(0*)(?:[1-9][0-9]*)?$/", "\\1", $ninfos);
				$query = str_replace("\x02",
						"and data.mtime>='$from' \x02",
						$query);
				if($start){
					$to = strtotime($today)-($start-1)*24*60*60;
					$to = date("Y-m-d", $to);
					$query = str_replace("\x02",
						"and data.mtime<'$to' \x02",
						$query);
					$start = 0;
				}
			}
			$r = pagelist($query, $start, $ninfos, $porder, 1);
			$str = preg_replace("\x01\\\\RecentChangesTo".preg_quote($m[1][$i])."(?![0-9a-zA-Z])\x01", $r, $str);
		}
		if(strpos($str, "\\") === false)
			return $str;
	}
	if(($n = preg_match_all("/\\\\RecentChangesFrom(@?\*?(?:\{.*?(?<!\\\\)\})?[0-9]*(?:-[0-9]*)?)(?![a-zA-Z])/", $str, $m))){
		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}

		for($i=0; $i<$n; $i++){
			$ipagename = $pagename0;
			$porder = 0;
			$ndays = 0;
			$start = 0;
			$ninfos = $m[1][$i];
			if($ninfos[0] == "@"){
				$porder = 2;
				$ninfos = substr($ninfos, 1);
			}
			if($ninfos[0] == "*"){
				$ndays = 1;
				$ninfos = substr($ninfos, 1);
			}
			if(preg_match("/^\{(.*)(?<!\\\\)\}(.*)$/", $ninfos, $_m)){
				$ipagename = clean4plugin($_m[1]);
				$ninfos = $_m[2];
			}
			if(preg_match("/^([0-9]*)-([0-9]*)$/", $ninfos, $_m)){
				$start = $_m[1];
				$ninfos = $_m[2];
			}
			$r = "";
			if(($id = pageid(addslashes($ipagename)))){
				$query = "select page.id, page.name, page.hits,
					page.ctime, page.locked, page.hidden,
					data.author, data.ip, data.mtime,
					data.content='\x01' as deleted
					from page, data, link
					where page.id=data.id
					and page.version=data.version ".
					($admin?"":"and page.hidden=0 ").
					"and link.linkfrom=$id
					and link.linkto=page.id
					\x02order by data.mtime desc, page.name";
				if($ndays){
					$from = strtotime($today)
						-$ninfos*24*60*60;
					$from = date("Y-m-d", $from);
					$ninfos = preg_replace(
						"/^(0*)(?:[1-9][0-9]*)?$/",
						"\\1", $ninfos);
					$query = str_replace("\x02",
						"and data.mtime>='$from' \x02",
						$query);
					if($start){
						$to = strtotime($today)-($start-1)*24*60*60;
						$to = date("Y-m-d", $to);
						$query = str_replace("\x02",
							"and data.mtime<'$to' \x02",
							$query);
						$start = 0;
					}
				}
				$r = pagelist($query, $start, $ninfos, $porder, 1);
			}
			$str = preg_replace("\x01\\\\RecentChangesFrom".preg_quote($m[1][$i])."(?![0-9a-zA-Z])\x01", $r, $str);
		}
		if(strpos($str, "\\") === false)
			return $str;
	}
	if(($n = preg_match_all("/\\\\MostPopular(@?[0-9]*(?:-[0-9]*)?)(?![a-zA-Z])/", $str, $m))){
		$query = "select page.id, page.name, page.hits, page.ctime,
					page.locked, page.hidden,
					data.author, data.ip, data.mtime
					from page, data
					where page.id=data.id
					and page.version=data.version ".
					($admin?"":"and page.hidden=0 ").
					"and data.content!='\x01'
					\x02order by page.hits desc, page.name";

		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}

		for($i=0; $i<$n; $i++){
			$porder = 0;
			$start = 0;
			$ninfos = $m[1][$i];
			if($ninfos[0] == "@"){
				$porder = 2;
				$ninfos = substr($ninfos, 1);
			}
			if(preg_match("/^([0-9]*)-([0-9]*)$/", $ninfos, $_m)){
				$start = $_m[1];
				$ninfos = $_m[2];
			}
			$r = pagelist($query, $start, $ninfos, $porder);
			$str = preg_replace("\x01\\\\MostPopular".preg_quote($m[1][$i])."(?![0-9a-zA-Z])\x01", $r, $str);
		}
		if(strpos($str, "\\") === false)
			return $str;
	}
	if(($n = preg_match_all("/\\\\MostPopularTo(@?(?:\{.*?(?<!\\\\)\})?[0-9]*(?:-[0-9]*)?)(?![a-zA-Z])/", $str, $m))){
		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}

		for($i=0; $i<$n; $i++){
			$ipagename = $pagename0;
			$porder = 0;
			$start = 0;
			$ninfos = $m[1][$i];
			if($ninfos[0] == "@"){
				$porder = 2;
				$ninfos = substr($ninfos, 1);
			}
			if(preg_match("/^\{(.*)(?<!\\\\)\}(.*)$/", $ninfos, $_m)){
				$ipagename = clean4plugin($_m[1]);
				$ninfos = $_m[2];
			}
			if(preg_match("/^([0-9]*)-([0-9]*)$/", $ninfos, $_m)){
				$start = $_m[1];
				$ninfos = $_m[2];
			}
			$r = "";
			$iPagename = addslashes($ipagename);
			$id = pageid($iPagename);
			$query = "select page.id, page.name, page.hits,
				page.ctime, page.locked, page.hidden,
				data.author, data.ip, data.mtime
				from page, data, link
				where page.id=data.id
				and page.version=data.version ".
				($admin?"":"and page.hidden=0 ").
				"and link.linkfrom=page.id
				and ".($id?"link.linkto=$id ":
				"link.linktoname='$iPagename' ").
				"\x02order by page.hits desc, page.name";
			$r = pagelist($query, $start, $ninfos, $porder);
			$str = preg_replace("\x01\\\\MostPopularTo".preg_quote($m[1][$i])."(?![0-9a-zA-Z])\x01", $r, $str);
		}
		if(strpos($str, "\\") === false)
			return $str;
	}
	if(($n = preg_match_all("/\\\\MostPopularFrom(@?(?:\{.*?(?<!\\\\)\})?[0-9]*(?:-[0-9]*)?)(?![a-zA-Z])/", $str, $m))){
		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}

		for($i=0; $i<$n; $i++){
			$ipagename = $pagename0;
			$porder = 0;
			$start = 0;
			$ninfos = $m[1][$i];
			if($ninfos[0] == "@"){
				$porder = 2;
				$ninfos = substr($ninfos, 1);
			}
			if(preg_match("/^\{(.*)(?<!\\\\)\}(.*)$/", $ninfos, $_m)){
				$ipagename = clean4plugin($_m[1]);
				$ninfos = $_m[2];
			}
			if(preg_match("/^([0-9]*)-([0-9]*)$/", $ninfos, $_m)){
				$start = $_m[1];
				$ninfos = $_m[2];
			}
			$r = "";
			if(($id = pageid(addslashes($ipagename)))){
				$query = "select page.id, page.name, page.hits,
					page.ctime, page.locked, page.hidden,
					data.author, data.ip, data.mtime
					from page, data, link
					where page.id=data.id
					and page.version=data.version ".
					($admin?"":"and page.hidden=0 ").
					"and link.linkfrom=$id
					and link.linkto=page.id
					\x02order by page.hits desc, page.name";
				$r = pagelist($query, $start, $ninfos, $porder);
			}
			$str = preg_replace("\x01\\\\MostPopularFrom".preg_quote($m[1][$i])."(?![0-9a-zA-Z])\x01", $r, $str);
		}
		if(strpos($str, "\\") === false)
			return $str;
	}
	if(($n = preg_match_all("/\\\\AllPages(@?[0-9]*(?:-[0-9]*)?)(?![a-zA-Z])/", $str, $m))){
		$query = "select page.id, page.name, page.hits, page.ctime,
					page.locked, page.hidden,
					data.author, data.ip, data.mtime
					from page, data
					where page.id=data.id
					and page.version=data.version ".
					($admin?"":"and page.hidden=0 ").
					"and data.content!='\x01'
					\x02order by page.name";
		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}

		for($i=0; $i<$n; $i++){
			$porder = 0;
			$start = 0;
			$ninfos = $m[1][$i];
			if($ninfos[0] == "@"){
				$porder = 2;
				$ninfos = substr($ninfos, 1);
			}
			if(preg_match("/^([0-9]*)-([0-9]*)$/", $ninfos, $_m)){
				$start = $_m[1];
				$ninfos = $_m[2];
			}
			$r = pagelist($query, $start, $ninfos, $porder);
			$str = preg_replace("/\\\\AllPages".$m[1][$i]."(?![0-9a-zA-Z])/", $r, $str);
		}
		if(strpos($str, "\\") === false)
			return $str;
	}
	if(($n = preg_match_all("/\\\\AllPagesTo(@?(?:\{.*?(?<!\\\\)\})?[0-9]*(?:-[0-9]*)?)(?![a-zA-Z])/", $str, $m))){
		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}

		for($i=0; $i<$n; $i++){
			$ipagename = $pagename0;
			$porder = 0;
			$start = 0;
			$ninfos = $m[1][$i];
			if($ninfos[0] == "@"){
				$porder = 2;
				$ninfos = substr($ninfos, 1);
			}
			if(preg_match("/^\{(.*)(?<!\\\\)\}(.*)$/", $ninfos, $_m)){
				$ipagename = clean4plugin($_m[1]);
				$ninfos = $_m[2];
			}
			if(preg_match("/^([0-9]*)-([0-9]*)$/", $ninfos, $_m)){
				$start = $_m[1];
				$ninfos = $_m[2];
			}
			$r = "";
			$iPagename = addslashes($ipagename);
			$id = pageid($iPagename);
			$query = "select page.id, page.name, page.hits,
					page.ctime, page.locked, page.hidden,
					data.author, data.ip, data.mtime
					from page, data, link
					where page.id=data.id
					and page.version=data.version ".
					($admin?"":"and page.hidden=0 ").
					"and link.linkfrom=page.id
					and ".($id?"link.linkto=$id ":
					"link.linktoname='$iPagename' ").
					"\x02order by page.name";
			$r = pagelist($query, $start, $ninfos, $porder);
			$str = preg_replace("\x01\\\\AllPagesTo".preg_quote($m[1][$i])."(?![0-9a-zA-Z])\x01", $r, $str);
		}
		if(strpos($str, "\\") === false)
			return $str;
	}
	if(($n = preg_match_all("/\\\\AllPagesFrom(@?(?:\{.*?(?<!\\\\)\})?[0-9]*(?:-[0-9]*)?)(?![a-zA-Z])/", $str, $m))){
		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}

		for($i=0; $i<$n; $i++){
			$ipagename = $pagename0;
			$porder = 0;
			$start = 0;
			$ninfos = $m[1][$i];
			if($ninfos[0] == "@"){
				$porder = 2;
				$ninfos = substr($ninfos, 1);
			}
			if(preg_match("/^\{(.*)(?<!\\\\)\}(.*)$/", $ninfos, $_m)){
				$ipagename = clean4plugin($_m[1]);
				$ninfos = $_m[2];
			}
			if(preg_match("/^([0-9]*)-([0-9]*)$/", $ninfos, $_m)){
				$start = $_m[1];
				$ninfos = $_m[2];
			}
			$r = "";
			if(($id = pageid(addslashes($ipagename)))){
				$query = "select page.id, page.name, page.hits,
					page.ctime, page.locked, page.hidden,
					data.author, data.ip, data.mtime
					from page, data, link
					where page.id=data.id
					and page.version=data.version ".
					($admin?"":"and page.hidden=0 ").
					"and link.linkfrom=$id
					and link.linkto=page.id
					\x02order by page.name";
				$r = pagelist($query, $start, $ninfos, $porder);
			}
			$str = preg_replace("\x01\\\\AllPagesFrom".preg_quote($m[1][$i])."(?![0-9a-zA-Z])\x01", $r, $str);
		}
		if(strpos($str, "\\") === false)
			return $str;
	}
	if(($n = preg_match_all("/\\\\RecentPages(@?\*?[0-9]*(?:-[0-9]*)?)(?![a-zA-Z])/", $str, $m))){
		$Query = "select page.id, page.name, page.hits, page.ctime,
				page.locked, page.hidden,
				data.author, data.ip, data.mtime
				from page, data
				where page.id=data.id
				and page.version=data.version ".
				($admin?"":"and page.hidden=0 ").
				"and data.content!='\x01'
				\x02order by page.ctime desc, page.name";

		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}

		for($i=0; $i<$n; $i++){
			$porder = 0;
			$ndays = 0;
			$start = 0;
			$ninfos = $m[1][$i];
			$query = $Query;
			if($ninfos[0] == "@"){
				$porder = 2;
				$ninfos = substr($ninfos, 1);
			}
			if($ninfos[0] == "*"){
				$ndays = 1;
				$ninfos = substr($ninfos, 1);
			}
			if(preg_match("/^([0-9]*)-([0-9]*)$/", $ninfos, $_m)){
				$start = $_m[1];
				$ninfos = $_m[2];
			}
			if($ndays){
				$from = strtotime($today)-$ninfos*24*60*60;
				$from = date("Y-m-d", $from);
				$ninfos = preg_replace("/^(0*)(?:[1-9][0-9]*)?$/", "\\1", $ninfos);
				$query = str_replace("\x02",
						"and page.ctime>='$from' \x02",
						$query);
				if($start){
					$to = strtotime($today)-($start-1)*24*60*60;
					$to = date("Y-m-d", $to);
					$query = str_replace("\x02",
						"and page.ctime<'$to' \x02",
						$query);
					$start = 0;
				}
			}
			$r = pagelist($query, $start, $ninfos, $porder);
			$str = preg_replace("\x01\\\\RecentPages".preg_quote($m[1][$i])."(?![0-9a-zA-Z])\x01", $r, $str);
		}
		if(strpos($str, "\\") === false)
			return $str;
	}
	if(($n = preg_match_all("/\\\\RecentPagesTo(@?\*?(?:\{.*?(?<!\\\\)\})?[0-9]*(?:-[0-9]*)?)(?![a-zA-Z])/", $str, $m))){
		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}

		for($i=0; $i<$n; $i++){
			$ipagename = $pagename0;
			$porder = 0;
			$ndays = 0;
			$start = 0;
			$ninfos = $m[1][$i];
			if($ninfos[0] == "@"){
				$porder = 2;
				$ninfos = substr($ninfos, 1);
			}
			if($ninfos[0] == "*"){
				$ndays = 1;
				$ninfos = substr($ninfos, 1);
			}
			if(preg_match("/^\{(.*)(?<!\\\\)\}(.*)$/", $ninfos, $_m)){
				$ipagename = clean4plugin($_m[1]);
				$ninfos = $_m[2];
			}
			if(preg_match("/^([0-9]*)-([0-9]*)$/", $ninfos, $_m)){
				$start = $_m[1];
				$ninfos = $_m[2];
			}
			$r = "";
			$iPagename = addslashes($ipagename);
			$id = pageid($iPagename);
			$query = "select page.id, page.name, page.hits,
				page.ctime, page.locked, page.hidden,
				data.author, data.ip, data.mtime
				from page, data, link
				where page.id=data.id
				and page.version=data.version ".
				($admin?"":"and page.hidden=0 ").
				"and link.linkfrom=page.id
				and ".($id?"link.linkto=$id ":
				"link.linktoname='$iPagename' ").
				"\x02order by page.ctime desc, page.name";
			if($ndays){
				$from = strtotime($today)-$ninfos*24*60*60;
				$from = date("Y-m-d", $from);
				$ninfos = preg_replace("/^(0*)(?:[1-9][0-9]*)?$/", "\\1", $ninfos);
				$query = str_replace("\x02",
						"and page.ctime>='$from' \x02",
						$query);
				if($start){
					$to = strtotime($today)-($start-1)*24*60*60;
					$to = date("Y-m-d", $to);
					$query = str_replace("\x02",
						"and page.ctime<'$to' \x02",
						$query);
					$start = 0;
				}
			}
			$r = pagelist($query, $start, $ninfos, $porder);
			$str = preg_replace("\x01\\\\RecentPagesTo".preg_quote($m[1][$i])."(?![0-9a-zA-Z])\x01", $r, $str);
		}
		if(strpos($str, "\\") === false)
			return $str;
	}
	if(($n = preg_match_all("/\\\\RecentPagesFrom(@?\*?(?:\{.*?(?<!\\\\)\})?[0-9]*(?:-[0-9]*)?)(?![a-zA-Z])/", $str, $m))){
		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}

		for($i=0; $i<$n; $i++){
			$ipagename = $pagename0;
			$porder = 0;
			$ndays = 0;
			$start = 0;
			$ninfos = $m[1][$i];
			if($ninfos[0] == "@"){
				$porder = 2;
				$ninfos = substr($ninfos, 1);
			}
			if($ninfos[0] == "*"){
				$ndays = 1;
				$ninfos = substr($ninfos, 1);
			}
			if(preg_match("/^\{(.*)(?<!\\\\)\}(.*)$/", $ninfos, $_m)){
				$ipagename = clean4plugin($_m[1]);
				$ninfos = $_m[2];
			}
			if(preg_match("/^([0-9]*)-([0-9]*)$/", $ninfos, $_m)){
				$start = $_m[1];
				$ninfos = $_m[2];
			}
			$r = "";
			if(($id = pageid(addslashes($ipagename)))){
				$query = "select page.id, page.name, page.hits,
					page.ctime, page.locked, page.hidden,
					data.author, data.ip, data.mtime
					from page, data, link
					where page.id=data.id
					and page.version=data.version ".
					($admin?"":"and page.hidden=0 ").
					"and link.linkfrom=$id
					and link.linkto=page.id
					\x02order by page.ctime desc, page.name";
				if($ndays){
					$from = strtotime($today)
						-$ninfos*24*60*60;
					$from = date("Y-m-d", $from);
					$ninfos = preg_replace(
						"/^(0*)(?:[1-9][0-9]*)?$/",
						"\\1", $ninfos);
					$query = str_replace("\x02",
						"and page.ctime>='$from' \x02",
						$query);
					if($start){
						$to = strtotime($today)-($start-1)*24*60*60;
						$to = date("Y-m-d", $to);
						$query = str_replace("\x02",
							"and page.ctime<'$to' \x02",
							$query);
						$start = 0;
					}
				}
				$r = pagelist($query, $start, $ninfos, $porder);
			}
			$str = preg_replace("\x01\\\\RecentPagesFrom".preg_quote($m[1][$i])."(?![0-9a-zA-Z])\x01", $r, $str);
		}
		if(strpos($str, "\\") === false)
			return $str;
	}
	if(($n = preg_match_all("/\\\\RandomPages([0-9]*(?:-[0-9]*)?)(?![a-zA-Z])/", $str, $m))){
		$query = "select page.id, page.name, page.hits, page.ctime,
					page.locked, page.hidden,
					data.author, data.ip, data.mtime
					from page, data
					where page.id=data.id
					and page.version=data.version ".
					($admin?"":"and page.hidden=0 ").
					"and data.content!='\x01' \x02";
		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}

		for($i=0; $i<$n; $i++){
			$start = 0;
			$ninfos = $m[1][$i];
			if(preg_match("/^([0-9]*)-([0-9]*)$/", $ninfos, $_m)){
				$start = $_m[1];
				$ninfos = $_m[2];
			}
			$r = pagelist($query, $start, $ninfos, 1);
			$str = preg_replace("/\\\\RandomPages".$m[1][$i]."(?![0-9a-zA-Z])/", $r, $str);
		}
		if(strpos($str, "\\") === false)
			return $str;
	}
	if(($n = preg_match_all("/\\\\RandomPagesTo((?:\{.*?(?<!\\\\)\})?[0-9]*(?:-[0-9]*)?)(?![a-zA-Z])/", $str, $m))){
		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}

		for($i=0; $i<$n; $i++){
			$ipagename = $pagename0;
			$start = 0;
			$ninfos = $m[1][$i];
			if(preg_match("/^\{(.*)(?<!\\\\)\}(.*)$/", $ninfos, $_m)){
				$ipagename = clean4plugin($_m[1]);
				$ninfos = $_m[2];
			}
			if(preg_match("/^([0-9]*)-([0-9]*)$/", $ninfos, $_m)){
				$start = $_m[1];
				$ninfos = $_m[2];
			}
			$r = "";
			$iPagename = addslashes($ipagename);
			$id = pageid($iPagename);
			$query = "select page.id, page.name, page.hits,
					page.ctime, page.locked, page.hidden,
					data.author, data.ip, data.mtime
					from page, data, link
					where page.id=data.id
					and page.version=data.version ".
					($admin?"":"and page.hidden=0 ").
					"and link.linkfrom=page.id
					and ".($id?"link.linkto=$id":
					"link.linktoname='$iPagename'")." \x02";
			$r = pagelist($query, $start, $ninfos, 1);
			$str = preg_replace("\x01\\\\RandomPagesTo".preg_quote($m[1][$i])."(?![0-9a-zA-Z])\x01", $r, $str);
		}
		if(strpos($str, "\\") === false)
			return $str;
	}
	if(($n = preg_match_all("/\\\\RandomPagesFrom((?:\{.*?(?<!\\\\)\})?[0-9]*(?:-[0-9]*)?)(?![a-zA-Z])/", $str, $m))){
		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}

		for($i=0; $i<$n; $i++){
			$ipagename = $pagename0;
			$start = 0;
			$ninfos = $m[1][$i];
			if(preg_match("/^\{(.*)(?<!\\\\)\}(.*)$/", $ninfos, $_m)){
				$ipagename = clean4plugin($_m[1]);
				$ninfos = $_m[2];
			}
			if(preg_match("/^([0-9]*)-([0-9]*)$/", $ninfos, $_m)){
				$start = $_m[1];
				$ninfos = $_m[2];
			}
			$r = "";
			if(($id = pageid(addslashes($ipagename)))){
				$query = "select page.id, page.name, page.hits,
					page.ctime, page.locked, page.hidden,
					data.author, data.ip, data.mtime
					from page, data, link
					where page.id=data.id
					and page.version=data.version ".
					($admin?"":"and page.hidden=0 ").
					"and link.linkfrom=$id
					and link.linkto=page.id \x02";
				$r = pagelist($query, $start, $ninfos, 1);
			}
			$str = preg_replace("\x01\\\\RandomPagesFrom".preg_quote($m[1][$i])."(?![0-9a-zA-Z])\x01", $r, $str);
		}
		if(strpos($str, "\\") === false)
			return $str;
	}
	if(($n = preg_match_all("/\\\\RandomPage([0-9]*)(?![a-zA-Z])/", $str, $m))){
		$query = "select page.name from page, data
				where page.id=data.id
				and page.version=data.version ".
				($admin?"":"and page.hidden=0 ").
				"and data.content!='\x01' $limitlist[0]";
		$result = pm_query($db, $query);
		$nrows = pm_num_rows($result);

		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}

		for($i=0; $i<$n; $i++){
			$ipagename = pm_fetch_result($result,
							rand(0, $nrows-1), 0);
			$ipageName = geni_urlencode($ipagename);
			$ipagename = geni_specialchars($ipagename);
			$r = "<a class=\"wikiword_display\" href=\"index.php?display=$ipageName\">$ipagename</a>";
			$str = preg_replace("/\\\\RandomPage".$m[1][$i]."(?![0-9a-zA-Z])/", $r, $str);
		}
		pm_free_result($result);
		if(strpos($str, "\\") === false)
			return $str;
	}
	if(($n = preg_match_all("/\\\\RandomPageTo((?:\{.*?(?<!\\\\)\})?[0-9]*)(?![a-zA-Z])/", $str, $m))){
		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}

		for($i=0; $i<$n; $i++){
			$ipagename = $pagename0;
			if(preg_match("/^\{(.*)(?<!\\\\)\}/", $m[1][$i], $_m))
				$ipagename = clean4plugin($_m[1]);
			$r = "";
			$iPagename = addslashes($ipagename);
			$id = pageid($iPagename);
			$query = "select page.name from page, link
					where link.linkfrom=page.id
					and ".($id?"link.linkto=$id":
					"link.linktoname='$iPagename'").
					($admin?"":" and page.hidden=0").
					" $limitlist[0]";
			$result = pm_query($db, $query);
			$nrows = pm_num_rows($result);
			if($nrows){
				$ipagename = pm_fetch_result($result,
						rand(0, $nrows-1), 0);
				$ipageName = geni_urlencode($ipagename);
				$ipagename = geni_specialchars($ipagename);
				$r = "<a class=\"wikiword_display\" href=\"index.php?display=$ipageName\">$ipagename</a>";
			}
			pm_free_result($result);
			$str = preg_replace("\x01\\\\RandomPageTo".preg_quote($m[1][$i])."(?![0-9a-zA-Z])\x01", $r, $str);
		}
		if(strpos($str, "\\") === false)
			return $str;
	}
	if(($n = preg_match_all("/\\\\RandomPageFrom((?:\{.*?(?<!\\\\)\})?[0-9]*)(?![a-zA-Z])/", $str, $m))){
		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}

		for($i=0; $i<$n; $i++){
			$ipagename = $pagename0;
			if(preg_match("/^\{(.*)(?<!\\\\)\}/", $m[1][$i], $_m))
				$ipagename = clean4plugin($_m[1]);
			$r = "";
			if(($id = pageid(addslashes($ipagename)))){
				$query = "select page.name from page, link
					where link.linkfrom=$id
					and link.linkto=page.id".
					($admin?"":" and page.hidden=0").
					" $limitlist[0]";
				$result = pm_query($db, $query);
				$nrows = pm_num_rows($result);
				if($nrows){
					$ipagename = pm_fetch_result($result,
							rand(0, $nrows-1), 0);
					$ipageName = geni_urlencode($ipagename);
					$ipagename = geni_specialchars($ipagename);
					$r = "<a class=\"wikiword_display\" href=\"index.php?display=$ipageName\">$ipagename</a>";
				}
				pm_free_result($result);
			}
			$str = preg_replace("\x01\\\\RandomPageFrom".preg_quote($m[1][$i])."(?![0-9a-zA-Z])\x01", $r, $str);
		}
		if(strpos($str, "\\") === false)
			return $str;
	}
	if(($n = preg_match_all("/\\\\OrphanedPages([@*]?[0-9]*(?:-[0-9]*)?)(?![a-zA-Z])/", $str, $m))){
		$query = "select page.id, page.name, page.hits, page.ctime,
					page.locked, page.hidden,
					data.author, data.ip, data.mtime
					from page, data ".
					($backendDB=="mysql"?
					"left join link on page.id=link.linkto
					and link.linkfrom!=link.linkto
					where link.linkto is NULL ":
					"where page.id not in
					(select linkto from link
					where linkfrom!=linkto) ").
					"and page.id=data.id
					and page.version=data.version ".
					($admin?"":"and page.hidden=0 ").
					"and page.name!='$wikiXFrontpage'
					\x02order by page.name";
		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}

		for($i=0; $i<$n; $i++){
			$porder = 0;
			$start = 0;
			$ninfos = $m[1][$i];
			if($ninfos != ""){
				switch($ninfos[0]){
				case "@":
					$porder = 2;
					$ninfos = substr($ninfos, 1);
					break;
				case "*":
					$porder = 1;
					$ninfos = substr($ninfos, 1);
					break;
				}
			}
			if(preg_match("/^([0-9]*)-([0-9]*)$/", $ninfos, $_m)){
				$start = $_m[1];
				$ninfos = $_m[2];
			}
			$r = pagelist($query, $start, $ninfos, $porder);
			$str = preg_replace("\x01\\\\OrphanedPages".preg_quote($m[1][$i])."(?![0-9a-zA-Z])\x01", $r, $str);
		}
		if(strpos($str, "\\") === false)
			return $str;
	}
	if(($n = preg_match_all("/\\\\WantedPages([@*]?[0-9]*(?:-[0-9]*)?)(?![a-zA-Z])/", $str, $m))){
		$query = "select link.linktoname, count(link.linkfrom) as nlinks
					from page, link
					where link.linkto=0
					and page.id=link.linkfrom ".
					($admin?"":"and page.hidden=0 ").
					"group by link.linktoname
					order by nlinks desc, link.linktoname";
		$result = pm_query($db, $query);
		$nrows = pm_num_rows($result);

		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}
		$Index = range(0, $nrows-1);
		for($i=0; $i<$n; $i++){
			$index = $Index;
			$start = 0;
			$ninfos = $m[1][$i];
			if($ninfos != ""){
				if($ninfos[0] == "@"){
					$index = array_reverse($index);
					$ninfos = substr($ninfos, 1);
				}else
				if($ninfos[0] == "*"){
					shuffle($index);
					$ninfos = substr($ninfos, 1);
				}
			}
			if(preg_match("/^([0-9]*)-([0-9]*)$/", $ninfos, $_m)){
				$start = $_m[1];
				$ninfos = $_m[2];
			}
			$list = 0;
			$mode = 0;
			$tag1 = "";
			$tag2 = "";
			$tag3 = "";
			$tag4 = "";

			if($ninfos[0] === "0" &&
					preg_match("/^(0+)/", $ninfos, $_m)){
				switch("x$_m[1]"){
				case "x0":
					$mode = 1;
					$tag2 = "<tr><td";
					$tag3 = "</td></tr>";
					break;
				case "x00":
					$mode = 2;
					$tag1 = "<tr>\n";
					$tag2 = "<td";
					$tag3 = "</td>";
					$tag4 = "</tr>\n";
					break;
				case "x000":
					$list = 1;
					break;
				case "x0000":
					$list = 1;
					$mode = 1;
					break;
				case "x00000":
					$list = 1;
					$mode = 2;
					break;
				case "x000000":
					$list = 2;
					break;
				case "x0000000":
					$list = 2;
					$mode = 1;
					break;
				case "x00000000":
					$list = 3;
					$mode = 1;
					break;
				}
				if($list)
					$color = 0;
			}

			if($ninfos == "")
				$end = $start + $nrows;
			else
				$end = $start + $ninfos;
			if($end < 1 || $end > $nrows)
				$end = $nrows;
			$nr = $end - $start;
			$r = "";
			if($nr > 0 && $list < 2){
				if($list){
					if($mode == 2)
						$r .= "<ul class=\"pagelist\">\n";
					else
						$r .= "<ol class=\"pagelist\">\n";
				}else{
					$r .=
"<table class=\"pagelist\">\n".($mode?$tag1:
"<tr class=\"pagelist_header\">".
"<td>&nbsp;<span class=\"table_header\">Page</span>".(!$ninfos||$ninfos>$nr?" ($nr page".($nr>1?"s":"").")":"")."&nbsp;</td>".
"<td align=\"right\">&nbsp;<span class=\"table_header\">To</span>&nbsp;</td>".
"</tr>\n");
				}
			}
			$nl = 0;
			$class = "outdated";
			for($j=$start; $j<$end; $j++){
				$data = pm_fetch_array($result, $index[$j]);
				$w = split_word($data['linktoname']);
				$w[0] = geni_specialchars($w[0]);
				$w[1] = geni_specialchars($w[1]);
				$linkName = geni_urlencode($data['linktoname']);
				if($nl != $data['nlinks']){
					$class = ($class=="outdated"?
							"recent":"outdated");
					$nl = $data['nlinks'];
				}
				if($list > 1){
					$r .=
"<a class=\"wikiword_goto\" href=\"index.php?goto=$linkName\"".
($mode?" title=\"$data[nlinks]\"":"").">$w[0]</a>$w[1]".($mode?"":" ... <a class=\"a\" href=\"index.php?links2=$linkName\">$data[nlinks]</a>").($j<$end-1?($list==2?"<br class=\"br\" />\n":", "):"");
				}else
				if($list){
					$r .=
"<li><a class=\"wikiword_goto\" href=\"index.php?goto=$linkName\"".
($mode?" title=\"$data[nlinks]\"":"").">$w[0]</a>$w[1]".($mode?"":" ... <a class=\"a\" href=\"index.php?links2=$linkName\">$data[nlinks]</a>").
"</li>\n";
				}else{
					$r .=
($mode?
"$tag2 class=\"pagelist_$class\">&nbsp;<a class=\"wikiword_goto\" href=\"index.php?goto=$linkName\">$w[0]</a>$w[1] <i>(<a class=\"a\" href=\"index.php?links2=$linkName\">$data[nlinks]</a>)</i>&nbsp;$tag3\n":
"<tr class=\"pagelist_$class\">".
"<td>&nbsp;<a class=\"wikiword_goto\" href=\"index.php?goto=$linkName\">$w[0]</a>$w[1]&nbsp;</td>".
"<td align=\"right\">&nbsp;<a class=\"a\" href=\"index.php?links2=$linkName\">$data[nlinks]</a>&nbsp;</td>".
"</tr>\n");
				}
			}
			if($nr > 0 && $list < 2){
				if($list){
					$r .= ($mode==2?"</ul>":"</ol>");
				}else{
					$r .= "$tag4</table>";
				}
			}
			$str = preg_replace("\x01\\\\WantedPages".preg_quote($m[1][$i])."(?![0-9a-zA-Z])\x01", str_replace("\\", "\x03", $r), $str);
		}
		pm_free_result($result);
		if(strpos($str, "\\") === false)
			return $str;
	}
	if(($n = preg_match_all("/\\\\AllAuthors([@*]?[0-9]*(?:-[0-9]*)?)(?![a-zA-Z])/", $str, $m))){
		$updateuser = 0;
		if($admin){
			if($action == "display" || $action == "doit")
				$updateuser = 1;
			$firstcol = " colspan=\"2\"";
			$last = "<td colspan=\"2\">&nbsp;<span class=\"table_header\">Last Login</span>&nbsp;</td>";
			$chktime = "mtime";
		}else{
			$firstcol = "";
			$last = "";
			$chktime = "ctime";
		}
		$query = "select id, ".($updateuser?"pw, ":"").
				"cip, ctime, mip, mtime from userdb
				$limitlist[1]order by $chktime desc, id";
		$result = pm_query($db, $query);
		$nrows = pm_num_rows($result);

		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}
		$Index = range(0, $nrows-1);
		for($i=0; $i<$n; $i++){
			$index = $Index;
			$start = 0;
			$ninfos = $m[1][$i];
			if($ninfos != ""){
				if($ninfos[0] == "@"){
					$index = array_reverse($index);
					$ninfos = substr($ninfos, 1);
				}else
				if($ninfos[0] == "*"){
					shuffle($index);
					$ninfos = substr($ninfos, 1);
				}
			}
			if(preg_match("/^([0-9]*)-([0-9]*)$/", $ninfos, $_m)){
				$start = $_m[1];
				$ninfos = $_m[2];
			}
			$list = 0;
			$mode = 0;
			$tag1 = "";
			$tag2 = "";
			$tag3 = "";
			$tag4 = "";

			if($ninfos[0] === "0" &&
					preg_match("/^(0+)/", $ninfos, $_m)){
				switch("x$_m[1]"){
				case "x0":
					$mode = 1;
					$tag2 = "<tr><td";
					$tag3 = "</td></tr>";
					break;
				case "x00":
					$mode = 2;
					$tag1 = "<tr>\n";
					$tag2 = "<td";
					$tag3 = "</td>";
					$tag4 = "</tr>\n";
					break;
				case "x000":
					$list = 1;
					break;
				case "x0000":
					$list = 1;
					$mode = 1;
					break;
				case "x00000":
					$list = 1;
					$mode = 2;
					break;
				case "x000000":
					$list = 2;
					break;
				case "x0000000":
					$list = 2;
					$mode = 1;
					break;
				case "x00000000":
					$list = 3;
					$mode = 1;
					break;
				}
				if($list)
					$color = 0;
			}

			if($ninfos == "")
				$end = $start + $nrows;
			else
				$end = $start + $ninfos;
			if($end < 1 || $end > $nrows)
				$end = $nrows;
			$nr = $end - $start;
			$r = "";
			if($nr > 0 && $list < 2){
				if($list){
					if($mode == 2)
						$r .= "<ul class=\"pagelist\">\n";
					else
						$r .= "<ol class=\"pagelist\">\n";
				}else{
					$r .=
"<table class=\"pagelist\">\n".($mode?$tag1:
"<tr class=\"pagelist_header\">".
"<td>&nbsp;<span class=\"table_header\">Author</span>".(!$ninfos||$ninfos>$nr?" ($nr author".($nr>1?"s":"").")":"")."&nbsp;</td>".
"<td$firstcol>&nbsp;<span class=\"table_header\">First Login</span>&nbsp;</td>$last".
"</tr>\n");
				}
			}
			$date = "";
			$class = "outdated";
			for($j=$start; $j<$end; $j++){
				$data = pm_fetch_array($result, $index[$j]);
				if(pageid($data['id'])){
					$doit = "display";
					$w[0] = $data['id'];
					$w[1] = "";
				}else{
					$doit = "goto";
					$w = split_word($data['id']);
				}
				$iauthor = geni_urlencode($data['id']);
				list($mdate, $mtime) = explode(" ", $data[$chktime]);
				if($date !== $mdate){
					$class = ($class=="outdated"?
							"recent":"outdated");
					$date = $mdate;
				}
				if($admin){
					if($updateuser)
						$mtimestr = "<a href=\"index.php?1,0,updateuser=$iauthor\">$mdate</a> ".($data['pw']==""?$mtime:"<a href=\"index.php?0,1,updateuser=$iauthor\">$mtime</a>");
					else
						$mtimestr = $data['mtime'];
				}
				if($list > 1){
					if($mode)
						$loginfo = ($admin?"$data[cip]@$data[ctime] ... $data[mip]@$data[mtime]":"$data[ctime]");
					else
						$loginfo = ($admin?"<small class=\"small\">$data[cip]@$data[ctime]</small> ... <small class=\"small\">$data[mip]@$mtimestr</small>":"<small class=\"small\">$data[ctime]</small>");
					$r .=
"<a class=\"wikiword_$doit\" href=\"index.php?$doit=$iauthor\"".
($mode?" title=\"$loginfo\"":"").">$w[0]</a>$w[1]".($mode?"":" ... $loginfo").($j<$end-1?($list==2?"<br class=\"br\" />\n":", "):"");
				}else
				if($list){
					if($mode)
						$loginfo = ($admin?"$data[cip]@$data[ctime] ... $data[mip]@$data[mtime]":"$data[ctime]");
					else
						$loginfo = ($admin?"<small class=\"small\">$data[cip]@$data[ctime]</small> ... <small class=\"small\">$data[mip]@$mtimestr</small>":"<small class=\"small\">$data[ctime]</small>");
					$r .=
"<li><a class=\"wikiword_$doit\" href=\"index.php?$doit=$iauthor\"".
($mode?" title=\"$loginfo\"":"").">$w[0]</a>$w[1]".($mode?"":" ... $loginfo").
"</li>\n";
				}else{
					$r .=
($mode?
"$tag2 class=\"pagelist_$class\">&nbsp;<a class=\"wikiword_$doit\" href=\"index.php?$doit=$iauthor\">$w[0]</a>$w[1] <i>(".($admin?$mtimestr:$data['ctime']).")</i>&nbsp;$tag3\n":
"<tr class=\"pagelist_$class\">".
"<td>&nbsp;<a class=\"wikiword_$doit\" href=\"index.php?$doit=$iauthor\">$w[0]</a>$w[1]&nbsp;</td>".
($admin?"<td>&nbsp;$data[cip]&nbsp;</td>":"").
"<td>&nbsp;$data[ctime]&nbsp;</td>".
($admin?"<td>&nbsp;$data[mip]&nbsp;</td>".
"<td>&nbsp;$mtimestr&nbsp;</td>":"").
"</tr>\n");
				}
			}
			if($nr > 0 && $list < 2){
				if($list){
					$r .= ($mode==2?"</ul>":"</ol>");
				}else{
					$r .= "$tag4</table>";
				}
			}
			$str = preg_replace("\x01\\\\AllAuthors".preg_quote($m[1][$i])."(?![0-9a-zA-Z])\x01", $r, $str);
		}
		pm_free_result($result);
		if(strpos($str, "\\") === false)
			return $str;
	}
	if(($n = preg_match_all("/\\\\AllAdmins([@*]?[0-9]*(?:-[0-9]*)?)(?![a-zA-Z])/", $str, $m))){
		if(!$admin)
			$str = preg_replace("/\\\\AllAdmins[@*]?[0-9]*(?:-[0-9]*)?(?![a-zA-Z])/", "", $str);
		else{
		$updateadmin = 0;
		if($action == "display" || $action == "doit")
			$updateadmin = 1;
		$firstcol = " colspan=\"2\"";
		$last = "<td colspan=\"2\">&nbsp;<span class=\"table_header\">Last Login</span>&nbsp;</td>";
		$query = "select id, ".($updateadmin?"pw, ":"").
					"cip, ctime, mip, mtime from admindb
					$limitlist[1]order by mtime desc, id";
		$result = pm_query($db, $query);
		$nrows = pm_num_rows($result);

		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}
		$Index = range(0, $nrows-1);
		for($i=0; $i<$n; $i++){
			$index = $Index;
			$start = 0;
			$ninfos = $m[1][$i];
			if($ninfos != ""){
				if($ninfos[0] == "@"){
					$index = array_reverse($index);
					$ninfos = substr($ninfos, 1);
				}else
				if($ninfos[0] == "*"){
					shuffle($index);
					$ninfos = substr($ninfos, 1);
				}
			}
			if(preg_match("/^([0-9]*)-([0-9]*)$/", $ninfos, $_m)){
				$start = $_m[1];
				$ninfos = $_m[2];
			}
			$list = 0;
			$mode = 0;
			$tag1 = "";
			$tag2 = "";
			$tag3 = "";
			$tag4 = "";

			if($ninfos[0] === "0" &&
					preg_match("/^(0+)/", $ninfos, $_m)){
				switch("x$_m[1]"){
				case "x0":
					$mode = 1;
					$tag2 = "<tr><td";
					$tag3 = "</td></tr>";
					break;
				case "x00":
					$mode = 2;
					$tag1 = "<tr>\n";
					$tag2 = "<td";
					$tag3 = "</td>";
					$tag4 = "</tr>\n";
					break;
				case "x000":
					$list = 1;
					break;
				case "x0000":
					$list = 1;
					$mode = 1;
					break;
				case "x00000":
					$list = 1;
					$mode = 2;
					break;
				case "x000000":
					$list = 2;
					break;
				case "x0000000":
					$list = 2;
					$mode = 1;
					break;
				case "x00000000":
					$list = 3;
					$mode = 1;
					break;
				}
				if($list)
					$color = 0;
			}

			if($ninfos == "")
				$end = $start + $nrows;
			else
				$end = $start + $ninfos;
			if($end < 1 || $end > $nrows)
				$end = $nrows;
			$nr = $end - $start;
			$r = "";
			if($nr > 0 && $list < 2){
				if($list){
					if($mode == 2)
						$r .= "<ul class=\"pagelist\">\n";
					else
						$r .= "<ol class=\"pagelist\">\n";
				}else{
					$r .=
"<table class=\"pagelist\">\n".($mode?$tag1:
"<tr class=\"pagelist_header\">".
"<td>&nbsp;<span class=\"table_header\">Author</span>".(!$ninfos||$ninfos>$nr?" ($nr author".($nr>1?"s":"").")":"")."&nbsp;</td>".
"<td$firstcol>&nbsp;<span class=\"table_header\">First Login</span>&nbsp;</td>$last".
"</tr>\n");
				}
			}
			$date = "";
			$class = "outdated";
			for($j=$start; $j<$end; $j++){
				$data = pm_fetch_array($result, $index[$j]);
				if(pageid($data['id'])){
					$doit = "display";
					$w[0] = $data['id'];
					$w[1] = "";
				}else{
					$doit = "goto";
					$w = split_word($data['id']);
				}
				$iauthor = geni_urlencode($data['id']);
				list($mdate, $mtime) = explode(" ", $data['mtime']);
				if($date !== $mdate){
					$class = ($class=="outdated"?
							"recent":"outdated");
					$date = $mdate;
				}
				if($updateadmin && $author === $adminAuthor && $iauthor !== $adminAuthor)
					$mtimestr = "<a href=\"index.php?1,0,updateadmin=$iauthor\">$mdate</a> ".($data['pw']==""?$mtime:"<a href=\"index.php?0,1,updateadmin=$iauthor\">$mtime</a>");
				else
					$mtimestr = $data['mtime'];
				if($list > 1){
					if($mode)
						$loginfo = "$data[cip]@$data[ctime] ... $data[mip]@$data[mtime]";
					else
						$loginfo = "<small class=\"small\">$data[cip]@$data[ctime]</small> ... <small class=\"small\">$data[mip]@$mtimestr</small>";
					$r .=
"<a class=\"wikiword_$doit\" href=\"index.php?$doit=$iauthor\"".
($mode?" title=\"$loginfo\"":"").">$w[0]</a>$w[1]".($mode?"":" ... $loginfo").($j<$end-1?($list==2?"<br class=\"br\" />\n":", "):"");
				}else
				if($list){
					if($mode)
						$loginfo = "$data[cip]@$data[ctime] ... $data[mip]@$data[mtime]";
					else
						$loginfo = "<small class=\"small\">$data[cip]@$data[ctime]</small> ... <small class=\"small\">$data[mip]@$mtimestr</small>";
					$r .=
"<li><a class=\"wikiword_$doit\" href=\"index.php?$doit=$iauthor\"".
($mode?" title=\"$loginfo\"":"").">$w[0]</a>$w[1]".($mode?"":" ... $loginfo").
"</li>\n";
				}else{
					$r .=
($mode?
"$tag2 class=\"pagelist_$class\">&nbsp;<a class=\"wikiword_$doit\" href=\"index.php?$doit=$iauthor\">$w[0]</a>$w[1] <i>($mtimestr)</i>&nbsp;$tag3\n":
"<tr class=\"pagelist_$class\">".
"<td>&nbsp;<a class=\"wikiword_$doit\" href=\"index.php?$doit=$iauthor\">$w[0]</a>$w[1]&nbsp;</td>".
"<td>&nbsp;$data[cip]&nbsp;</td>".
"<td>&nbsp;$data[ctime]&nbsp;</td>".
"<td>&nbsp;$data[mip]&nbsp;</td>".
"<td>&nbsp;$mtimestr&nbsp;</td>".
"</tr>\n");
				}
			}
			if($nr > 0 && $list < 2){
				if($list){
					$r .= ($mode==2?"</ul>":"</ol>");
				}else{
					$r .= "$tag4</table>";
				}
			}
			$str = preg_replace("\x01\\\\AllAdmins".preg_quote($m[1][$i])."(?![0-9a-zA-Z])\x01", $r, $str);
		}
		pm_free_result($result);
		}
		if(strpos($str, "\\") === false)
			return $str;
	}
	if(($n = preg_match_all("/\\\\UploadedFiles((?:\{.*?(?<!\\\\)\})?)(?![a-zA-Z])/", $str, $m))){
		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}
		for($i=0; $i<$n; $i++){
			$ipagename = $pagename0;
			if(preg_match("/^\{(.*)(?<!\\\\)\}$/", $m[1][$i], $_m))
				$ipagename = clean4plugin($_m[1]);
			$iPagename = addslashes($ipagename);
			$r = uploadedfiles($iPagename);
			$str = preg_replace("\x01\\\\UploadedFiles".preg_quote($m[1][$i])."(?![0-9a-zA-Z])\x01", $r, $str);
		}
		if(strpos($str, "\\") === false)
			return $str;
	}
	if(($n = preg_match_all("/\\\\LinkUp((?:\{.*?(?<!\\\\)\})?[0-9]*)(?![a-zA-Z])/", $str, $m))){
		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}
		for($i=0; $i<$n; $i++){
			$ipagename = $pagename0;
			$depth = 10;
			$ninfos = $m[1][$i];
			if(preg_match("/^\{(.*)(?<!\\\\)\}(.*)$/", $ninfos, $_m)){
				$ipagename = clean4plugin($_m[1]);
				$ninfos = $_m[2];
			}
			if(preg_match("/^([0-9]+)$/", $ninfos, $_m))
				$depth = $_m[1];
			$iPagename = addslashes($ipagename);
			$LinkPool = array();
			$r = "<ol class=\"linkup\">\n".linkup($iPagename, $depth, $depth)."</ol>";
			$str = preg_replace("\x01\\\\LinkUp".preg_quote($m[1][$i])."(?![0-9a-zA-Z])\x01", $r, $str);
		}
		if(strpos($str, "\\") === false)
			return $str;
	}
	if(($n = preg_match_all("/\\\\LinkDown((?:\{.*?(?<!\\\\)\})?[0-9]*)(?![a-zA-Z])/", $str, $m))){
		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}
		for($i=0; $i<$n; $i++){
			$ipagename = $pagename0;
			$depth = 10;
			$ninfos = $m[1][$i];
			if(preg_match("/^\{(.*)(?<!\\\\)\}(.*)$/", $ninfos, $_m)){
				$ipagename = clean4plugin($_m[1]);
				$ninfos = $_m[2];
			}
			if(preg_match("/^([0-9]+)$/", $ninfos, $_m))
				$depth = $_m[1];
			$iPagename = addslashes($ipagename);
			$LinkPool = array();
			$r = "<ol class=\"linkdown\">\n".linkdown($iPagename, $depth, $depth)."</ol>";
			$str = preg_replace("\x01\\\\LinkDown".preg_quote($m[1][$i])."(?![0-9a-zA-Z])\x01", $r, $str);
		}
		if(strpos($str, "\\") === false)
			return $str;
	}
	if(($n = preg_match_all("/\\\\Calendar(@?\*?(?:\{.*?(?<!\\\\)\}\{.*?(?<!\\\\)\})?[0-9]*(?:-[0-9]*)?)(?![a-zA-Z])/", $str, $m))){
		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}
		for($i=0; $i<$n; $i++){
			$info = clean4plugin($m[1][$i]);
			$r = calendar($info);
			$str = preg_replace("\x01\\\\Calendar".preg_quote($m[1][$i])."(?![0-9a-zA-Z])\x01", $r, $str);
		}
		if(strpos($str, "\\") === false)
			return $str;
	}
	if(($n = preg_match_all("/\\\\StripTags\{(.*?)(?<!\\\\)\}/", $str, $m))){
		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}
		for($i=0; $i<$n; $i++){
			$r = strip_tags($m[1][$i]);
			$str = preg_replace("\x01\\\\StripTags\{".preg_quote($m[1][$i])."\}\x01", $r, $str);
		}
		if(strpos($str, "\\") === false)
			return $str;
	}
	if(($n = preg_match_all("/\\\\DisplayPage\{(.*?)(?<!\\\\)\}/", $str, $m))){
		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}
		for($i=0; $i<$n; $i++){
			$page = clean4plugin($m[1][$i]);
			$Page = addslashes($page);
			$r = "";
#unlimitedpool:			if(/*
			if(!in_array($page, $DisplayPool) &&
#unlimitedpool:			*/
				($id = pageid($Page)) && ($admin ||
				 !(is_hidden($Page) || is_site_hidden()))){
				$query = "select data.content from page, data
						where page.id=$id and
						data.id=page.id and
						data.version=page.version";
				$result = pm_query($db, $query);
				$content = pm_fetch_result($result, 0, 0);
				pm_free_result($result);
				$displaypool[] = $page;
				ob_start();
				DisplayContent("$wikiXheader$content$wikiXfooter");
				$r = ob_get_contents();
				ob_end_clean();
				$r = str_replace("\\", "\x03", $r);
			}
			$str = preg_replace("\x01\\\\DisplayPage\{".preg_quote($m[1][$i])."\}\x01", $r, $str);
		}
		if(strpos($str, "\\") === false)
			return $str;
	}
	if(($n = preg_match_all("/\\\\iphp\{(.*?)(?<!\\\\)\}/", $str, $m))){
		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}
		for($i=0; $i<$n; $i++){
			$r = php(clean4plugin($m[1][$i]), 1);
			$str = preg_replace("\x01\\\\iphp\{".preg_quote($m[1][$i])."\}\x01", $r, $str);
		}
		if(strpos($str, "\\") === false)
			return $str;
	}

	return $str;
}

function subs($str){
	global	$wikiXversion, $pagename0, $dpagename, $pageName, $pagenamE,
		$npages, $Y, $M, $D, $today, $now, $timestamp, $btime,
		$author, $ip;

	if(strpos($str, "\\") === false)
		return $str;

	$pattern = array(
		"/\\\\[pP]agename0(?![a-zA-Z])/",
		"/\\\\[pP]ageName(?![a-zA-Z])/",
		"/\\\\[pP]agenamE(?![a-zA-Z])/",
		"/\\\\n[pP]ages(?![a-zA-Z])/",
		"/\\\\y(?![a-z])/i",
		"/\\\\m(?![a-z])/i",
		"/\\\\d(?![a-z])/i",
		"/\\\\t(?![a-z])/i",
		"/\\\\s(?![a-z])/i",

		"/\\\\n(?![a-zA-Z])/",
		"/\\\\p(?![a-zA-Z])/",
		"/\\\\[aA]uthor(?![a-z])/",
		"/\\\\[pP]agename(?![a-zA-Z])/",
		"/\\\\[vV]ersion(?![a-zA-Z])/",
		"/\\\\[tT]oday(?![a-zA-Z])/",
		"/\\\\[nN]ow(?![a-zA-Z])/",
		"/\\\\[tT]imestamp(?![a-zA-Z])/",
		"/\\\\[bB]time(?![a-zA-Z])/",
		"/\\\\(?:smalltoday|SmallToday)(?![a-zA-Z])/",
		"/\\\\(?:smallnow|SmallNow)(?![a-zA-Z])/",
		"/\\\\(?:ip|IP)(?![a-zA-Z])/",
	);
	$replace = array(
		$pagename0,
		$pageName,
		$pagenamE,
		$npages,
		$Y,
		$M,
		$D,
		$today,
		$timestamp,

		"<br class=\"br\" />",
#		"<p class=\"p\"></p>",
		"<p class=\"p\">",
		$author,
		$dpagename,
		$wikiXversion,
		$today,
		$now,
		$timestamp,
		$btime,
		"<small class=\"small\">$today</small>",
		"<small class=\"small\">$now</small>",
		$ip,
	);
	$str = preg_replace($pattern, $replace, $str);

	return $str;
}

function table($str, &$isblock, &$table, &$ntdattrs, &$done){
	global	$tableAttr;

	$done = 0;
	if($str == "" || $str[0] != "|"){
		$str = "";
		if($isblock&0x1){
			$isblock &= ~0x1;
			$str = "</table>\n";
		}
		return $str;
	}
	$done = 1;

	if(preg_match("/^\|=(.*?)$/", $str, $m)){
		if(preg_match("/^(.*?)\|(.*?)(\|.*)$/", $m[1], $_m)){
			$ntdattrs = preg_match_all("/\|([^|]*)/", $_m[3], $m);
			$table = $m[1];
			array_unshift($table, $_m[1], $_m[2]);
		}else{
			$table = $tableAttr;
			$ntdattrs = 1;
		}
		return "";
	}

	$str = str_replace("\x02|", "\x07", $str);
	$str = str_replace("\\|", "\x07", $str);
	$n = preg_match_all("/(\|+)(v*)(\^|&lt;|&gt;)?([^|]*)/", $str, $m);
	$str = "";
	if(!($isblock&0x1)){
		$done = 2;
		$isblock |= 0x1;
		$str = pre("", $isblock, $i);
		if($str != "")
			$done = 3;
		$str .= "<table$table[0]>\n";
	}
	$str .= "<tr$table[1]>\n";
	for($i=0; $i<$n; $i++){
		$lc = strlen($m[1][$i]);
		$lr = strlen($m[2][$i]) + 1;
		$tdattr = ($i<$ntdattrs?$table[2+$i]:$table[1+$ntdattrs]);

		$m[4][$i] = trim($m[4][$i]);
		$str .= "<td$tdattr".($lr>1?" rowspan=\"$lr\"":"").
			($lc>1?" colspan=\"$lc\"":"").
			($m[3][$i]=="^"?" align=\"center\"":
			($m[3][$i]=="&gt;"?" align=\"right\"":
			($m[3][$i]=="&lt;"?" align=\"left\"":
			 ""))).">".$m[4][$i]."</td>\n";
	}
	$str .= "</tr>";
	$str = str_replace("\x07", "|", $str);
	return $str;
}

function pre($str, &$isblock, &$done){
	$done = 0;
	if($str == "" || ($str[0] != " " && $str[0] != "\t")){
		$str = "";
		if($isblock&0x2){
			$isblock &= ~0x2;
			$str = "</pre>\n";
		}
		return $str;
	}
	$done = 1;

	if(!($isblock&0x2)){
		$done = 2;
		$isblock |= 0x2;
		$str = "<pre class=\"pre\">\n$str";
	}
	return $str;
}

function pagelist($query, $start, $ninfos, $order, $color = 0){
	global	$db, $admin, $author, $login, $btime, $now, $pageName,
		$limitlist;

	$query = str_replace("\x02", $limitlist[0], $query);
	$result = pm_query($db, $query);
	if(!($n = pm_num_rows($result))){
		pm_free_result($result);
		return "";
	}

	$start += 0;
	if($ninfos == "")
		$end = $start + $n;
	else
		$end = $start + $ninfos;
	if($end < 1 || $end > $n)
		$end = $n;
	$nr = $end - $start;
	if($nr < 1)
		return "";

	$index = range(0, $n-1);
	if($order == 1)
		shuffle($index);
	else
	if($order == 2)
		$index = array_reverse($index);

	$list = 0;
	$mode = 0;
	$tag1 = "";
	$tag2 = "";
	$tag3 = "";
	$tag4 = "";

	if($ninfos[0] === "0" &&
			preg_match("/^(0+)/", $ninfos, $_m)){
		switch("x$_m[1]"){
		case "x0":
			$mode = 1;
			$tag2 = "<tr><td";
			$tag3 = "</td></tr>";
			break;
		case "x00":
			$mode = 2;
			$tag1 = "<tr>\n";
			$tag2 = "<td";
			$tag3 = "</td>";
			$tag4 = "</tr>\n";
			break;
		case "x000":
			$list = 1;
			break;
		case "x0000":
			$list = 1;
			$mode = 1;
			break;
		case "x00000":
			$list = 1;
			$mode = 2;
			break;
		case "x000000":
			$list = 2;
			break;
		case "x0000000":
			$list = 2;
			$mode = 1;
			break;
		case "x00000000":
			$list = 3;
			$mode = 1;
			break;
		}
		if($list)
			$color = 0;
	}

	$str = "";
	if($list < 2){
		if($list){
			if($mode == 2)
				$str .= "<ul class=\"pagelist\">\n";
			else
				$str .= "<ol class=\"pagelist\">\n";
		}else{
			$str .=
"<table class=\"pagelist\">\n".($mode?$tag1:
"<tr class=\"pagelist_header\">".
"<td>&nbsp;<span class=\"table_header\">Page</span>".(!$ninfos||$ninfos>$nr?" ($nr page".($nr>1?"s":"").")":"")."&nbsp;</td>".
"<td>&nbsp;<span class=\"table_header\">Author</span>&nbsp;</td>".
($admin?"<td>&nbsp;<span class=\"table_header\">IP</span>&nbsp;</td>":"").
"<td>&nbsp;".
($login?"<a class=\"a\" href=\"index.php?".strtotime($now).",bookmark=$pageName\">":"")."<span class=\"table_header\">Last Modifie".
($login?"</span></a><a class=\"a\" href=\"index.php?bookmark=$pageName\"><span class=\"table_header\">":"")."d</span>".($login?"</a>":"")."&nbsp;</td>".
"<td align=\"right\">&nbsp;<span class=\"table_header\">H</span>&nbsp;</td>".
"<td align=\"right\">&nbsp;<span class=\"table_header\">F</span>&nbsp;</td>".
"<td align=\"right\">&nbsp;<span class=\"table_header\">T</span>&nbsp;</td>".
"<td>&nbsp;<span class=\"table_header\">L".($admin?"<span class=\"emphasized\">H</span>":"")."</span>&nbsp;</td>".
"</tr>\n");
		}
	}
	$date = "";
	$class = ($color?"outdated":"recent");
	for($i=$start; $i<$end; $i++){
		$data = pm_fetch_array($result, $index[$i]);
		list($mdate, $mtime) = explode(" ", $data['mtime']);
		if($color && $date !== $mdate){
			$class = ($class=="outdated"?"recent":"outdated");
			$date = $mdate;
		}
		$bmtime = strtotime($data['mtime']);

		$ipagename = $data['name'];
		$ipageName = geni_urlencode($ipagename);
		$ipagename = str_replace("\\", "\x03", $ipagename);
		$iauthor = geni_urlencode($data['author']);

		$bclass = "general";
		if($data['deleted'] == 1 || $data['deleted'] == "t"){
			if($btime != "" && $data['mtime'] > $btime)
				$bclass = "deleted";
			else
				$bclass = "deleted0";
			$action = "goto";
			$w = split_word($ipagename);
			$w[0] = geni_specialchars($w[0]);
			$w[1] = geni_specialchars($w[1]);
		}else{
			if($btime != "" && $data['mtime'] > $btime){
				if($data['ctime'] > $btime)
					$bclass = "new";
				else
					$bclass = "updated";
			}
			$action = "display";
			$w[0] = geni_specialchars($ipagename);
			$w[1] = "";
		}

		if(!$mode){
			$query = "select count(linkfrom) from link
						where linkfrom=$data[id]";
			$result0 = pm_query($db, $query);
			$linkfrom = pm_fetch_result($result0, 0, 0);
			pm_free_result($result0);
			$iPagename = addslashes($data['name']);
			$query = "select count(linkto) from link
						where linkto=$data[id]
						or linktoname='$iPagename'";
			$result0 = pm_query($db, $query);
			$linkto = pm_fetch_result($result0, 0, 0);
			pm_free_result($result0);
		}

		if($list > 1){
			$str .=
"<a class=\"wikiword_$action\" href=\"index.php?$action=$ipageName\"".
($mode?" title=\"$data[author] ... $data[mtime]\"":"").">$w[0]</a>$w[1]".
($mode?"":" ... ".
(pageid($data['author'])?
"<a class=\"a\" href=\"index.php?display=$iauthor\">$data[author]</a>":$data['author']).
" ... <small class=\"small\">".($login?"<a class=\"a\" href=\"index.php?$bmtime,bookmark=$pageName\"><span class=\"$bclass\">":"").$mdate.($login?"</span></a>":"")." <a class=\"a\" href=\"index.php?diff=$ipageName\"><span class=\"$bclass\">$mtime</span></a></small>").
($i<$end-1?($list==2?"<br class=\"br\" />\n":", "):"");
		}else
		if($list){
			$str .=
"<li><a class=\"wikiword_$action\" href=\"index.php?$action=$ipageName\"".
($mode?" title=\"$data[author] ... $data[mtime]\"":"").">$w[0]</a>$w[1]".
($mode?"":" ... ".
(pageid($data['author'])?
"<a class=\"a\" href=\"index.php?display=$iauthor\">$data[author]</a>":$data['author']).
" ... <small class=\"small\">".($login?"<a class=\"a\" href=\"index.php?$bmtime,bookmark=$pageName\"><span class=\"$bclass\">":"").$mdate.($login?"</span></a>":"")." <a class=\"a\" href=\"index.php?diff=$ipageName\"><span class=\"$bclass\">$mtime</span></a></small>").
"</li>\n";
		}else{
			$str .=
($mode?
"$tag2 class=\"pagelist_$class\">&nbsp;<a class=\"wikiword_$action\" href=\"index.php?$action=$ipageName\" title=\"$data[author] ... $data[mtime]\">$w[0]</a>$w[1] <i>(<a class=\"a\" href=\"index.php?diff=$ipageName\"><span class=\"$bclass\">diff</span></a>)</i>&nbsp;$tag3\n":
"<tr class=\"pagelist_$class\">".
"<td>&nbsp;<a class=\"wikiword_$action\" href=\"index.php?$action=$ipageName\">$w[0]</a>$w[1]&nbsp;</td>".
"<td>&nbsp;".
(pageid($data['author'])?
"<a class=\"a\" href=\"index.php?display=$iauthor\">$data[author]</a>":$data['author']).
"&nbsp;</td>".
($admin?"<td>&nbsp;$data[ip]&nbsp;</td>":"").
"<td>&nbsp;".($login?"<a class=\"a\" href=\"index.php?$bmtime,bookmark=$pageName\"><span class=\"$bclass\">":"").$mdate.($login?"</span></a>":"")." <a class=\"a\" href=\"index.php?diff=$ipageName\"><span class=\"$bclass\">$mtime</span></a>&nbsp;</td>".
"<td align=\"right\">&nbsp;<a class=\"a\" href=\"index.php?info=$ipageName\">$data[hits]</a>&nbsp;</td>".
"<td align=\"right\">&nbsp;<a class=\"a\" href=\"index.php?links1=$ipageName\">$linkfrom</a>&nbsp;</td>".
"<td align=\"right\">&nbsp;<a class=\"a\" href=\"index.php?links2=$ipageName\">$linkto</a>&nbsp;</td>".
"<td>&nbsp;".($data['locked']?"L":"").($data['hidden']?"<span class=\"emphasized\">H</span>":"")."&nbsp;</td>".
"</tr>\n");
		}
	}
	pm_free_result($result);
	if($list < 2){
		if($list){
			$str .= ($mode==2?"</ul>":"</ol>");
		}else{
			$str .= "$tag4</table>";
		}
	}

	return $str;
}

function replace_toc($page){
	if(!(($nt = preg_match_all("/\x04\(([0-9]*(?:-[0-9]*)?)\)\x04/", $page, $mt)) && preg_match("'<h[1-6](?: .*?)?>.*<a name=\"wikiXheading([0-9]*)_[0-9]+\" class=\"totoc0\" href=\"#wikiXtoc\\1_\">&nbsp;</a><a class=\"totoc\" href=\"#wikiXtoc\">&nbsp;</a></h[1-6]>'", $page)))
		return preg_replace(
				"/\x04\([0-9]*(?:-[0-9]*)?\)\x04/", "", $page);

	if($nt > 1){
		$mt[1] = array_values(array_unique($mt[1]));
		$nt = count($mt[1]);
	}

	$nh = preg_match_all("'<h([1-6])(?: .*?)?>(.*)<a name=\"wikiXheading([0-9]*)_([0-9]+)\" class=\"totoc0\" href=\"#wikiXtoc\\3_\">&nbsp;</a><a class=\"totoc\" href=\"#wikiXtoc\">&nbsp;</a></h[1-6]>'", $page, $mh);

	for($i=0; $i<$nh; $i++){
		$g = $mh[3][$i];
		$s = $mh[4][$i];
		$j = $s - 1;
		$h0[$g][0][$j] = $mh[1][$i];
		$h0[$g][1][$j] = "${g}_$s";
		$h0[$g][2][$j] = $mh[2][$i];
		$h[0][$i] = $mh[1][$i];
		$h[1][$i] = "${g}_$s";
		$h[2][$i] = $mh[2][$i];
	}

	for($it=0; $it<$nt; $it++){
		$tocfmt = $mt[1][$it];
		if(strpos($tocfmt, "-") === false){
			$grp = 0;
			$toctype = $tocfmt;
			$tocgrp = "[0-9]*";
			$toc = "<a name=\"wikiXtoc\"></a>\n";
			$n = count($h[0]);
			$m = $h;
		}else{
			$grp = 1;
			list($toctype, $tocgrp) = explode("-", $tocfmt);
			$toc = "<a name=\"wikiXtoc${tocgrp}_\"></a>\n";
			if(!isset($h0[$tocgrp]) ||
			   !($n = count($h0[$tocgrp][0])))
				continue;
			$m = $h0[$tocgrp];
		}
		$type = ($toctype==""?0:$toctype+1);
		$BLI = "";
		$ELI = "";
		$bli = "";
		$eli = "";
		if($type != 1){
			switch($type){
			case 0:
				$toc .= "<ol class=\"toc\">\n";
				$BLI = "<ol>\n";
				$ELI = "</ol>\n";
				$bli = "<li>";
				$eli = "</li>\n";
				break;
			case 2:
				$toc .= "<ul class=\"toc\">\n";
				$BLI = "<ul>\n";
				$ELI = "</ul>\n";
				$bli = "<li>";
				$eli = "</li>\n";
				break;
			case 3:
				$toc .= "<dl class=\"toc\">\n";
				$BLI = "<dl>\n";
				$ELI = "</dl>\n";
				$bli = "<dt>";
				$eli = "</dt>\n";
				break;
			case 4:
				$toc .= "<dl class=\"toc\">\n";
				$BLI = "<dl>\n";
				$ELI = "</dl>\n";
				$bli = "<dt>";
				$eli = "</dt>\n";
				break;
			}
		}
		$mindepth = min($m[0]);
		$depth = 0;
		$sec = "";
		if($type > 2)
			$tag = array();
		for($i=0; $i<$n; $i++){
			$idepth = $m[0][$i] - $mindepth;
			if($type != 1){
				if($idepth > $depth){
					if($type > 2)
						$tag[$idepth] = 0;
					for($j=0; $j<$idepth-$depth; $j++)
						$toc .= $BLI;
				}else
				if($idepth < $depth){
					for($j=0; $j<$depth-$idepth; $j++)
						$toc .= $ELI;
				}
				$depth = $idepth;
				if($type > 2){
					$tag[$depth]++;
					$sec = "&nbsp;&nbsp;&nbsp;";
					if($type == 4){
						$Sec = "";
						for($j=0; $j<=$depth; $j++)
							$Sec .= ($tag[$j]+0).".";
						$sec .= "$Sec$sec";
						$page = preg_replace("\x01(<h".$m[0][$i]."(?: .*?)?>)(".preg_quote($m[2][$i])."<a name=\"wikiXheading".$m[1][$i]."\" class=\"totoc0\" href=\"#wikiXtoc${tocgrp}_\">&nbsp;</a><a class=\"totoc\" href=\"#wikiXtoc\">&nbsp;</a></h".$m[0][$i].">)\x01", "\\1\x07$Sec \\2", $page);
					}
				}
			}else
			if($i > 0){
				$depth = $idepth;
				$toc .= ($depth==0?" | ":
					($depth==1?" / ":
					($depth==2?" * ":
					($depth==3?" = ":
					($depth==4?" - ":
					 " . ")))));
			}
			if($m[2][$i] == "")
				$t = "----";
			else{
				$t = preg_replace("/<(img[ \t].+?)>/i",
					"\x02&lt;\\1\x02&gt;", $m[2][$i]);
				$t = strip_tags($t);
				$t = str_replace("\x02&lt;", "<",
					str_replace("\x02&gt;", ">", $t));
			}
			$toc .= ($type==1?"":"$bli$sec").
				"<a class=\"toc\" href=\"#wikiXheading".
				$m[1][$i]."\">$t</a>".($type==1?"":$eli);
		}
		if($type != 1){
			$depth++;
			for($j=0; $j<$depth; $j++)
				$toc .= $ELI;
			if($type > 2)
				$page = str_replace("\x07", "", $page);
		}
		$page = str_replace("\x04($tocfmt)\x04", $toc, $page);
	}
	$page = preg_replace("/\x04\([0-9]*(?:-[0-9]*)\)\x04/", "", $page);

	return $page;
}

$euri = escape_html(escape_bracket(escape_misc(
				str_replace("\\", "\x03", $uri))));
?>
