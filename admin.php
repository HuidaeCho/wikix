<?
include_once("config.php");
include_once("mywikix/config.php");
include_once("lib/misc.php");
include_once("lib/get_php_vars.php");
include_once("lib/dbm.php");

if(invalid_access()){
    	echo "It's not allowed to access the page directly.\n";
	exit;
}

$now = date("Y-m-d H:i:s");
$author = (isset($post['author'])?$post['author']:"");
$password = (isset($post['password'])?$post['password']:"");

if(!preg_match("/^admin:/", $wikiXauthor) ||
		!preg_match("'^http://$host$script'", $referer)){
	setcookie($aid, "admin:");
	include_once("lib/get_mythemeNcss.php");
	$action = "admin";
	include_once("mywikix/header0.php");
	include_once("$mytheme/admin.php");
	include_once("mywikix/footer0.php");
	exit;
}else
if($wikiXauthor == "admin:"){
#	$author = trim($author);
	if($author == "" ||
	   !preg_match("/^[0-9a-zA-Z\x80-\xff]+$/", $author)){
		setcookie($aid, "");
		header("Location: index.php");
		echo "Invalid login!";
		exit;
	}
	$Password = md5($password);
	opendb($db, $dbHost, $dbName, $dbUser, $dbPass);
	$query = "select pw from ${db_}admindb where id='$author'";
	$result = pm_query($db, $query);
	$r = (pm_num_rows($result)&&$Password==pm_fetch_result($result, 0, 0)?
									1:0);
	pm_free_result($result);
	if(!$r){
		closedb($db);
		setcookie($aid, "");
		header("Location: index.php");
		echo "Invalid login!";
		exit;
	}
	$sid = md5("$author:$password".
				($uniqLogin?":$ip:".uniqid(rand(), 1):""));
	$query = "update ${db_}admindb set mip='$ip', mtime='$now',
					sid='".md5($sid)."' where id='$author'";
	$result = pm_query($db, $query);
	closedb($db);
	$wikiXauthor = "admin:$author:$sid";
}

if($moreAdmins && preg_match("/^admin:$adminAuthor:(.+)$/", $wikiXauthor, $m)){
	$adminauthor = (isset($post['adminauthor'])?$post['adminauthor']:"");
	$adminpassword = (isset($post['adminpassword'])?
						$post['adminpassword']:"");

	opendb($db, $dbHost, $dbName, $dbUser, $dbPass);
	$query = "select sid from ${db_}admindb where id='$adminAuthor'";
	$result = pm_query($db, $query);
	$sid = pm_fetch_result($result, 0, 0);
	pm_free_result($result);
	$sid0 = md5($m[1]);
	if($sid0 !== $sid){
		closedb($db);
		setcookie($aid, "");
		header("Location: index.php");
		echo "Invalid login!";
		exit;
	}
	if($adminauthor != "" && $adminauthor !== $adminAuthor){
		$query = "select id from ${db_}admindb where id='$adminauthor'";
		$result = pm_query($db, $query);
		$r = pm_num_rows($result);
		pm_free_result($result);
		if($r){
			if($adminpassword == ""){
				$query = "delete from ${db_}admindb
						where id='$adminauthor'";
				$result = pm_query($db, $query);
				warn("Administrator <span class=\"general\">$adminauthor</span> removed.");
			}else
				warn("Administrator <span class=\"general\">$adminauthor</span> already exists.");
		}else
	   	if(!preg_match("/^[0-9a-zA-Z\x80-\xff]+$/", $adminauthor))
			warn("AdminAuthor can not contain any symbol characters including whitespaces.");
		else
		if($adminpassword == "")
			warn("AdminPassword is empty.");
		else{
			$query = "insert into ${db_}admindb (id, pw,
						cip, ctime, mip, mtime)
						values('$adminauthor',
						'".md5($adminpassword)."',
						'$ip', '$now', '$ip', '$now')";
			$result = pm_query($db, $query);
			$query = "delete from ${db_}userdb where id='$adminauthor'";
			$result = pm_query($db, $query);
			warn("New administrator <span class=\"general\">$adminauthor</span> added.");
		}
	}else
	if($adminauthor === $adminAuthor)
		warn("<span class=\"general\">$adminAuthor</span> can neither be added nor removed.");
	closedb($db);
}

unset($post['author']);
unset($post['password']);

include_once("index.php");
?>
