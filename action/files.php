<?
if($pagename0 == "\x02")
	$pagename = $Pagename = "";
if(!$admin){
	if(is_site_hidden()){
		echo "Sorry, it's a hidden site.\n";
		return;
	}else
	if($pagename != "" && is_hidden($Pagename)){
		echo "$pagename: Sorry, it's a hidden page.\n";
		return;
	}
}
if($pagename == ""){
	if(!$admin && $v0 != ""){
		$query = "select page from ${db_}file where id=$v0";
		$result = pm_query($db, $query);
		if(pm_num_rows($result)){
			if(is_locked(
				addslashes(pm_fetch_result($result, 0, 0))))
				$v0 = "";
		}else
			$v0 = "";
		pm_free_result($result);
	}
}else{
	if(!$admin && is_locked($Pagename))
		$v0 = "";
	if(($id = pageid($Pagename))){
		$doit = "display";
		$w[0] = geni_specialchars($pagename0);
		$w[1] = "";
	}else{
		$doit = "goto";
		$w = split_word($pagename0);
		$w[0] = geni_specialchars($w[0]);
		$w[1] = geni_specialchars($w[1]);
	}
}
if($v0 != ""){
	if(invalid_access()){
    		echo "It's not allowed to access the page directly.\n";
		return;
	}

	$query = "select file, page, author from ${db_}file where id=$v0".
				($pagename==""?"":" and page='$Pagename'");
	$result = pm_query($db, $query);
	if(pm_num_rows($result)){
		$data = pm_fetch_array($result, 0);
		if($admin || ($login && $author == $data['author'])){
			$old = $data['file'];
			if(file_exists("$wikiXdir/$old")){
				$new = "file0/deleted".
					preg_replace("/^file/", "", $old);
				mkdirp("$wikiXdir/".dirname($new));
				if(rename("$wikiXdir/$old", "$wikiXdir/$new")){
					$query = "delete from ${db_}file
								where id=$v0";
					$result0 = pm_query($db, $query);

					$d = dirname($old);
					while($d != "" &&
						$d != "file" && $d != "file0" &&
						rmdir("$wikiXdir/$d"))
						$d = dirname($d);
				}else{
					$d = dirname($new);
					while($d != "" && $d != "file0" &&
						rmdir("$wikiXdir/$d"))
						$d = dirname($d);
				}
			}else{
				$query = "delete from ${db_}file where id=$v0";
				$result0 = pm_query($db, $query);

				$d = dirname($old);
				while($d != "" &&
						$d != "file" && $d != "file0" &&
					rmdir("$wikiXdir/$d"))
					$d = dirname($d);
			}
		}
	}
	pm_free_result($result);
}

$query = "select count(id) from ${db_}file".
				($pagename==""?"":" where page='$Pagename'");
$result = pm_query($db, $query);
$n = pm_fetch_result($result, 0, 0);
$FilesOf = ($n?$n:"No")." File".($n>1?"s":"").($pagename==""?"":" of");
pm_free_result($result);

include_once("$mytheme/files.php");
?>
