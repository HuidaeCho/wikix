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

$query = "select data.version, data.content!='\x01' as undeleted
			from page, data where page.id=$id and data.id=page.id
			and data.version=page.version";
$result = pm_query($db, $query);
$data = pm_fetch_array($result, 0);
pm_free_result($result);
if($data['undeleted'] == 1 || $data['undeleted'] == "t"){
	include_once("action/display.php");
	return;
}

$version = $data['version'];
$v = $version - 1;

$query = "select id from data where id=$id and version=$v";
$result = pm_query($db, $query);
$r = pm_num_rows($result);
pm_free_result($result);
if(!$r){
	echo "$pagename: Sorry, it's permanently deleted.\n";
	return;
}

$query = "select content from data where id=$id and version=$v";
$result = pm_query($db, $query);
$content = pm_fetch_result($result, 0, 0);
pm_free_result($result);
$content = addslashes($content);

$query = "update data set content='' where id=$id and version=$v";
$result = pm_query($db, $query);

$version++;
$query = "update page set version=$version where id=$id";
$result = pm_query($db, $query);

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
?>
