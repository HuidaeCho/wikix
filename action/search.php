<?
if(!$admin && is_site_hidden()){
	echo "Sorry, it's a hidden site.\n";
	return;
}
$query = search_query($pagename0, $tc, $ibegin, $iend, $order,
							$regex, $highlight);
$random = ($order==""?1:0);
$color = (strpos($order, " order by ${db_}data.mtime ")===false?0:1);

include_once("$mytheme/search.php");
?>
