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

$query = "select id, tag, version from ${db_}page where tag>0 order by id";
$result = pm_query($db, $query);
$n = pm_num_rows($result);

for($i=0; $i<$n; $i++){
	$data = pm_fetch_array($result, $i);
	$id = $data['id'];
	$tag = $data['tag'];
	$version = $data['version'];
	if($tag == $version)
		continue;
	$content = page_content($id, $tag);
	$content = addslashes($content);
	$query = "update ${db_}data set content='$content'
				where id=$id and version=$tag";
	$result0 = pm_query($db, $query);
	$query = "update ${db_}page set version=$tag where id=$id";
	$result0 = pm_query($db, $query);
	$query = "delete from ${db_}data where id=$id and version>$tag";
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
