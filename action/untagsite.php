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

$query = "update ${db_}page set tversion=0, tname=''";
$result = pm_query($db, $query);

$query = "delete from ${db_}taggedlink";
$result = pm_query($db, $query);
?>
