<h1 class="title"><a accesskey="z" class="general" href="index.php<?=($pagename==""?"":"?$doit=$pageName")?>"><?=$FilesOf.($pagename==""?"":" $pagename")?></a></h1>

<?=uploadedfiles($Pagename, ($pagename==""?0:1))?>

<hr noshade />

<table width="100%" cellspacing="0px" cellpadding="0px" style="margin:0px;">
<tr valign="top"><td>

<?if($pagename == ""){?>
<a accesskey="x" class="wikiword_display" href="index.php"><?=$wikiXfrontpage?></a>
<?}else{?>
<a accesskey="x" class="wikiword_<?=$doit?>" href="index.php?<?="$doit=$pageName"?>"><?="$w[0]</a>$w[1]"?>
<?}?>

</td><td align="right">

<?include_once("$mytheme/loginout.php")?>

</td></tr>
</table>
