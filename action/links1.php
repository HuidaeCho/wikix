<?
if(!$admin && is_site_hidden()){
	echo "Sorry, it's a hidden site.\n";
	return;
}
if(!($id = pageid($Pagename))){
	echo "$pagename: No such page found.\n";
	return;
}
if(!$admin && is_hidden($Pagename)){
	echo "$pagename: Sorry, it's a hidden page.\n";
	return;
}

$query = "select page.name from link, page
			where link.linkfrom=$id and link.linkto=page.id ".
			($admin?"":"and page.hidden=0 ").
			"order by page.name";
$result1 = pm_query($db, $query);
$nlinks1 = pm_num_rows($result1);

$query = "select linktoname from link
			where linkfrom=$id and linkto=0
			order by linktoname";
$result2 = pm_query($db, $query);
$nlinks2 = pm_num_rows($result2);

$nlinks = $nlinks1 + $nlinks2;

$LinksFrom = ($nlinks?$nlinks:"No")." Link".($nlinks>1?"s":"")." from";

include_once("$mytheme/links1.php");
?>
