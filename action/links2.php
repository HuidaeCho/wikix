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
	$where = "link.linkto=$id";
	$doit = "display";
	$w[0] = geni_specialchars($pagename0);
	$w[1] = "";
}else{
	$where = "link.linktoname='$Pagename'";
	$doit = "goto";
	$w = split_word($pagename0);
	$w[0] = geni_specialchars($w[0]);
	$w[1] = geni_specialchars($w[1]);
}

$query = "select page.name from link, page
			where $where and link.linkfrom=page.id ".
			($admin?"":"and page.hidden=0 ").
			"order by page.name";
$result = pm_query($db, $query);
$nlinks = pm_num_rows($result);

$LinksTo = ($nlinks?$nlinks:"No")." Link".($nlinks>1?"s":"")." to";

include_once("$mytheme/links2.php");
?>
