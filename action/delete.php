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
	if(is_site_locked()){
		echo "Sorry, it's a locked site.\n";
		return;
	}
}
if(!($id = pageid0($Pagename))){
	echo "$pagename: No such page found.\n";
	return;
}
if(!$admin){
	if(is_hidden($Pagename)){
		echo "$pagename: Sorry, it's a hidden page.\n";
		return;
	}else
	if(is_locked($Pagename)){
		echo "$pagename: Sorry, it's a locked page.\n";
		return;
	}
}

$query = "select ${db_}data.version, ${db_}data.content!='\x01' as undeleted
		from ${db_}page, ${db_}data
		where ${db_}page.id=$id and ${db_}data.id=${db_}page.id
		and ${db_}data.version=${db_}page.version";
$result = pm_query($db, $query);
$data = pm_fetch_array($result, 0);
pm_free_result($result);

if($data['undeleted'] == 1 || $data['undeleted'] == "t"){
	$version = $data['version'];
	$query = "select content from ${db_}data where id=$id and version=$version";
	$result = pm_query($db, $query);
	$content = pm_fetch_result($result, 0, 0);
	pm_free_result($result);

	$version++;
	$query = "update ${db_}page set version=$version where id=$id";
	$result = pm_query($db, $query);

	$query = "insert into ${db_}data
		(id, version, author, ip, mtime, content)
		values($id, $version, '$author', '$ip', '$now', '\x01')";
	$result = pm_query($db, $query);

	$query = "delete from ${db_}link where linkfrom=$id";
	$result = pm_query($db, $query);

	$query = "update ${db_}link set linktoname='$Pagename', linkto=0
				where linkto=$id";
	$result = pm_query($db, $query);
}

$pagename0 = $wikiXfrontpage0;
$pagename = $wikiXfrontpage;
$Pagename = $wikiXFrontpage;
$pageName = $wikiXfrontPage;
$pagenamE = $wikiXfrontpagE;

include_once("action/display.php");
?>
