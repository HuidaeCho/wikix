<h1 class="title"><a accesskey="z" class="general" href="index.php?<?="$doit=$pageName"?>"><?="$LinksTo $pagename"?></a></h1>

<ol>
<?
for($i=0; $i<$nlinks; $i++){
	$frompage = pm_fetch_result($result, $i, 0);
	$fromPage = geni_urlencode($frompage);
	$frompage = geni_specialchars($frompage);
	echo
"<li><a class=\"wikiword_display\" href=\"index.php?display=$fromPage\">$frompage<a></li>\n";
}
pm_free_result($result);
?>
</ol>

<hr noshade />

<table width="100%" cellspacing="0px" cellpadding="0px" style="margin:0px;">
<tr valign="top"><td>

<a accesskey="x" class="wikiword_<?=$doit?>" href="index.php?<?="$doit=$pageName"?>"><?="$w[0]</a>$w[1]"?>

</td><td align="right">

<?include_once("$mytheme/loginout.php")?>

</td></tr>
</table>
