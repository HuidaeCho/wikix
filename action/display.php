<?
if(!$admin && is_site_hidden()){
	echo "Sorry, it's a hidden sit<a class=\"general\" href=\"admin.php?$arg\">e</a>.\n";
	return;
}
if(!($id = pageid0($Pagename))){
	echo "$pagename: No such page found.\n";
	return;
}
if(!$admin && is_hidden($Pagename)){
	echo "$pagename: Sorry, it's a hidden page.\n";
	return;
}

$version = "page.version";
if($v0 != "")
	$version = $v0;

$query = "select page.hits, page.locked, page.hidden, page.version as current,
			data.id, data.version, data.author, data.ip, data.mtime
			from page, data where page.id=$id and
			data.id=page.id and data.version=$version";
$result = pm_query($db, $query);
if(($r = pm_num_rows($result)))
	$data = pm_fetch_array($result, 0);
pm_free_result($result);
if(!$r){
	echo "<a class=\"wikiword_display\" href=\"index.php?display=$pageName\">$pagename</a> v$version: No such page version found.\n";
	return;
}

if($author !== $data['author'] && $ip !== $data['ip']){
	$query = "update page set hits=hits+1 where id=$id";
	$result = pm_query($db, $query);
	$data['hits']++;
}

$data['content'] = page_content($id, $version);
if($data['content'] == "\x01")
	$data['content'] = "";

/******************************************************************************/
if(preg_match("/^\\\\RedirectTo:([^\r\n]+)/", $data['content'], $m)){
	$EditRedirectedPage = "";
	switch(redirect_to($pagename0, $data['content'],
				1, $EditRedirectedPage)){
	case 0:
		$v0 = $v1 = "";
		$pagename = geni_specialchars($pagename0);
		$Pagename = addslashes($pagename0);
		$pageName = geni_urlencode($pagename0);
		$pagenamE = escape_doit($pagename0);
		include("action/display.php");
		return;
		break;
	case 1:
		warn(geni_specialchars($pagename0).
					": No such \\RedirectTo page found.\n");
		$data['content'] = "";
		break;
	case 2:
		warn(geni_specialchars($pagename0).
					": Sorry, it's a hidden \\RedirectTo page.\n");
		$data['content'] = "";
		break;
	case 3:
		warn(geni_specialchars($pagename0).
					": Infinite \\RedirectTo loop.\n");
		$data['content'] = "";
		break;
	}
}
/******************************************************************************/

$query = "select min(version) from data where id=$id";
$result = pm_query($db, $query);
$minversion = pm_fetch_result($result, 0, 0);
pm_free_result($result);

$deleted = !pageid($Pagename);

/*
$title_action	= ($init_wikix||$action!="display"?
		"display":"links2")."=$pageName";
*/
$title_action	= ($init_wikix?"display":"links2")."=$pageName";
$edit_action	= "$data[version],edit=$pageName";
$view		= ((!$admin&&(is_locked($Pagename)||is_site_locked()))||
		($data['version']!=$data['current'])||$deleted);
$diff_do	= ($data['version']>$minversion?
		"<a href=\"index.php?$data[version],diff=$pageName\">diff</a>":
		"diff");
$author_do	= (pageid($data['author'])?
		"<a href=\"index.php?display=$data[author]\">$data[author]</a>":
		$data['author']);
$current_do	= ($deleted?
		" (<a href=\"index.php?display=$pageName\">deleted</a>)":
		($data['current']!=$data['version']?
		" (<a href=\"index.php?display=$pageName\">current</a>)":""));

if($admin)
	$hits = "hit".($data['hits']>1?"s":"");
else
if($data['hits'] > 1)
	$hits = "hit<a class=\"general\" href=\"admin.php?$arg\">s</a>";
else
	$hits = "hi<a class=\"general\" href=\"admin.php?$arg\">t</a>";

include_once("$mytheme/display.php");
?>
