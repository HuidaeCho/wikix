<?
if(invalid_access()){
    	echo "It's not allowed to access the page directly.\n";
	return;
}
if(!$admin)
	return;

if($v0 != "" || $v1 != ""){
	if($v1 > 0)
		$query = "update ${db_}userdb set pw='' where id='$Pagename'";
	else
		$query = "delete from ${db_}userdb where id='$Pagename'";
	$result = pm_query($db, $query);
}

$v0 = 1;
$action = "doit";
$pagename0 = "\\AllAuthors";
$pagename = geni_specialchars($pagename0);
$Pagename = addslashes($pagename0);
$pageName = geni_urlencode($pagename0);
$pagenamE = escape_doit($pagename0);

include_once("action/doit.php");
?>
