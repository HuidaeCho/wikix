<?
if(invalid_access()){
    	echo "It's not allowed to access the page directly.\n";
	return;
}
if(!$admin || !is_site_hidden())
	return;

$query = "update ${db_}site set hidden=0";
$result = pm_query($db, $query);
?>
