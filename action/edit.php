<?
if(invalid_access()){
    	echo "It's not allowed to access the page directly.\n";
	return;
}
if(!$admin){
	if(is_site_hidden()){
		echo "Sorry, it's a hidden site.\n";
		return;
	}else
	if(is_hidden($Pagename)){
		echo "$pagename: Sorry, it's a hidden page.\n";
		return;
	}
}
if($action == "edit"){
	if(isset($post['subaction']))
		$subaction = $post['subaction'];
	if(isset($post['lock']))
		$lock = $post['lock'];
	if(isset($post['hide']))
		$hide = $post['hide'];
	if(isset($post['version']))
		$version = $post['version'];
	if(isset($post['content']))
		$content = $post['content'];
}
$locked = 0;
$deleted = 0;
if((!$admin && (is_locked($Pagename) || is_site_locked())) ||
		($deleted=(!pageid($Pagename)&&pageid0($Pagename)))){
	$locked = 1;
	$subaction = "";
}

if(!isset($subaction)){
	$subaction = "";
	if($admin){
		$lock = ($pageLock?" checked":"");
		$hide = ($pageHide?" checked":"");
	}
}
if(isset($content))
	$content = geni_trim(str_replace("\r", "", $content));
else
	$content = "";
if(!$locked && $v0 != ""){
	$query = "select page.version from page, data
				where page.name='$Pagename' and
				data.id=page.id and data.version=$v0";
	$result = pm_query($db, $query);
	if(($r0 = pm_num_rows($result)))
		$r = pm_fetch_result($result, 0, 0);
	pm_free_result($result);
	if(!$r0){
		echo "<a class=\"wikiword_display\" href=\"index.php?display=$pageName\">$pagename</a> v$v0: No such page version found.\n";
		return;
	}
	if($subaction == "" && $v0 != $r)
		$locked = 1;
}

if($subaction != ""){
	$query = "select version from page where name='$Pagename'";
	$result = pm_query($db, $query);
	$n = pm_num_rows($result);
	if(($n && $version != pm_fetch_result($result, 0, 0)) ||
	   (!$n && $version)){
		warn("Someone has updated the page. Edit the new version.");
		$subaction = "Preview";
	}
	pm_free_result($result);
}

if($subaction == "" || $subaction == "Save"){
	$version="page.version";
	if($v0 != "")
		$version = $v0;
	$query = "select page.hits, page.locked, page.hidden,
			page.version as current,
			data.id, data.version, data.author, data.ip, data.mtime
			from page, data where page.name='$Pagename' and
			data.id=page.id and data.version=$version";
	$result = pm_query($db, $query);
	if(($r0 = pm_num_rows($result)))
		$data = pm_fetch_array($result, 0);
	pm_free_result($result);
	if($r0){
		$data['content'] = page_content($data['id'], $version);
		if($data['content'] == "\x01")
			$data['content'] = "";
		if($subaction == ""){
			$query = "select min(version) from data
						where id=$data[id]";
			$result = pm_query($db, $query);
			$minversion = pm_fetch_result($result, 0, 0);
			pm_free_result($result);
			$content = $data['content'];
			if(!$admin && $data['locked'])
				$content = hidecode($content);
		}else
		if($content === $data['content']){
#			warn("Page refreshed.");
#/*
			warn("No changes to save.");
			include_once("action/display.php");
			return;
#*/
		}
		$version = $data['version'];
	}else
		$version = 0;
}

if($subaction == "Save"){
/*
	if($content == ""){
		warn("No content to save.");
		include_once("action/display.php");
		return;
	}
*/

	$wikiXheader = include_page($pagename0, $wikiXheader);
	$wikiXfooter = include_page($pagename0, $wikiXfooter);
	$content = "$wikiXheader\n\x06\n$content\n\x06\n$wikiXfooter";
	$content = mystage0s($content);
	$content = preg_replace("/^(\\\\basis\n.*?\n\\\\easis)$/mse",
			"str_replace('$bs$bs', '\x02',
			str_replace('$bs\"', '\"',
			'\\1'))", $content);
	$content = preg_replace("/^(\\\\bnobs\n.*?\n\\\\enobs)$/mse",
			"str_replace('$bs$bs', '\x02',
			str_replace('$bs\"', '\"',
			'\\1'))", $content);
	$content = str_replace("\\\\", "\x03", $content);
	$content = str_replace("\\x09", "\x09", $content);

	$content = preg_replace("/\\\\\{(.*?)\\\\\}/se",
			"'$bs{'.str_replace('\n', '\x08',
				str_replace('$bs\"', '\"', '\\1')).'$bs}'",
			$content);
	$content = smacro($content);
	$content = str_replace("\x08", "\n", $content);

	$content = replace("sreplace", $content);
	$content = rreplace($content);

	$content = mystage1s($content);

	$m = explode("\n\x06\n", $content);
	$content = $m[1];

	$epagename = escape_wikix($pagename0);

	$pattern = array(
		"/^\\\\k([0-9]*)([=+-])([+-]?[0-9]*)(?:\n|$)|\\\\k([0-9]*)(?![a-zA-Z])/me",
		"/\\\\pagename0(?![a-zA-Z])/",
		"/\\\\pageName(?![a-zA-Z])/",
		"/\\\\pagenamE(?![a-zA-Z])/",
		"/\\\\npages(?![a-zA-Z])/",
		"/\\\\y(?![a-zA-Z])/",
		"/\\\\m(?![a-zA-Z])/",
		"/\\\\d(?![a-zA-Z])/",
		"/\\\\t(?![a-zA-Z])/",
		"/\\\\s(?![a-zA-Z])/",
		"/\\\\beginsmafi(?![a-zA-Z])[ \t]?(.*?)\\\\endsmafi(?![a-zA-Z])/se",
		"/\\\\sign(?:[ \t]+(.+))?[ \t]*$/me",
		"/\\\\author(?![a-zA-Z])/",
		"/\\\\pagename(?![a-zA-Z])/",
		"/\\\\version(?![a-zA-Z])/",
		"/\\\\today(?![a-zA-Z])/",
		"/\\\\now(?![a-zA-Z])/",
		"/\\\\timestamp(?![a-zA-Z])/",
		"/\\\\btime(?![a-zA-Z])/",
		"/\\\\smalltoday(?![a-zA-Z])/",
		"/\\\\smallnow(?![a-zA-Z])/",
		"/\\\\ip(?![a-zA-Z])/",
		"/\\\\\^\n/",
		"/\n?\\\\\^/",
	);
	$replace = array(
		"('x\\2'=='x'?k(\\4+0):k(\\1+0, '\\2', \\3+0))",
		$pagename0,
		$pageName,
		$pagenamE,
		$npages,
		$Y,
		$M,
		$D,
		$today,
		$timestamp,
		"mafi(str_replace('\x03', '$bs$bs', stripslashes('\\1')))",
		"'${bs}right -- ['.('x\\1'=='x'?'$author':'\\1').'] ${bs}smallnow'",
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
		"",
		"",
	);
	$content = preg_replace($pattern, $replace, $content);
	$content = str_replace("\x02", "\\", $content);
	$content = str_replace("\x03", "\\\\", $content);
	$content = geni_trim($content);

	if($content === $data['content']){
#		warn("Page refreshed.");
#/*
		warn("No changes to save.");
		include_once("action/display.php");
		return;
#*/
	}

	if($admin){
		$ilock = (isset($lock)&&$lock=="on"?1:0);
		$ihide = (isset($hide)&&$hide=="on"?1:0);
	}else{
		$ilock = 0;
		$ihide = 0;
	}

	if($version){
		$id = $data['id'];

		$diff = diff($content, $data['content']);
		$diff = addslashes($diff);
		$query = "update data set content='$diff'
				where id=$id and version=$version";
		$result = pm_query($db, $query);

		$version++;
		$query = "update page set version=$version,
				locked=$ilock, hidden=$ihide where id=$id";
		$result = pm_query($db, $query);
	}else{
		$id = 1;
		$version = 1;
		$query = "select max(id) from page";
		$result = pm_query($db, $query);
		if(pm_num_rows($result))
			$id = pm_fetch_result($result, 0, 0) + 1;
		pm_free_result($result);
		$query = "insert into page (id, name, cauthor, cip, ctime,
				hits, locked, hidden, tag, version)
				values($id, '$Pagename', '$author', '$ip',
				'$now', 0, $ilock, $ihide, 0, $version)";
		$result = pm_query($db, $query);
	}

	$content = addslashes($content);
	$query = "insert into data (id, version, author, ip, mtime, content)
				values($id, $version,
				'$author', '$ip', '$now', '$content')";
	$result0 = pm_query($db, $query);

	$id0 = $id;
	$Pagename0 = $Pagename;
	$v0 = "";

	include_once("action/display.php");

	if(!$result0)
		return;

	$query = "update link set linkto=$id0, linktoname=''
				where linktoname='$Pagename0'";
	$result = pm_query($db, $query);

	$query = "delete from link where linkfrom=$id0";
	$result = pm_query($db, $query);

	if($id == $id0){
		if(count($link) > 1)
			$link = array_values(array_unique($link));

		$nlinks = count($link);
		for($i=0; $i<$nlinks; $i++){
			if(preg_match("/^-(.*)$/", $link[$i], $m))
				$query = "insert into link
						(linkfrom, linkto, linktoname)
						values($id, 0,
						'".addslashes($m[1])."')";
			else
				$query = "insert into link
						(linkfrom, linkto, linktoname)
						values($id, $link[$i], '')";
			$result = pm_query($db, $query);
		}
	}

	return;
}

$id = pageid($Pagename);

if($subaction == "" && isset($data)){
	if($admin){
		$hits = "hit".($data['hits']>1?"s":"");
		$lock = ($data['locked']?" checked":"");
		$hide = ($data['hidden']?" checked":"");
	}else
	if($data['hits'] > 1)
		$hits = "hit<a class=\"general\" href=\"admin.php?$arg\">s</a>";
	else
		$hits = "hi<a class=\"general\" href=\"admin.php?$arg\">t</a>";
}

$title_action	= ($id?"display":"links2")."=$pageName";

if(isset($data)){
	$display_version = "$data[version],";
	if(preg_match("/^\\\\RedirectTo:([^\r\n]+)/", $data['content']))
		$display_version = "";
	$view_action	= ($id?"${display_version}display":"links2")."=$pageName";
	$diff_do	= ($data['version']>$minversion?
			"<a href=\"index.php?$data[version],diff=$pageName\">diff</a>":
			"diff");
	$edit		= ($subaction==""?1:0);
	$author_do	= (pageid($data['author'])?
			"<a href=\"index.php?display=$data[author]\">$data[author]</a>":
			$data['author']);
	$current_do	= ($deleted?
			" (<a href=\"index.php?display=$pageName\">deleted</a>)":
			($data['current']!=$data['version']?
			" (<a href=\"index.php?display=$pageName\">current</a>)":""));
}else{
	$view_action	= ($id?"display":"links2")."=$pageName";
	$diff_do	= "";
	$edit		= 0;
	$author_do	= "";
	$current_do	= "";
}

include_once("$mytheme/edit.php");
?>
