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

$query = "update ${db_}page set tag=version";
$result = pm_query($db, $query);

$query = "delete from ${db_}taggedlink";
$result = pm_query($db, $query);

$query = "insert into ${db_}taggedlink (linkfrom, linkto, linktoname)
	select linkfrom, linkto, linktoname from ${db_}link";
$result = pm_query($db, $query);
?>
