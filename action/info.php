<?
if(!$admin && is_site_hidden()){
	echo "Sorry, it's a hidden site.\n";
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

$query = "select cauthor, cip, ctime, hits, hidden, locked, tag, version
			from page where id=$id";
$result = pm_query($db, $query);
$page = pm_fetch_array($result, 0);
pm_free_result($result);

$query = "select version, author, ip, mtime, content
			from data where id=$id order by version desc";
$result = pm_query($db, $query);
$nversions = pm_num_rows($result);

$query = "select count(linkfrom) from link where linkfrom=$id";
$result0 = pm_query($db, $query);
$linkfrom = pm_fetch_result($result0, 0, 0);
pm_free_result($result0);

$query = "select count(linkto) from link
			where linkto=$id or linktoname='$Pagename'";
$result0 = pm_query($db, $query);
$linkto = pm_fetch_result($result0, 0, 0);
pm_free_result($result0);

$Hits = "hit".($page['hits']>1?"s":"");

if($login){
	$query = "select btime from ".($admin?"admindb":"userdb").
			" where id='$author'";
	$result0 = pm_query($db, $query);
	$btime = pm_fetch_result($result0, 0, 0);
	pm_free_result($result0);
}

include_once("$mytheme/info.php");
?>
