<?
if(invalid_access()){
    	echo "It's not allowed to access the page directly.\n";
	return;
}
if($login){
	if($v0 == "")
		$btime = "";
	else
		$btime = date("Y-m-d H:i:s", $v0);
	$query = "update ${db_}".($admin?"admindb":"userdb").
			" set btime='$btime' where id='$author'";
	$result = pm_query($db, $query);
}

$v0 = "";
include_once("action/display.php");
?>
