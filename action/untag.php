<?
if(invalid_access()){
    	echo "It's not allowed to access the page directly.\n";
	return;
}
if(!$admin || !($id = pageid0($Pagename)))
	return;

$query = "update ${db_}page set tversion=0, tname='' where id=$id";
$result = pm_query($db, $query);

$query = "delete from ${db_}taggedlink where linkfrom=$id";
$result = pm_query($db, $query);
?>
