<?
/*
if(invalid_access()){
    	echo "It's not allowed to access the page directly.\n";
	return;
}
*/
if(!$admin && is_site_hidden()){
	echo "Sorry, it's a hidden site.\n";
	return;
}
$content = $pagename0;

/******************************************************************************/
if(preg_match("/^\\\\RedirectTo:([^\r\n]+)/", $content, $m)){
	$pagename0 = "\x02";
	$EditRedirectedPage = "";
	switch(redirect_to($pagename0, $content, 1, $EditRedirectedPage)){
	case 0:
		$v0 = $v1 = "";
		$action = "display";
		$pagename = geni_specialchars($pagename0);
		$Pagename = addslashes($pagename0);
		$pageName = geni_urlencode($pagename0);
		$pagenamE = escape_doit($pagename0);
		include_once("action/display.php");
		return;
		break;
	case 1:
		warn(geni_specialchars($pagename0).
					": No such \\RedirectTo page found.\n");
		$content = "";
		break;
	case 2:
		warn(geni_specialchars($pagename0).
					": Sorry, it's a hidden \\RedirectTo page.\n");
		$content = "";
		break;
	case 3:
		warn(geni_specialchars($pagename0).
					": Infinite \\RedirectTo loop.\n");
		$content = "";
		break;
	}
}
/******************************************************************************/

$hits = ($admin?"hits":"hit<a class=\"general\" href=\"admin.php?$arg\">s</a>");

include_once("$mytheme/doit.php");
?>
