<?
if(invalid_access()){
    	echo "It's not allowed to access the page directly.\n";
	return;
}
if(!$admin || !is_site_locked())
	return;

$query = "update ${db_}site set locked=0";
$result = pm_query($db, $query);
?>
