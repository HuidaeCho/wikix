<h1 class="title"><a accesskey="z" class="general" href="index.php?display=SearchPages">SearchPages</a></h1>

<tt class="title"><?=$pagename?></tt><br />
<?=
($tc&0x1?"Title":"").(($tc&0x3)==0x3?($tc&0x4?"|":"&"):"").
($tc&0x2?"Content":"")." / ".
($ibegin==""?"Dont":"")."IgnoreCase".
($regex?" / RegularExpression":"")." / ".
($highlight==""?"No":"")."Highlighted"."<br />\n".
str_replace("\x03", "\\", ($highlight==""?
	pagelist($query, 0, 0, $random, $color)
:
	highlighted_pagelist($query, 0, 0, $random, $highlight)
))
?>

<hr noshade />

<table width="100%" cellspacing="0px" cellpadding="0px" style="margin:0px;">
<tr valign="top"><td>

</td><td align="right">

<?include_once("$mytheme/loginout.php")?>

</td></tr>
</table>
