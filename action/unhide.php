<?
if(invalid_access()){
    	echo "It's not allowed to access the page directly.\n";
	return;
}
if(!$admin || !is_hidden($Pagename))
	return;

$query = "update ${db_}page set hidden=0 where name='$Pagename'";
$result = pm_query($db, $query);
?>
