<?
function opendb(&$db, $dbhost, $dbname, $dbuser, $dbpass){
	$db = pm_connect($dbhost, $dbname, $dbuser, $dbpass) or
		die("Database open failed.");
}

function closedb(&$db){
	pm_close($db) or die("Database close failed.");
	$db = 0;
}

function pageid0($Pagename){
	global	$db, $db_;

	$query = "select id from ${db_}page where name='$Pagename'";
	$result = pm_query($db, $query);
	$ret = 0;
	if(pm_num_rows($result))
		$ret = pm_fetch_result($result, 0, 0);
	pm_free_result($result);

	return $ret;
}

function pageid($Pagename){
	global	$db, $db_;

	$query = "select ${db_}page.id from ${db_}page, ${db_}data
			where ${db_}page.id=${db_}data.id
			and ${db_}page.version=${db_}data.version
			and ${db_}page.name='$Pagename'
			and ${db_}data.content!='\x01'";
	$result = pm_query($db, $query);
	$ret = 0;
	if(pm_num_rows($result))
		$ret = pm_fetch_result($result, 0, 0);
	pm_free_result($result);
	return $ret;
}

function pagename($pageid){
	global	$db, $db_;

	$query = "select name from ${db_}page where id=$pageid";
	$result = pm_query($db, $query);
	$ret = "";
	if(pm_num_rows($result))
		$ret = pm_fetch_result($result, 0, 0);
	pm_free_result($result);
	return $ret;
}

function add_file($file){
	global	$db, $db_, $Pagename, $author, $ip;
	static	$once = 1, $v;

	if($once){
		$v = 0;
		if(($pageid = pageid($Pagename))){
			$query = "select version from ${db_}page
							where id=$pageid";
			$result = pm_query($db, $query);
			$v = pm_fetch_result($result, 0, 0);
			pm_free_result($result);
		}
		$once = 0;
	}
	$i = 1;
	$query = "select max(id) from ${db_}file";
	$result = pm_query($db, $query);
	if(pm_num_rows($result))
		$i = pm_fetch_result($result, 0, 0) + 1;
	pm_free_result($result);

	$f = addslashes($file);
	$m = date("Y-m-d H:i:s");
	$query = "insert into ${db_}file
		(id, file, page, version, author, ip, mtime)
		values($i, '$f', '$Pagename', $v, '$author', '$ip', '$m')";
	$result = pm_query($db, $query);
}

function uploadedfiles($Pagename, $table = 0){
	global	$db, $db_, $admin, $login, $author, $wikiXdir, $caseinsensitiveSearch;

	if(!$admin &&
	   (($Pagename != "" && is_hidden($Pagename)) || is_site_hidden()))
		return "";

	$query = "select ".($Pagename==""?"page, ":"").
		"id, file, author, ip, mtime, version from ${db_}file ".
		($Pagename==""?"":"where page='$Pagename' ").
		"order by page, file";
	$result = pm_query($db, $query);
	$p = "";
	$hidden = 0;
	$locked = 0;
	if($Pagename != ""){
		$pageName = geni_urlencode(stripslashes($Pagename));
		$locked = is_locked($Pagename);
	}
	$opened = 0;
	if($table){
		$tag = "table class=\"pagelist\"";
		$tag0 = "table";
	}else
		$tag = $tag0 = "ol";
	$n = pm_num_rows($result);
	$ret = "";
	if($n)
		$ret .= ($table&&$Pagename!=""?"<$tag>\n":"<ol>\n");
	for($i=0,$j=0; $i<$n; $i++){
		$data = pm_fetch_array($result, $i);
		$deleted = 0;
		if(!file_exists("$wikiXdir/$data[file]")){
			$query = "delete from ${db_}file where id=$data[id]";
			$result0 = pm_query($db, $query);

			$d = dirname($data['file']);
			while($d != "" && $d != "file" && $d != "file0" &&
				rmdir("$wikiXdir/$d"))
				$d = dirname($d);
			$deleted = 1;
		}
		if($Pagename == ""){
			if($p != $data['page']){
				if($opened){
					$ret .= "</$tag0>\n</li>\n";
					$opened = 0;
				}
				$p = $data['page'];
				$iPagename = addslashes($p);
				$ipagename = geni_specialchars($p);
				$ipageName = geni_urlencode($p);
				$hidden = is_hidden($iPagename);
				if($hidden && !$admin)
					continue;
				$locked = is_locked($iPagename);
				if(pageid($iPagename))
					$ret .= "<li><a class=\"wikiword_display\" href=\"index.php?display=$ipageName\">$ipagename</a>";
				else{
					$w = split_word($p);
					$w[0] = geni_specialchars($w[0]);
					$w[1] = geni_specialchars($w[1]);
					$ret .= "<li><a class=\"wikiword_goto\" href=\"index.php?goto=$ipageName\">$w[0]</a>$w[1] <span class=\"emphasized\">".(pageid0($iPagename)?"deleted":"nonexistent")."</span>";
				}
				$ret .= " ... <small class=\"small\">".($locked?"L":"").($hidden?"<span class=\"emphasized\">H</span>":"").($locked||$hidden?" ":"")."<a class=\"a\" href=\"index.php?files=$ipageName\">files</a></small>\n<$tag>\n";
				$j = 0;
				$opened = 1;
			}else
			if($hidden && !$admin)
				continue;
		}
		$filename = geni_specialchars($data['file']);
		$fileName = geni_urlencode($data['file']);
		$filesize = filesize($data['file']);
		$s = number_format($filesize);
		$size = "$s byte".($filesize>1?"s":"");
		$fn = geni_urlencode($fileName);
		$f = str_replace("%26", "%5Cx26",
			str_replace("%7C", "%5Cx7c",
			str_replace("%28", "%5Cx28",
			str_replace("%29", "%5Cx29",
			str_replace("%5C", "%5Cx5c", $fileName)))));
		$search = "$fn".($fn!=$f?" | $f/":"/").
			($caseinsensitiveSearch?"i":"");
		$fN = basename($fileName);
		$fn = geni_urlencode($fN);
		$f = str_replace("%26", "%5Cx26",
			str_replace("%7C", "%5Cx7c",
			str_replace("%28", "%5Cx28",
			str_replace("%29", "%5Cx29",
			str_replace("%5C", "%5Cx5c", $fN)))));
		$search2 = "$fn".($fn!=$f?" | $f/":"/").
			($caseinsensitiveSearch?"i":"");
		if($table){
			if(!$j++)
				$ret .=
"<tr class=\"pagelist_header\">".
"<td align=\"right\">&nbsp;<span class=\"table_header\">Id</span>&nbsp;</td>".
"<td>&nbsp;<span class=\"table_header\">File</span>&nbsp;</td>".
"<td align=\"right\">&nbsp;<span class=\"table_header\">Size</span>&nbsp;</td>".
"<td>&nbsp;<span class=\"table_header\">Uploaded Time</span>&nbsp;</td>".
"<td>&nbsp;<span class=\"table_header\">Author</span>&nbsp;</td>".
"<td>&nbsp;<span class=\"table_header\">Search</span>&nbsp;</td>".
"<td>&nbsp;<span class=\"table_header\">Delete</span>&nbsp;</td>".
"</tr>\n";
			$ret .=
"<tr>".
"<td align=\"right\">&nbsp;$data[id]&nbsp;</td>".
"<td>&nbsp;<a class=\"a\" href=\"$fileName\">$filename</a>&nbsp;</td>".
"<td align=\"right\">&nbsp;$s&nbsp;</td>".
"<td>&nbsp;<a class=\"general\" title=\"v$data[version]\">$data[mtime]</a>&nbsp;</td>".
"<td>&nbsp;<a ".(pageid($data['author'])?"class=\"a\" href=\"index.php?display=".geni_urlencode($data['author'])."\"":"class=\"general\"")." title=\"$data[ip]\">$data[author]</a>&nbsp;</td>".
"<td>&nbsp;<a class=\"a\" href=\"index.php?search=$search\">search</a> <a class=\"a\" href=\"index.php?search=$search2\">search2</a>&nbsp;</td>".
"<td>&nbsp;".($deleted?"<span class=\"emphasized\">deleted!</span>":($admin||($login&&$author==$data['author']&&!$locked)?"<a class=\"a\" href=\"index.php?$data[id],files=".($Pagename==""?"%02":$pageName)."\">delete</a>":"delete"))."&nbsp;</td>".
"</tr>\n";
		}else
			$ret .=
"<li><a class=\"a\" href=\"$fileName\" ".
"title=\"$size $data[mtime] v$data[version]:$data[author]\">$filename</a>".
" ... <small class=\"small\">".
"<a class=\"a\" href=\"index.php?search=$search\">search</a> ".
"<a class=\"a\" href=\"index.php?search=$search2\">search2</a>".
($deleted?" <span class=\"emphasized\">deleted!</span>":
($admin||($login&&$author==$data['author']&&!$locked)?
" <a class=\"a\" href=\"index.php?$data[id],files=".
($Pagename==""?"%02":$pageName)."\">delete</a>":"")).
"</small></li>\n";
	}
	if($n)
		$ret .= ($opened?"</$tag0>\n</li>\n":"").
			($table&&$Pagename!=""?"</table>\n":"</ol>\n");
	$ret = str_replace("\\", "\x03", $ret);
	pm_free_result($result);
	return $ret;
}

function linkup($Pagename, $depth, $idepth){
	global	$db, $db_, $admin, $LinkPool;

	$pagename0 = stripslashes($Pagename);
	$pagename = str_replace("\\", "\x03", $pagename0);
	$pageName = geni_urlencode($pagename0);
	$pagenamE = escape_doit($pagename0);

	if(!$admin && (is_hidden($Pagename) || is_site_hidden()))
		return "";

	if(($pageid = pageid($Pagename)))
		$ret = "<li><a class=\"a\" href=\"index.php?1,doit=\x03LinkUp\{$pagenamE\}$depth\">&lt;</a><a class=\"wikiword_display\" href=\"index.php?display=$pageName\">".geni_specialchars($pagename)."</a><a class=\"a\" href=\"index.php?1,doit=\x03LinkDown\{$pagenamE\}$depth\">&gt;</a>";
	else{
		$w = split_word($pagename);
		$w[0] = geni_specialchars($w[0]);
		$w[1] = geni_specialchars($w[1]);
		$ret = "<li><a class=\"a\" href=\"index.php?1,doit=\x03LinkUp\{$pagenamE\}$depth\">&lt;</a><a class=\"wikiword_goto\" href=\"index.php?goto=$pageName\">$w[0]</a>$w[1]<a class=\"a\" href=\"index.php?1,doit=\x03LinkDown\{$pagenamE\}$depth\">&gt;</a>";
	}

	if(in_array($pagename0, $LinkPool)){
		$ret .= "...</li>\n";
		return $ret;
	}
	$LinkPool[] = $pagename0;
	$ret .= "</li>\n";

	if(--$idepth){
		if($pageid)
			$where = "${db_}link.linkto=$pageid";
		else
			$where = "${db_}link.linktoname='$Pagename'";

		$query = "select ${db_}page.name from ${db_}link, ${db_}page
			where $where and ${db_}link.linkfrom=${db_}page.id
			order by ${db_}page.name";
		$result = pm_query($db, $query);
		$nlinks = pm_num_rows($result);

		if($nlinks){
			$ret .= "<ol>\n";
			for($i=0; $i<$nlinks; $i++){
				$frompage = pm_fetch_result($result, $i, 0);
				$Frompage = addslashes($frompage);
				$ret .= linkup($Frompage, $depth, $idepth);
			}
			$ret .= "</ol>\n";
		}
		pm_free_result($result);
	}
	return $ret;
}

function linkdown($Pagename, $depth, $idepth){
	global	$db, $db_, $admin, $LinkPool;

	$pagename0 = stripslashes($Pagename);
	$pagename = str_replace("\\", "\x03", $pagename0);
	$pageName = geni_urlencode($pagename0);
	$pagenamE = escape_doit($pagename0);

	if(!$admin && (is_hidden($Pagename) || is_site_hidden()))
		return "";

	if(($pageid = pageid($Pagename)))
		$ret = "<li><a class=\"a\" href=\"index.php?1,doit=\x03LinkUp\{$pagenamE\}$depth\">&lt;</a><a class=\"wikiword_display\" href=\"index.php?display=$pageName\">".geni_specialchars($pagename)."</a><a class=\"a\" href=\"index.php?1,doit=\x03LinkDown\{$pagenamE\}$depth\">&gt;</a>";
	else{
		$w = split_word($pagename);
		$w[0] = geni_specialchars($w[0]);
		$w[1] = geni_specialchars($w[1]);
		$ret = "<li><a class=\"a\" href=\"index.php?1,doit=\x03LinkUp\{$pagenamE\}$depth\">&lt;</a><a class=\"wikiword_goto\" href=\"index.php?goto=$pageName\">$w[0]</a>$w[1]<a class=\"a\" href=\"index.php?1,doit=\x03LinkDown\{$pagenamE\}$depth\">&gt;</a>";
	}

	if(in_array($pagename0, $LinkPool)){
		$ret .= "...</li>\n";
		return $ret;
	}
	$LinkPool[] = $pagename0;
	$ret .= "</li>\n";

	if(!$pageid)
		return $ret;

	if(--$idepth){
		$query = "select ${db_}page.name from ${db_}link, ${db_}page
					where ${db_}link.linkfrom=$pageid
					and ${db_}link.linkto=${db_}page.id
					order by ${db_}page.name";
		$result1 = pm_query($db, $query);
		$nlinks1 = pm_num_rows($result1);

		$query = "select linktoname from ${db_}link
					where linkfrom=$pageid and linkto=0
					order by linktoname";
		$result2 = pm_query($db, $query);
		$nlinks2 = pm_num_rows($result2);

		$nlinks = $nlinks1 + $nlinks2;

		if($nlinks){
			$ret .= "<ol>\n";
			for($i=0; $i<$nlinks1; $i++){
				$topage = pm_fetch_result($result1, $i, 0);
				$Topage = addslashes($topage);
				$ret .= linkdown($Topage, $depth, $idepth);
			}
			for($i=0; $i<$nlinks2; $i++){
				$topage = pm_fetch_result($result2, $i, 0);
				$w = split_word($topage);
				$w[0] = geni_specialchars($w[0]);
				$w[1] = geni_specialchars($w[1]);
				$toPage = geni_urlencode($topage);
				$dtoPage = escape_doit($topage);
				$ret .= "<li><a class=\"a\" href=\"index.php?1,doit=\x03LinkUp\{$dtoPage\}$depth\">&lt;</a><a class=\"wikiword_goto\" href=\"index.php?goto=$toPage\">$w[0]</a>$w[1]<a class=\"a\" href=\"index.php?1,doit=\x03LinkDown\{$dtoPage\}$depth\">&gt;</a></li>\n";
			}
			$ret .= "</ol>\n";
		}
		pm_free_result($result1);
		pm_free_result($result2);
	}
	return $ret;
}

function redirect_to(&$pagename0, &$content, $edit, &$editpage){
	global	$db, $db_, $admin,
		$beginEditRedirectedPage, $sepEditRedirectedPage,
		$endEditRedirectedPage;
	static	$RedirectPool = array();

	if($pagename0 == "\x01"){
		$RedirectPool = array();
		return 0;
	}
	if(!preg_match("/^\\\\RedirectTo:([^\r\n]+)/", $content, $m)){
		if($edit && $editpage != "")
			$editpage .= $endEditRedirectedPage;
		return 0;
	}
	if(in_array($pagename0, $RedirectPool)){
		if($edit && $editpage != "")
			$editpage .= $endEditRedirectedPage;
		return 3;
	}
	if($edit && $pagename0 != "\x02"){
		if($editpage == "")
			$editpage = $beginEditRedirectedPage;
		else
			$editpage .= $sepEditRedirectedPage;
		$pagename = geni_specialchars($pagename0);
		$pageName = geni_urlencode($pagename0);
		$editpage .= "<a href=\"index.php?edit=$pageName\">$pagename</a><a href=\"index.php?links2=$pageName\">&nbsp;</a>";
	}
	$page0 = $m[1];
	$Page = addslashes($page0);
	if(!($pageid = pageid($Page)) || (!$admin && is_hidden($Page))){
		if($edit && $editpage != "")
			$editpage .= $endEditRedirectedPage;
		$pagename0 = $page0;
		return ($pageid?2:1);
	}

	if($pagename0 != "\x02")
		$RedirectPool[] = $pagename0;
	$pagename0 = $page0;
	$content = page_content($pageid, "${db_}page.version");
	return redirect_to($pagename0, $content, $edit, $editpage);
}

function include_page($pagename0, $content, $allowed = 0){
	global	$db, $db_, $bs, $admin, $action;
	static	$IncludePool = array();

	if($pagename0 == "\x01"){
		$IncludePool = array();
		return "";
	}

	if($action != "doit")
		$IncludePool[] = $pagename0;
	if(($n = preg_match_all("/^\\\\IncludePage:(.+)$/m", $content, $m))){
		if($n > 1){
			$m[1] = array_values(array_unique($m[1]));
			$n = count($m[1]);
		}
		for($i=0; $i<$n; $i++){
			$pagename0 = $m[1][$i];
			$Pagename = addslashes($pagename0);
			$r = "";
#unlimitedpool:			if(/*
			if(!in_array($pagename0, $IncludePool) &&
#unlimitedpool:			*/
				($pageid = pageid($Pagename)) &&
				($admin || $allowed ||
				 !(is_hidden($Pagename) || is_site_hidden()))){
				$query = "select ${db_}data.content
					from ${db_}page, ${db_}data
					where ${db_}page.id=$pageid and
					${db_}data.id=${db_}page.id and
					${db_}data.version=${db_}page.version";
				$result = pm_query($db, $query);
				$r = pm_fetch_result($result, 0, 0);
				pm_free_result($result);
				$r = str_replace("$", "\\$",
					str_replace("\\", $bs, $r));
			}
			$content = preg_replace("\x01^\\\\IncludePage:".preg_quote($m[1][$i])."$\x01m", $r, $content);
			$content = include_page($pagename0, $content, $allowed);
		}
	}
	return $content;
}

function is_locked($Pagename){
	global	$db, $db_, $mustLogin, $author, $ip;

	if($mustLogin && $author === $ip)
		return 1;
/*
	if(is_site_locked())
		return 1;
*/

	$query = "select locked from ${db_}page
				where name='$Pagename' and locked=1";
	$result = pm_query($db, $query);
	$ret = pm_num_rows($result);
	pm_free_result($result);
	return $ret;
}

function is_hidden($Pagename){
	global	$db, $db_;

/*
	if(is_site_hidden())
		return 1;
*/

	$query = "select hidden from ${db_}page
				where name='$Pagename' and hidden=1";
	$result = pm_query($db, $query);
	$ret = pm_num_rows($result);
	pm_free_result($result);
	return $ret;
}

function is_site_locked(){
	global	$db, $db_, $mustLogin, $author, $ip;

	if($mustLogin && $author === $ip)
		return 1;

	$query = "select locked from ${db_}site where locked=1";
	$result = pm_query($db, $query);
	$ret = pm_num_rows($result);
	pm_free_result($result);
	return $ret;
}

function is_site_hidden(){
	global	$db, $db_;

	$query = "select hidden from ${db_}site where hidden=1";
	$result = pm_query($db, $query);
	$ret = pm_num_rows($result);
	pm_free_result($result);
	return $ret;
}

function npages(){
	global	$db, $db_, $admin;

	$query = "select count(id) from ${db_}page".
				($admin?"":" where ${db_}page.hidden=0");
	$result = pm_query($db, $query);
	$ret = pm_fetch_result($result, 0, 0);
	pm_free_result($result);
	return $ret;
}

function btime(){
	global	$db, $db_, $login, $admin, $author;

	if(!$login)
		return "";
	$query = "select btime from ${db_}".($admin?"admindb":"userdb").
				" where id='$author'";
	$result = pm_query($db, $query);
	$ret = pm_fetch_result($result, 0, 0);
	pm_free_result($result);
	return $ret;
}

function page_content($pageid, $version){
	global	$db, $db_;

	$query = "select ${db_}data.content, ${db_}page.version as current
			from ${db_}page, ${db_}data
			where ${db_}data.id=$pageid
			and ${db_}data.id=${db_}page.id and
			${db_}data.version=${db_}page.version";
	$result = pm_query($db, $query);
	$data = pm_fetch_array($result, 0);
	pm_free_result($result);
	if($version == "${db_}page.version" || $version == $data['current'])
		return $data['content'];

	$content = $data['content'];
	for($v=$data['current']-1; $v>=$version; $v--){
		$query = "select content from ${db_}data
					where id=$pageid and version=$v";
		$result = pm_query($db, $query);
		$diff = pm_fetch_result($result, 0, 0);
		pm_free_result($result);
		if($content == "\x01")
			$content = $diff;
		else
			$content = patch($content, $diff);
	}
	return ($diff=="\x01"?"\x01":$content);
}

$backendDB = $dbBack.($dbBack=="pg"?$oldphp:"");
include_once("lib/$backendDB.php");
?>
