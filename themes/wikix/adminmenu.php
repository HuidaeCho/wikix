<?
if(!isset($subaction)){
	$lock = (is_locked($Pagename)?"on":"");
	$hide = (is_hidden($Pagename)?"on":"");
}

if($moreAdmins && $author === $adminAuthor){?>
<div align="right">
<form action="admin.php?<?=$arg?>" method="post">
<input type="submit" value="AddAdmin" />
<input name="adminauthor" size="10" />
<input type="password" name="adminpassword" size="10" />
</form>
</div>
<?}

echo "Page[\n";
if(isset($lock) && $lock == "on")
	echo "<a href=\"index.php?unlock=$pageName\">Unlock</a>\n";
else
	echo "<a href=\"index.php?lock=$pageName\">Lock</a>\n";

if(isset($hide) && $hide == "on")
	echo ". <a href=\"index.php?unhide=$pageName\">Unhide</a>\n";
else
	echo ". <a href=\"index.php?hide=$pageName\">Hide</a>\n";

if($pagename === $wikiXfrontpage)
	echo ". Remove\n";
else
	echo ". <a href=\"index.php?remove=$pageName\">Remove</a>\n";

$v = ($v0==""?"":"$v0,");
echo ". <a href=\"index.php?${v}tag=$pageName\">Tag</a>\n";
echo ". <a href=\"index.php?untag=$pageName\">Untag</a>\n";

echo "]\nSite[\n";

if(is_site_locked())
	echo "<a href=\"index.php?unlocksite=$pageName\">Unlock</a>\n";
else
	echo "<a href=\"index.php?locksite=$pageName\">Lock</a>\n";

if(is_site_hidden())
	echo ". <a href=\"index.php?unhidesite=$pageName\">Unhide</a>\n";
else
	echo ". <a href=\"index.php?hidesite=$pageName\">Hide</a>\n";

echo ". <a href=\"index.php?5,cleansite=$pageName\">Clean</a>\n";
echo ". <a href=\"index.php?tagsite=$pageName\">Tag</a>\n";
echo ". <a href=\"index.php?untagsite=$pageName\">Untag</a>\n";
echo ". <a href=\"index.php?recoversite=$pageName\">Recover</a>\n";

echo "]\n";
?>
