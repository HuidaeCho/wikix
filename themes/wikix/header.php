<html>
<head>
<title>wikiX:<?=$pagename?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$charSet?>" />
<link rel="stylesheet" type="text/css" href="<?=$mycss?>" />
<script language="JavaScript" src="js/wikix.js"></script>
</head>

<body>
<?if(!$wikiXonlybody){?>
<table width="100%" cellspacing="0px" cellpadding="0px" style="margin:0px;">
<tr><td>
<a accesskey="q" href="index.php" style="color:blue;"><?=$wikiXlogo?></a>
<br />
<?if($login){?>
Login as <a href="index.php?goto=<?=$author?>"><?=$author?></a>
<?}else{?>
<span class="emphasized">Anonymou<?=($noAnonymous?
	"<a class=\"emphasized\" href=\"admin.php?$arg\">s</a>":"s")?>
	<?=$ip?></span>
<?}?>
</td><td align="right">
<a accesskey="w" href="http://wikix.org">
<img src="<?=$mytheme?>/images/NovaKim_Powered_by_wikiX_02.png" alt="Powered by wikiX" border="0px" /></a>
<br />
TimeZone: <?=date("T")?> | <a href="#Bottom">Bottom</a>
</td></tr>
</table>
<?}
if($noAnonymous && !$login){?>
<a accesskey="l" href="login.php?<?=$arg?>">Login</a>
<?include_once("$mytheme/footer.php"); closedb($db); exit;}?>
