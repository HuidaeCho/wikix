<?
if(invalid_access()){
    	echo "It's not allowed to access the page directly.\n";
	return;
}
if(!$admin || !($id = pageid0($Pagename)))
	return;

$query = "update page set tag=0 where id=$id";
$result = pm_query($db, $query);

$query = "delete from taggedlink where linkfrom=$id";
$result = pm_query($db, $query);
?>
