<?if($wikiXonlybody)
	return;
if($action == "display"){?>
<div align="right">
<a href="index.php?edit=<?=$pageName?>">EditOrViewSource</a>
</div>
<?}?>
<style type="text/css">
.mymenu
{
	background-color:	black;
	color:			white;
	padding:		2px;
	margin-bottom:		5px;
	text-align:		right;
}
.mymenu a
{
	color:			white;
}
</style>
<div class="mymenu">
<a href="index.php?<?=$wikiXfrontPage?>"><?=$wikiXfrontpage?></a>
<?if($admin){?>
. <a href="index.php?MySecretPage">MySecretPage</a>
<?}?>
</div>
