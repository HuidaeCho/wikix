<?
if(!$admin){
	if(is_site_hidden()){
		echo "Sorry, it's a hidden site.\n";
		return;
	}else
	if(is_hidden($Pagename)){
		echo "$pagename: Sorry, it's a hidden page.\n";
		return;
	}
}
if(($id = pageid($Pagename))){
	$where = "${db_}link.linkto=$id";
	$doit = "display";
	$w[0] = geni_specialchars($pagename0);
	$w[1] = "";
}else{
	$where = "${db_}link.linktoname='$Pagename'";
	$doit = "goto";
	$w = split_word($pagename0);
	$w[0] = geni_specialchars($w[0]);
	$w[1] = geni_specialchars($w[1]);
}

$query = "select ${db_}page.name from ${db_}link, ${db_}page
			where $where and ${db_}link.linkfrom=${db_}page.id ".
			($admin?"":"and ${db_}page.hidden=0 ").
			"order by ${db_}page.name";
$result = pm_query($db, $query);
$nlinks = pm_num_rows($result);

$LinksTo = ($nlinks?$nlinks:"No")." Link".($nlinks>1?"s":"")." to";

include_once("$mytheme/links2.php");
?>
