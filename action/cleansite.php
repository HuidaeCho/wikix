<?
if(invalid_access()){
    	echo "It's not allowed to access the page directly.\n";
	return;
}
if(!$admin)
	return;
if(!is_site_locked()){
	warn("Lock the site first.");
	$v0 = "";
	return;
}

if($v0 == "" || !$v0)
	$v0 = 1;

$query = "select id, version, tversion from ${db_}page order by id";
$result = pm_query($db, $query);
$n = pm_num_rows($result);

for($i=0; $i<$n; $i++){
	$data = pm_fetch_array($result, $i);
	$version = $data['version'] - $v0 + 1;
	$tversion = $data['tversion'];
	if($tversion)
		$version = ($tversion>$version?$version:$tversion);
	$query = "delete from ${db_}data
			where id=$data[id] and version<$version";
	$result0 = pm_query($db, $query);
}
pm_free_result($result);

$v0 = "";
?>
