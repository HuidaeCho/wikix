<?if(!$wikiXonlybody){
	if($admin){
		include_once("$mytheme/adminmenu.php");
	}
?>

<form name="search" action="index.php" method="get">
<a tabindex="2" class="wikiword_display" href="index.php?display=AllPages">AllPages</a> |
<a accesskey="r" tabindex="3" class="wikiword_display" href="index.php?display=RecentChanges">RecentChanges</a> |
<a tabindex="4" class="wikiword_display" href="index.php?display=MostPopular">MostPopular</a> |
<a class="wikiword_display" href="index.php?display=SearchPages">SearchPages</a>
<input accesskey="s" tabindex="1" type="text" name="search" size="<?=$searchSize?>" />
</form>

<?/*
<script language="JavaScript" type="text/javascript">
<!--
document.forms.search.elements.search.focus();
//-->
</script>
*/?>

<?=$EditRedirectedPage?>
<?}?>

<h1 class="title"><a accesskey="z" class="general" href="index.php?<?=$title_action?>"><?=$pagename?></a><a href="index.php?<?=($action=="display"?"::":"")."display=$pageName"?>">&nbsp;</a></h1>

<?
include_once("mywikix/header.php");
$link = DisplayContent("$wikiXheader$data[content]$wikiXfooter");
include_once("mywikix/footer.php");
?>

<?if(!$wikiXonlybody){?>
<hr noshade />

<table width="100%" cellspacing="0px" cellpadding="0px" style="margin:0px;">
<tr valign="top"><td>

<form action="index.php" method="get">
<a accesskey="x" href="index.php?<?=$edit_action?>"><?=($view?"ViewSource":"EditPage")?></a> |
<a href="index.php?info=<?=$pageName?>">info</a> |
<?=$diff_do?> |
<a href="index.php?files=<?=$pageName?>">files</a><?=
($pagename0==$wikiXfrontpage0?"<a href=\"index.php?files=%02\">*</a>":"")?> |
GoTo <input accesskey="c" type="text" name="goto" size="<?=$gotoSize?>" />
<br />
<i>
<?="$data[mtime] v$data[version]:$author_do"?>
<?=$current_do?>
<br />
<?="$data[hits] $hits"?>
</i>
</form>

</td><td align="right">

<?include_once("$mytheme/loginout.php")?>

</td></tr>
</table>
<?}?>
