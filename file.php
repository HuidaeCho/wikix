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
$author = $ip;
if($wikiXauthor != "" &&
	preg_match("/^(login|admin):(.+):(.+)$/", $wikiXauthor, $m)){
	opendb($db, $dbHost, $dbName, $dbUser, $dbPass);
	$query = "select sid from ${db_}".($m[1]=="admin"?"admindb":"userdb").
				" where id='$m[2]'";
	$result = pm_query($db, $query);
	$sid = pm_fetch_result($result, 0, 0);
	pm_free_result($result);
	$sid0 = md5($m[3]);
	if($sid0 === $sid)
		$author = $m[2];
	else{
		$wikiXauthor = "";
		setcookie($aid, "");
	}
	closedb($db);
}
$aval .= (preg_match("/^.+:(.+):.+$/", $wikiXauthor, $m)?$m[1]:"");
include_once("lib/get_mythemeNcss.php");
$action = "file";
include_once("mywikix/header0.php");
include_once("$mytheme/file.php");
include_once("mywikix/footer0.php");
?>
