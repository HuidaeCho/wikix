<?
$m = explode(" ", microtime()); $startTime = $m[0] + $m[1];

$Y = date("Y"); $M = date("m"); $D = date("d");
$today = date("Y-m-d"); $now = date("Y-m-d H:i:s");
$timestamp = strtotime($now);

include_once("config.php");
include_once("mywikix/config.php");
include_once("lib/misc.php");
include_once("lib/get_php_vars.php");
include_once("lib/get_mypagenames.php");
include_once("lib/dbm.php");
include_once("lib/DisplayContent.php");
include_once("mywikix/package.php");

opendb($db, $dbHost, $dbName, $dbUser, $dbPass);

if($agentBlock){
	include_once("lib/agentblock.php");
}
if($refererBlock){
	include_once("lib/refererblock.php");
}
if($ipBlock){
	include_once("lib/ipblock.php");
}

$admin = 0;
$author = $ip;
if($wikiXauthor != "" &&
	preg_match("/^(login|admin):(.+):(.+)$/", $wikiXauthor, $m)){
	$query = "select sid from ".($m[1]=="admin"?"admindb":"userdb").
				" where id='$m[2]'";
	$result = pm_query($db, $query);
	$sid = pm_fetch_result($result, 0, 0);
	pm_free_result($result);
	$sid0 = md5($m[3]);
	if($sid0 === $sid){
		$author = $m[2];
		$authorok = 1;
		if($authorReset){
			include_once("lib/authorreset.php");
		}
		if($authorok){
			$admin = ($m[1]=="admin"?1:0);
			if($expireTime > 0)
				setcookie($aid, $wikiXauthor,
						$timestamp+$expireTime);
			else
				setcookie($aid, $wikiXauthor);
		}
	}
}
if($author === $ip){
	$wikiXauthor = "";
	setcookie($aid, "");
}else
if(isset($post['author']) && isset($post['password']) &&
		$author === $post['author'] && !invalid_access()){
	if($post['password'] != ""){
		$author = $post['author'];
		$Password = md5($post['password']);
		$query = "update admindb set pw='$Password' where id='$author'";
		$result = pm_query($db, $query);
		$query = "update userdb set pw='$Password' where id='$author'";
		$result = pm_query($db, $query);
	}
	$admin = 0;
	$author = $ip;
	$wikiXauthor = "";
	setcookie($aid, "");
}

$login = ($author===$ip?0:1);
$aval .= ($login?$author:"");
$wikiXonlybody = (isset($cookie["wikiXonlybody4$aval"])?
					$cookie["wikiXonlybody4$aval"]:0);

$init_wikix = 0;
if($ip === $initIP){
	include_once("lib/init_wikix.php");
}

$v0 = "";
$v1 = "";
$action = "display";
$pagename0 = $wikiXfrontpage0;

if(strpos($arg, "\x01") !== false){
	$arg = "";
	if(isset($get['v0']) && $get['v0'] != "")
		$arg .= "$get[v0],";
	if(isset($get['v1']) && $get['v1'] != "")
		$arg .= "$get[v1],";
	$arg .= (isset($get['action'])&&$get['action']!=""?
			$get['action']:"display")."=";
	$arg .= (isset($get['page'])&&$get['page']!=""?
			$get['page']:$wikiXfrontpage0);

	$h = $f = "";
	if(isset($get['header']) && $get['header'] != "")
		$h .= "$get[header]\n";
	if(isset($get['footer']) && $get['footer'] != "")
		$f .= "$get[footer]\n";
	if(isset($get['p'])){
		if(is_array($get['p'])){
			$n = count($get['p']);
			for($i=0; $i<$n; $i++)
				$h .= "\\def@_".($i+1)."=".$get['p'][$i]."\n";
		}else
			$h .= "\\def@_1=$get[p]\n";
	}
	if($h != ""){
		$m = explode("\n", $wikiXheader);
		$i = count($m) - 1;
		$m[$i] = "$h$m[$i]";
		$wikiXheader = implode("\n", $m);
	}
	if($f != ""){
		$m = explode("\n", $wikiXfooter);
		$m[0] .= "\n$f";
		$wikiXfooter = implode("\n", $m);
	}
}

$useraction = array("display", "doit", "search", "bookmark", "links1", "links2",
		"goto", "edit", "delete", "undelete", "info", "diff", "files");

if(preg_match("/^(?:([0-9]+),(?:([0-9]+),)?)?(:?:?)(".
	"display|doit|search|bookmark|links1|links2|goto|".
	"edit|delete|undelete|info|diff|files|".
	"lock|unlock|hide|unhide|remove|tag|untag|".
	"locksite|unlocksite|hidesite|unhidesite|cleansite|".
	"tagsite|untagsite|recoversite|updateuser|updateadmin|".
	")=(.*)$/s", $arg, $argv)){
	$v0 = $argv[1];
	$v1 = $argv[2];
	switch($argv[3]){
	case ":":
		$wikiXonlybody = !$wikiXonlybody;
		break;
	case "::":
		$wikiXonlybody = !$wikiXonlybody;
		setcookie("wikiXonlybody4$aval", $wikiXonlybody);
		break;
	}
	$action = $argv[4];
	$pagename0 = $argv[5];
}else
if($arg != "")
	$pagename0 = $arg;

$arg = geni_specialchars0($query_string);
$arg = str_replace('"', "&quot;", $arg);

if($action == "search"){
	if(preg_match("/^search=(.*?)&goto=(.*)$/s", $query_string, $m)){
		$m[1] = urldecode($m[1]);
		$m[2] = urldecode($m[2]);
		if($m[1] != "")
			$pagename0 = $m[1];
		else
		if($m[2] != ""){
			$action = "goto";
			$pagename0 = $m[2];
		}else
			$pagename0 = "";
	}
}else
if($action == "goto"){
	if(preg_match("/^goto=(.*?)&search=(.*)$/s", $query_string, $m)){
		$m[1] = urldecode($m[1]);
		$m[2] = urldecode($m[2]);
		if($m[1] != "")
			$pagename0 = $m[1];
		else
		if($m[2] != ""){
			$action = "search";
			$pagename0 = $m[2];
		}else
			$pagename0 = "";
	}
}
if($pagename0 == ""){
	$action = "display";
	$pagename0 = $wikiXfrontpage0;
	$pagename = $wikiXfrontpage;
	$Pagename = $wikiXFrontpage;
	$pageName = $wikiXfrontPage;
	$pagenamE = $wikiXfrontpagE;
}else{
	$pagename = geni_specialchars($pagename0);
	$Pagename = addslashes($pagename0);
	$pageName = geni_urlencode($pagename0);
	$pagenamE = escape_doit($pagename0);
}

switch($action){
case "goto":
	if(pageid($Pagename))
		$action = "display";
	else
	if(pageid0($Pagename))
		$action = "info";
	else
		$action = "edit";
	break;
case "display":
	if($v0 == "" && !pageid($Pagename) && pageid0($Pagename))
		$action = "info";
	break;
}

$wikiXheader = include_page($pagename0, $wikiXheader);
$wikiXfooter = include_page($pagename0, $wikiXfooter);

$EditRedirectedPage = "";
$npages = npages();
$btime = btime();

################################################################################

include_once("lib/get_mythemeNcss.php");
include_once("mywikix/header0.php");
include_once("$mytheme/header.php");

if($DEBUG){
	echo "<b class=\"emphasized\">PLEASE DEBUG ME!</b><br />\n";
	echo "Backend database: $backendDB<br />\n";
	echo "PHP version: $phpversion<br />\n";
	ini_set("display_errors", true);
}

include_once("action/$action.php");

if($admin && !in_array($action, $useraction) && $action != "remove" &&
		$action != "updateuser" && $action != "updateadmin"){
	include_once("action/display.php");
}

include_once("$mytheme/footer.php");
include_once("mywikix/footer0.php");

################################################################################

closedb($db);
?>
