<?
if(invalid_access()){
    	echo "It's not allowed to access the page directly.\n";
	return;
}
if(!$admin)
	return;
if(!is_site_locked()){
	warn("Lock the site first.");
	return;
}

$query = "update page set tag=0";
$result = pm_query($db, $query);

$query = "delete from taggedlink";
$result = pm_query($db, $query);
?>
