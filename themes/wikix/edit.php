<h1 class="title"><a accesskey="z" class="general" href="index.php?<?=$title_action?>"><?=$pagename?></a></h1>

<?
if($subaction == "Preview"){
	echo "<b class=\"emphasized\">Preview</b><hr />\n";
	DisplayContent("$wikiXheader$content$wikiXfooter");
	echo "<hr />\n";
	if($admin){
		$lock = (isset($lock)&&$lock=="on"?" checked":"");
		$hide = (isset($hide)&&$hide=="on"?" checked":"");
	}
}
?>

<form name="edit" action="<?=$uri?>" method="post">
<?
if(!$locked){
?>
<a href="file.php?p=<?=$pageName?>&n=10" target="_blank">UploadFile</a>
<?
}

$content = geni_specialchars0($content);
?>

<textarea accesskey="i" name="content" rows="30" cols="80" style="width:100%;">
<?=$content?>
</textarea>

<?
if(!$locked){
?>
<input type="hidden" name="version" value="<?=$version?>" />
<input type="submit" name="subaction" value="Preview" />
<input type="submit" name="subaction" value="Save" />
<?if($admin){?>
<input type="checkbox" name="lock"<?=$lock?> />Lock
<input type="checkbox" name="hide"<?=$hide?> />Hide
<?}
}
?>
</form>

<?if($subaction == ""){?>
<script language="JavaScript" type="text/javascript">
<!--
document.forms.edit.elements.content.focus();
//-->
</script>
<?}?>

<hr noshade />

<table width="100%" cellspacing="0px" cellpadding="0px" style="margin:0px;">
<tr valign="top"><td>

<a accesskey="x" href="index.php?<?=$view_action?>"><?=($id?"ViewPage":"LinksTo")?></a>
<?if($edit){?>
| <a href="index.php?info=<?=$pageName?>">info</a>
<?=($diff_do==""?"":" | $diff_do")?> |
<a href="index.php?files=<?=$pageName?>">files</a><?=
($pagename0==$wikiXfrontpage0?"<a href=\"index.php?files=%02\">*</a>":"")?>
<br />
<i>
<?="$data[mtime] v$data[version]:$author_do"?>
<?=$current_do?>
<br />
<?="$data[hits] $hits"?>
</i>
<?}else{?>
| <a href="index.php?files=<?=$pageName?>">files</a><?=
($pagename0==$wikiXfrontpage0?"<a href=\"index.php?files=%02\">*</a>":"")?>
<?}?>

</td><td align="right">

<?include_once("$mytheme/loginout.php")?>

</td></tr>
</table>
