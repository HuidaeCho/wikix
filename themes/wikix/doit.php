<?
if(!$wikiXonlybody){
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

<form action="index.php" method="get">
<?if($v0){?>
<h1 class="title"><a accesskey="z" class="general" href="index.php">
<?=$pagename?>
</a><a href="index.php?1,<?=
($action=="doit"?"::":"")."doit=$pageName"?>">&nbsp;</a></h1>
<?}?>

<?
include_once("mywikix/header.php");
DisplayContent("$wikiXheader$content$wikiXfooter");
include_once("mywikix/footer.php");
?>
</form>

<?if(!$wikiXonlybody){?>
<hr noshade />

<table width="100%" cellspacing="0px" cellpadding="0px" style="margin:0px;">
<tr valign="top"><td>

<form action="index.php" method="get">
GoTo <input accesskey="c" type="text" name="goto" size="<?=$gotoSize?>" />
<br />
<i>
<?=$now?>
<br />
<?="* $hits"?>
</i>
</form>

</td><td align="right">

<?include_once("$mytheme/loginout.php")?>

</td></tr>
</table>
<?}?>
