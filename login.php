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

if(!preg_match("/^login:/", $wikiXauthor) ||
		!preg_match("'^http://$host$script'", $referer)){
	setcookie($aid, "login:");
	$aval = "";
	include_once("lib/get_mythemeNcss.php");
	$action = "login";
	include_once("mywikix/header0.php");
	include_once("$mytheme/login.php");
	include_once("mywikix/footer0.php");
	exit;
}else
if($wikiXauthor == "login:"){
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
	$query = "select id from admindb where id='$author'";
	$result = pm_query($db, $query);
	$r = pm_num_rows($result);
	pm_free_result($result);
	if($r){
		closedb($db);
		setcookie($aid, "");
		header("Location: index.php");
		echo "Invalid login!";
		exit;
	}
	$sid = md5("$author:$password".
				($uniqLogin?":$ip:".uniqid(rand(), 1):""));
	$sid0 = md5($sid);
	$query = "select pw from userdb where id='$author'";
	$result = pm_query($db, $query);
	if(($r0 = pm_num_rows($result)))
		$r = pm_fetch_result($result, 0, 0);
	pm_free_result($result);
	if($r0){
		if($Password !== $r){
			closedb($db);
			setcookie($aid, "");
			header("Location: index.php");
			echo "Invalid login!";
			exit;
		}
		$query = "update userdb set mip='$ip', mtime='$now',
						sid='$sid0' where id='$author'";
		$result = pm_query($db, $query);
	}else{
		if($nomoreUsers){
			closedb($db);
			setcookie($aid, "");
			header("Location: index.php");
			echo "Invalid login!";
			exit;
		}
		$query = "insert into userdb
					(id, pw, sid, cip, ctime, mip, mtime)
					values('$author', '$Password', '$sid0',
					'$ip', '$now', '$ip', '$now')";
		$result = pm_query($db, $query);
	}
	closedb($db);
	$wikiXauthor = "login:$author:$sid";
}

unset($post['author']);
unset($post['password']);

include_once("index.php");
?>
