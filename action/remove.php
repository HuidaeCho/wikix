<?
if(invalid_access()){
    	echo "It's not allowed to access the page directly.\n";
	return;
}
if(!$admin || !($id = pageid0($Pagename)))
	return;

$query = "select tag from page where id=$id";
$result = pm_query($db, $query);
$tag = pm_fetch_result($result, 0, 0);
pm_free_result($result);

$newversion = 0;
if($v0 == "" && $v1 == "")
	$query = "delete from data where id=$id";
else
if($v0 > 0){
	$v0 = ($tag<$v0?$v0:$tag+1);
	$query = "select min(version) from data where id=$id";
	$result = pm_query($db, $query);
	$minversion = pm_fetch_result($result, 0, 0);
	pm_free_result($result);
	$newversion = $v0 - 1;
	if($newversion >= $minversion){
		$v = $newversion;
		$content = page_content($id, $v);
		if($content == "\x01"){
			$content = page_content($id, $v0);
			$v = $newversion - 1;
		}
		$content = addslashes($content);
		$query = "update data set content='$content'
					where id=$id and version=$v";
		$result = pm_query($db, $query);
	}
	$query = "delete from data where id=$id and version>=$v0";
}else
if($v1 > 0){
	$v1 = ($tag>$v1||!$tag?$v1:$tag-1);
	$query = "delete from data where id=$id and version<=$v1";
}else{
	$v0 = "";
	$v1 = "";
	return;
}
$result = pm_query($db, $query);

$query = "select id from data where id=$id";
$result = pm_query($db, $query);
$n = pm_num_rows($result);
pm_free_result($result);
if($n){
	if($newversion > 0){
		$query = "update page set version=$newversion where id=$id";
		$result = pm_query($db, $query);
	}else
		$n = 0;
}else{
	$query = "delete from page where id=$id";
	$result = pm_query($db, $query);

	$query = "delete from link where linkfrom=$id";
	$result = pm_query($db, $query);
	$query = "update link set linktoname='$Pagename', linkto=0
				where linkto=$id";
	$result = pm_query($db, $query);

	$query = "delete from taggedlink where linkfrom=$id";
	$result = pm_query($db, $query);
	$query = "update taggedlink set linktoname='$Pagename', linkto=0
				where linkto=$id";
	$result = pm_query($db, $query);

	$pagename0 = $wikiXfrontpage0;
	$pagename = $wikiXfrontpage;
	$Pagename = $wikiXFrontpage;
	$pageName = $wikiXfrontPage;
	$pagenamE = $wikiXfrontpagE;
}

$v0 = "";

include_once("action/display.php");

if($n){
	if(count($link) > 1)
		$link = array_values(array_unique($link));

	$query = "delete from link where linkfrom=$id";
	$result = pm_query($db, $query);

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

	/*
	$query = "update link set linkto=$id, linktoname=''
				where linktoname='$Pagename'";
	$result = pm_query($db, $query);
	*/
}
?>
