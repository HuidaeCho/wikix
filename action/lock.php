<?
if(invalid_access()){
    	echo "It's not allowed to access the page directly.\n";
	return;
}
if(!$admin || is_locked($Pagename))
	return;

$query = "update ${db_}page set locked=1 where name='$Pagename'";
$result = pm_query($db, $query);
?>
