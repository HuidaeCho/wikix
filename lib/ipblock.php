<?
$str = "";
if(file_exists($ipBlockCfg)){
	$fp = fopen($ipBlockCfg, "r");
	$str .= fread($fp, filesize($ipBlockCfg));
	fclose($fp);
}
if($ipBlockcfg != "" && ($id = pageid($ipBlockcfg))){
	$str .= page_content($id, "page.version")."\n";
	$str = include_page($ipblockcfg0, $str, 1);
}
if($str == "")
	return;
$str = preg_replace("/[ \t]+/", " ", $str);
if(!($n = preg_match_all("/^([+-] ?[0-9. ]+)/m", $str, $m)))
	return;

$denyfirst = 1;
if($m[1][0][0] == "+")
	$denyfirst = 0;
$ipdenied = "/^(?:";
$ipallowed = "/^(?:";
for($i=0; $i<$n; $i++){
	$s = str_replace(".", "\\.", trim(substr($m[1][$i], 1)));
	if($s == "")
		continue;
	$s = str_replace(" ", "|", $s);
	if($m[1][$i][0] == "+")
		$ipallowed .= "$s|";
	else
		$ipdenied .= "$s|";
}
$ipdenied = substr($ipdenied, 0, -1).")/";
$ipallowed = substr($ipallowed, 0, -1).")/";
$denied = 1;
if($denyfirst || $ipallowed == "/^(?)/"){
	if($ipdenied == "/^(?)/" || !preg_match($ipdenied, $ip) ||
	  ($ipallowed != "/^(?)/" && preg_match($ipallowed, $ip)))
		$denied = 0;
}else{
	if(preg_match($ipallowed, $ip) &&
	  ($ipdenied == "/^(?)/" || !preg_match($ipdenied, $ip)))
		$denied = 0;
}
if($denied){
	closedb($db);
	include_once("mywikix/ipblocked.php");
	exit;
}
?>
