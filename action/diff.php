<?
if(!$admin && is_site_hidden()){
	echo "Sorry, it's a hidden site.\n";
	return;
}
if(!($id = pageid0($Pagename))){
	echo "$pagename: No such page found.\n";
	return;
}
if(!$admin && is_hidden($Pagename)){
	echo "$pagename: Sorry, it's a hidden page.\n";
	return;
}

$query = "select hits, hidden, locked, version from page where id=$id";
$result = pm_query($db, $query);
$page = pm_fetch_array($result, 0);
pm_free_result($result);
if($page['version'] == 1){
	echo "<a class=\"wikiword_display\" href=\"index.php?display=$pageName\">$pagename</a>: The original version.\n";
	return;
}

$query = "select min(version) from data where id=$id";
$result = pm_query($db, $query);
$minversion = pm_fetch_result($result, 0, 0);
pm_free_result($result);

if($v0 == "" && $v1 == ""){
	if($login){
		$v1 = $page['version'];
		$query = "select btime from ".($admin?"admindb":"userdb").
				" where id='$author'";
		$result = pm_query($db, $query);
		$btime = pm_fetch_result($result, 0, 0);
		pm_free_result($result);
		if($btime == "")
			$v0 = $v1 - 1;
		else{
			$query = "select version from data
					where id=$id and mtime < '$btime'
					order by mtime desc limit 1";
			$result = pm_query($db, $query);
			if(pm_num_rows($result) == 1)
				$v0 = pm_fetch_result($result, 0, 0);
			else
				$v0 = $minversion;
			pm_free_result($result);
			if($v0 == $v1)
				$v0--;
		}
	}else{
		$v1 = $page['version'];
		$v0 = $v1 - 1;
	}
}else{
	if($v1 == "" || $v0 == $v1){
		$v1 = $v0;
		$v0--;
	}else
	if($v0 > $v1){
		$v = $v0;
		$v0 = $v1;
		$v1 = $v;
	}
	if($v1 == 1){
		echo "<a class=\"wikiword_display\" href=\"index.php?display=$pageName\">$pagename</a>: The original version.\n";
		return;
	}
}

if($v0 < $minversion || $v1 > $page['version']){
	echo "<a class=\"wikiword_display\" href=\"index.php?display=$pageName\">$pagename</a>: diff v$v0 v$v1: No such page version found.\n";
	return;
}

$showsize = ($admin||!$page['locked']);

$query = "select author, mtime from data where id=$id and
			(version=$v0 or version=$v1)
			order by version";
$result = pm_query($db, $query);
$data0 = pm_fetch_array($result, 0);
$data1 = pm_fetch_array($result, 1);
pm_free_result($result);

$data0['content'] = page_content($id, $v0);
$data1['content'] = page_content($id, $v1);

if($data0['content'] == "\x01"){
	$size0 = ($showsize?"1":"-");
	$content0 = "";
	$line0 = array();
	$nlines0 = 0;
}else{
	$size0 = ($showsize?number_format(strlen($data0['content'])):"-");
	$content0 = str_replace("\r", "", $data0['content']);
	if(!$admin && $page['locked'])
		$content0 = hidecode($content0);
	$content0 = geni_specialchars0($content0);
	$line0 = explode("\n", $content0);
	$nlines0 = count($line0);
}

if($data1['content'] == "\x01"){
	$size1 = ($showsize?"1":"-");
	$content1 = "";
	$line1 = array();
	$nlines1 = 0;
}else{
	$size1 = ($showsize?number_format(strlen($data1['content'])):"-");
	$content1 = str_replace("\r", "", $data1['content']);
	if(!$admin && $page['locked'])
		$content1 = hidecode($content1);
	$content1 = geni_specialchars0($content1);
	$line1 = explode("\n", $content1);
	$nlines1 = count($line1);
}

$diff_do	= ($v0>$minversion?
		"<a href=\"index.php?$v0,diff=$pageName\">&lt;</a>":"&lt;").
		"diff".($v1<$page['version']?
		"<a href=\"index.php?".($v1+1).
		",diff=$pageName\">&gt;</a>":"&gt;");
$author_do	= (pageid($data1['author'])?
		"<a href=\"index.php?display=$data1[author]\">$data1[author]</a>":
		$data1['author']);
$current_do	= ($v1!=$page['version']?
		" (<a href=\"index.php?display=$pageName\">current</a>)":"");

$Hits = "hit".($page['hits']>1?"s":"");

if($admin)
	$hits = $Hits;
else
if($page['hits'] > 1)
	$hits = "hit<a class=\"general\" href=\"admin.php?$arg\">s</a>";
else
	$hits = "hi<a class=\"general\" href=\"admin.php?$arg\">t</a>";

include_once("$mytheme/diff.php");
?>
