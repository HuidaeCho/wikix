<h1 class="title"><a accesskey="z" class="general" href="index.php?display=<?=$pageName?>">
info of <?=$pagename?>
</a></h1>

<p><b>
<?
if($pagename0 === $wikiXfrontpage0){
	if($admin)
		$query = "select sum(length(content)) from data";
	else
		$query = "select sum(length(data.content)) from page, data
					where page.id=data.id and
					page.hidden=0 and page.locked=0";
	$result0 = pm_query($db, $query);
	$size = pm_fetch_result($result0, 0, 0);
	pm_free_result($result0);
	$bytes = ($admin?" byte".($size>1?"s":""):"+? bytes");
	$size = number_format($size).$bytes;
	echo "<span style=\"color:blue;\">site size: $size</span><br />\n";
}

if($admin||!$page['locked']){
	$query = "select sum(length(content)) from data where id=$id";
	$result0 = pm_query($db, $query);
	$size = pm_fetch_result($result0, 0, 0);
	pm_free_result($result0);
	$bytes = " byte".($size>1?"s":"").", ";
	$size = number_format($size).$bytes;
}else
	$size = "";
echo
"page id: $id, ".
"original(author: $page[cauthor], ".($admin?"ip: $page[cip], ":"").
"time: $page[ctime])<br />\n".
"$page[hits] $Hits, ".
($page['locked']?"locked":"unlocked").", ".
($page['hidden']?"hidden":"unhidden").", ".
$size.
($page['tag']?"tagged: v$page[tag], ":"").
"current: v$page[version]<br />\n".
"from: <a href=\"index.php?links1=$pageName\">$linkfrom</a>, ".
"to: <a href=\"index.php?links2=$pageName\">$linkto</a>
";
?>
</b></p>

<table class="pagelist">
<tr class="pagelist_header">
<td align="right">&nbsp;<span class="table_header">Version</span>&nbsp;</td>
<td>&nbsp;<span class="table_header">Author</span>&nbsp;</td>
<?=($admin?"<td>&nbsp;<span class=\"table_header\">IP</span>&nbsp;</td>":"")?>
<td>&nbsp;<a href="index.php?diff=<?=$pageName?>"><span class="table_header">Last Modified</span></a>&nbsp;</td>
<td align="right">&nbsp;<span class="table_header">Size</span>&nbsp;</td>
<?=($admin?"<td align=\"center\">&nbsp;<span class=\"table_header\">Remove</span>&nbsp;</td>":"")?>
</tr>

<?
$deleted = 0;
$showsize = ($admin||!$page['locked']);
$date = "";
$class = "outdated";
for($i=0; $i<$nversions; $i++){
	$data = pm_fetch_array($result, $i);
	$size = ($showsize?number_format(strlen($data['content'])):"-");
	$mdate = substr($data['mtime'], 0, 10);
	if($date !== $mdate){
		$class = ($class=="outdated"?"recent":"outdated");
		$date = $mdate;
	}
	$bclass = "general";
	if($data['content'] == "\x01"){
		if($btime != "" && $data['mtime'] > $btime)
			$bclass = "deleted";
		else
			$bclass = "deleted0";
		if(!$i)
			$deleted = 1;
	}else
	if($btime != "" && $data['mtime'] > $btime){
		if($page['ctime'] > $btime)
			$bclass = "new";
		else
			$bclass = "updated";
	}
	$v_ = "$data[version],";
	if(preg_match("/^\\\\RedirectTo:([^\r\n]+)/", $data['content']))
		$v_ = "";
	echo
"<tr class=\"pagelist_$class\">".
"<td align=\"right\">&nbsp;<a href=\"index.php?${v_}display=$pageName\">v$data[version]</a>&nbsp;</td>".
"<td>&nbsp;".
(pageid($data['author'])?
"<a href=\"index.php?display=$data[author]\">$data[author]</a>":$data['author']).
"&nbsp;</td>".
($admin?"<td>&nbsp;$data[ip]&nbsp;</td>":"").
"<td>&nbsp;".($i<$nversions-1?"<a href=\"index.php?$data[version],diff=$pageName\">":"")."<span class=\"$bclass\">$data[mtime]</span>".($i<$nversions-1?"</a>":"")."&nbsp;</td>".
"<td align=\"right\">&nbsp;$size&nbsp;</td>".
($admin?"<td align=\"center\">&nbsp;<a href=\"index.php?$data[version],0,remove=$pageName\">from</a> | <a href=\"index.php?0,$data[version],remove=$pageName\">to</a>&nbsp;</td>":"").
"</tr>\n";
}
pm_free_result($result);
$DeleteOrUndeletePage = "";
if($admin||!$page['locked'])
	$DeleteOrUndeletePage = ($deleted?
		"| <a href=\"index.php?undelete=$pageName\">UndeletePage</a>":
		"| <a href=\"index.php?delete=$pageName\">DeletePage</a>");
?>

</table>

<hr noshade />

<table width="100%" cellspacing="0px" cellpadding="0px" style="margin:0px;">
<tr valign="top"><td>

<a accesskey="x" class="wikiword_display" href="index.php?display=<?=$pageName?>"><?=$pagename?></a>
<?=$DeleteOrUndeletePage?>

</td><td align="right">

<?include_once("$mytheme/loginout.php")?>

</td></tr>
</table>
