<?
$query = "select count(id) from ${db_}page";
$result = pm_query($db, $query);
$r = pm_fetch_result($result, 0, 0);
pm_free_result($result);

if($r)
	return;

if(!isset($adminAuthor))
	$adminAuthor = "wikiX";
if(!isset($adminPassword))
	$adminPassword = "[w!k!X|p@ssw*rd]";

$AdminAuthor = $adminAuthor;
$AdminPassword = md5($adminPassword);

$query = "select id from ${db_}admindb where id='$AdminAuthor'";
$result = pm_query($db, $query);
$r = pm_num_rows($result);
pm_free_result($result);
if(!$r){
	$query = "insert into ${db_}admindb (id, pw, cip, ctime, mip, mtime)
				values('$AdminAuthor', '$AdminPassword',
					'$ip', '$now', '$ip', '$now')";
	$result = pm_query($db, $query);
}

$query = "delete from ${db_}page";
$result = pm_query($db, $query);

$query = "delete from ${db_}data";
$result = pm_query($db, $query);

$query = "delete from ${db_}link";
$result = pm_query($db, $query);

$id = $n = 0;
$dir = opendir($wikiXpages);

while(($pagename0 = readdir($dir))){
	$Pagename = addslashes($pagename0);
	$file = "$wikiXpages/$pagename0";

	if(!is_file($file))
		continue;

	$fp = fopen($file, "r");
	$content = fread($fp, filesize($file));
	fclose($fp);

	$content = geni_trim(str_replace("\r", "", $content));
	$content = addslashes($content);

	$id++;
	$query = "insert into ${db_}page (id, name, cauthor, cip, ctime,
				hits, hidden, locked, version, tversion, tname)
				values($id, '$Pagename', 'HuidaeCho', '$ip',
				'$now', 0, 0, 0, 1, 0, '')";
	$result = pm_query($db, $query);

	$query = "insert into ${db_}data (id, version, author, ip, mtime,
				content) values($id, 1, 'HuidaeCho', '$ip',
				'$now', '$content')";
	$result = pm_query($db, $query);
	
	if($result){
		echo "<b>$pagename0 added.</b><br />\n";
		$n++;
	}

	$link = DisplayContent($content, 0);
	if(count($link) > 1)
		$link = array_values(array_unique($link));

	$query = "delete from ${db_}link where linkfrom=$id";
	$result = pm_query($db, $query);

	$nlinks = count($link);
	for($i=0; $i<$nlinks; $i++){
		if(preg_match("/^-(.*)$/", $link[$i], $m))
			$query = "insert into ${db_}link
						(linkfrom, linkto, linktoname)
						values($id, 0, '$m[1]')";
		else
			$query = "insert into ${db_}link
						(linkfrom, linkto, linktoname)
						values($id, $link[$i], '')";
		$result = pm_query($db, $query);
	}

	$query = "update ${db_}link set linkto=$id, linktoname=''
				where linktoname='$Pagename'";
	$result = pm_query($db, $query);
}
closedir($dir);

echo "<span class=\"emphasized\"><b>$n page".($n>1?"s":"")." added.</b></span><br />\n";

$init_wikix = 1;
?>
