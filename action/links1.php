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

$query = "select ${db_}page.name from ${db_}link, ${db_}page
	where ${db_}link.linkfrom=$id and ${db_}link.linkto=${db_}page.id ".
	($admin?"":"and ${db_}page.hidden=0 ")."order by ${db_}page.${db_}name";
$result1 = pm_query($db, $query);
$nlinks1 = pm_num_rows($result1);

$query = "select linktoname  ${db_}link
			where linkfrom=$id and linkto=0
			order by linktoname";
$result2 = pm_query($db, $query);
$nlinks2 = pm_num_rows($result2);

$nlinks = $nlinks1 + $nlinks2;

$LinksFrom = ($nlinks?$nlinks:"No")." Link".($nlinks>1?"s":"")." from";

include_once("$mytheme/links1.php");
?>
