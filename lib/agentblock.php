<?
$str = "";
if(file_exists($agentBlockCfg)){
	$fp = fopen($agentBlockCfg, "r");
	$str .= fread($fp, filesize($agentBlockCfg));
	fclose($fp);
}
if($agentBlockcfg != "" && ($id = pageid($agentBlockcfg))){
	$str .= page_content($id, "page.version")."\n";
	$str = include_page($agentblockcfg0, $str, 1);
}
if($str == "")
	return;
$str = preg_replace("/[ \t]+/", " ", $str);
if(!($n = preg_match_all("/^([+-] ?.+)/m", $str, $m)))
	return;

$denyfirst = 1;
if($m[1][0][0] == "+")
	$denyfirst = 0;
$agentdenied = "\x01^(?:";
$agentallowed = "\x01^(?:";
for($i=0; $i<$n; $i++){
	$s = preg_quote(trim(substr($m[1][$i], 1)));
	if($s == "")
		continue;
	$s = str_replace(" ", "|", $s);
	if($m[1][$i][0] == "+")
		$agentallowed .= "$s|";
	else
		$agentdenied .= "$s|";
}
$agentdenied = substr($agentdenied, 0, -1).")\x01";
$agentallowed = substr($agentallowed, 0, -1).")\x01";
$denied = 1;
if($denyfirst || $agentallowed == "\x01^(?)\x01"){
	if($agentdenied == "\x01^(?)\x01" ||
	  !preg_match($agentdenied, $agent) ||
	  ($agentallowed != "\x01^(?)\x01" &&
	   preg_match($agentallowed, $agent)))
		$denied = 0;
}else{
	if(preg_match($agentallowed, $agent) &&
	  ($agentdenied == "\x01^(?)\x01" ||
	   !preg_match($agentdenied, $agent)))
		$denied = 0;
}
if($denied){
	closedb($db);
	include_once("mywikix/agentblocked.php");
	exit;
}
?>
