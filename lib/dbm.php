<?
function opendb(&$db, $dbhost, $dbname, $dbuser, $dbpass){
	$db = pm_connect($dbhost, $dbname, $dbuser, $dbpass) or die("Database open failed.");
}

function closedb($db){
	pm_close($db) or die("Database close failed.");
}

function pageid0($Pagename){
	global	$db;

	$query = "select id from page where name='$Pagename'";
	$result = pm_query($db, $query);
	$ret = 0;
	if(pm_num_rows($result))
		$ret = pm_fetch_result($result, 0, 0);
	pm_free_result($result);
	return $ret;
}

function pageid($Pagename){
	global	$db;

	$query = "select page.id from page, data
			where page.id=data.id and page.version=data.version
			and page.name='$Pagename' and data.content!='\x01'";
	$result = pm_query($db, $query);
	$ret = 0;
	if(pm_num_rows($result))
		$ret = pm_fetch_result($result, 0, 0);
	pm_free_result($result);
	return $ret;
}

function pagename($pageid){
	global	$db;

	$query = "select name from page where id=$pageid";
	$result = pm_query($db, $query);
	$ret = "";
	if(pm_num_rows($result))
		$ret = pm_fetch_result($result, 0, 0);
	pm_free_result($result);
	return $ret;
}

function linkup($Pagename, $depth, $idepth){
	global	$db, $admin, $LinkPool;

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
			$where = "link.linkto=$pageid";
		else
			$where = "link.linktoname='$Pagename'";

		$query = "select page.name from link, page
					where $where and link.linkfrom=page.id
					order by page.name";
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
	global	$db, $admin, $LinkPool;

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
		$query = "select page.name from link, page
					where link.linkfrom=$pageid
					and link.linkto=page.id
					order by page.name";
		$result1 = pm_query($db, $query);
		$nlinks1 = pm_num_rows($result1);

		$query = "select linktoname from link
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
	global	$db, $admin, $beginEditRedirectedPage, $sepEditRedirectedPage,
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
	$content = page_content($pageid, "page.version");
	return redirect_to($pagename0, $content, $edit, $editpage);
}

function include_page($pagename0, $content, $allowed = 0){
	global	$db, $bs, $admin, $action;
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
				$query = "select data.content from page, data
					where page.id=$pageid and
					data.id=page.id and
					data.version=page.version";
				$result = pm_query($db, $query);
				$r = pm_fetch_result($result, 0, 0);
				pm_free_result($result);
				$r = str_replace("\\", $bs, $r);
			}
			$content = preg_replace("\x01^\\\\IncludePage:".preg_quote($m[1][$i])."$\x01m", $r, $content);
			$content = include_page($pagename0, $content, $allowed);
		}
	}
	return $content;
}

function is_locked($Pagename){
	global	$db, $mustLogin, $author, $ip;

	if($mustLogin && $author === $ip)
		return 1;
/*
	if(is_site_locked())
		return 1;
*/

	$query = "select locked from page where name='$Pagename' and locked=1";
	$result = pm_query($db, $query);
	$ret = pm_num_rows($result);
	pm_free_result($result);
	return $ret;
}

function is_hidden($Pagename){
	global	$db;

/*
	if(is_site_hidden())
		return 1;
*/

	$query = "select hidden from page where name='$Pagename' and hidden=1";
	$result = pm_query($db, $query);
	$ret = pm_num_rows($result);
	pm_free_result($result);
	return $ret;
}

function is_site_locked(){
	global	$db, $mustLogin, $author, $ip;

	if($mustLogin && $author === $ip)
		return 1;

	$query = "select locked from site where locked=1";
	$result = pm_query($db, $query);
	$ret = pm_num_rows($result);
	pm_free_result($result);
	return $ret;
}

function is_site_hidden(){
	global	$db;

	$query = "select hidden from site where hidden=1";
	$result = pm_query($db, $query);
	$ret = pm_num_rows($result);
	pm_free_result($result);
	return $ret;
}

function npages(){
	global	$db, $admin;

	$query = "select count(id) from page".
				($admin?"":" where page.hidden=0");
	$result = pm_query($db, $query);
	$ret = pm_fetch_result($result, 0, 0);
	pm_free_result($result);
	return $ret;
}

function btime(){
	global	$db, $login, $admin, $author;

	if(!$login)
		return "";
	$query = "select btime from ".($admin?"admindb":"userdb").
				" where id='$author'";
	$result = pm_query($db, $query);
	$ret = pm_fetch_result($result, 0, 0);
	pm_free_result($result);
	return $ret;
}

function page_content($pageid, $version){
	global	$db;

	$query = "select data.content, page.version as current from page, data
				where data.id=$pageid and data.id=page.id and
				data.version=page.version";
	$result = pm_query($db, $query);
	$data = pm_fetch_array($result, 0);
	pm_free_result($result);
	if($version == "page.version" || $version == $data['current'])
		return $data['content'];

	$content = $data['content'];
	for($v=$data['current']-1; $v>=$version; $v--){
		$query = "select content from data
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
