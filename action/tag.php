<?
if(invalid_access()){
    	echo "It's not allowed to access the page directly.\n";
	return;
}
if(!$admin || !($id = pageid0($Pagename)))
	return;

if($v0 == "")
	$tag = "version";
else{
	$query = "select min(version), max(version) from data where id=$id";
	$result = pm_query($db, $query);
	$minversion = pm_fetch_result($result, 0, 0);
	$maxversion = pm_fetch_result($result, 0, 1);
	pm_free_result($result);
	if($v0 < $minversion)
		$tag = $minversion;
	else
	if($v0 > $maxversion)
		$tag = $maxversion;
	else
		$tag = $v0;
}

$query = "update page set tag=$tag where id=$id";
$result = pm_query($db, $query);

$query = "delete from taggedlink where linkfrom=$id";
$result = pm_query($db, $query);

$query = "insert into taggedlink (linkfrom, linkto, linktoname)
	select linkfrom, linkto, linktoname from link where linkfrom=$id";
$result = pm_query($db, $query);
?>