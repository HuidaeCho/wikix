<h1 class="title"><a accesskey="z" class="general" href="index.php?display=<?=$pageName?>">
<?="$LinksFrom $pagename"?>
</a></h1>

<ol>
<?
for($i=0; $i<$nlinks1; $i++){
	$topage = pm_fetch_result($result1, $i, 0);
	$toPage = geni_urlencode($topage);
	$topage = geni_specialchars($topage);
	echo
"<li><a class=\"wikiword_display\" href=\"index.php?display=$toPage\">$topage</a></li>\n";
}
pm_free_result($result1);

for($i=0; $i<$nlinks2; $i++){
	$topage = pm_fetch_result($result2, $i, 0);
	$w = split_word($topage);
	$w[0] = geni_specialchars($w[0]);
	$w[1] = geni_specialchars($w[1]);
	$toPage = geni_urlencode($topage);
	echo
"<li><a class=\"wikiword_goto\" href=\"index.php?goto=$toPage\">$w[0]</a>$w[1]</li>\n";
}
pm_free_result($result2);
?>
</ol>

<hr noshade />

<table width="100%" cellspacing="0px" cellpadding="0px" style="margin:0px;">
<tr valign="top"><td>

<a accesskey="x" class="wikiword_display" href="index.php?display=<?=$pageName?>"><?=$pagename?></a>

</td><td align="right">

<?include_once("$mytheme/loginout.php")?>

</td></tr>
</table>
