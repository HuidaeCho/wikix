<?
if(invalid_access()){
    	echo "It's not allowed to access the page directly.\n";
	return;
}
if(!$admin || is_site_hidden())
	return;

$query = "update site set hidden=1";
$result = pm_query($db, $query);
?>
