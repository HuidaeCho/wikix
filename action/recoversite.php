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

$query = "select id, name, version, tversion, tname from ${db_}page
				where tversion>0 order by id";
$result = pm_query($db, $query);
$n = pm_num_rows($result);

for($i=0; $i<$n; $i++){
	$data = pm_fetch_array($result, $i);
	$id = $data['id'];
	$name = addslashes($data['name']);
	$version = $data['version'];
	$tversion = $data['tversion'];
	$tname = addslashes($data['tname']);
	if($name != $tname){
		if(pageid($tname))
			warn("$tname: Page already exists.");
		else
		if(pageid0($tname))
			warn("$tname: Deleted page exists.");
		else{
			$query = "update ${db_}page set name='$tname'
						where id=$id";
			$result0 = pm_query($db, $query);
		}
	}
	if($tversion == $version)
		continue;
	$content = page_content($id, $tversion);
	$content = addslashes($content);
	$query = "update ${db_}data set content='$content'
				where id=$id and version=$tversion";
	$result0 = pm_query($db, $query);
	$query = "update ${db_}page set version=$tversion where id=$id";
	$result0 = pm_query($db, $query);
	$query = "delete from ${db_}data where id=$id and version>$tversion";
	$result0 = pm_query($db, $query);

	$query = "delete from ${db_}link where linkfrom=$id";
	$result0 = pm_query($db, $query);
	$query = "insert into ${db_}link (linkfrom, linkto, linktoname)
		select linkfrom, linkto, linktoname from ${db_}taggedlink
		where linkfrom=$id";
	$result0 = pm_query($db, $query);
}
pm_free_result($result);
?>
