<?
$str = "";
if(file_exists($refererBlockCfg)){
	$fp = fopen($refererBlockCfg, "r");
	$str .= fread($fp, filesize($refererBlockCfg));
	fclose($fp);
}
if($refererBlockcfg != "" && ($id = pageid($refererBlockcfg))){
	$str .= page_content($id, "page.version")."\n";
	$str = include_page($refererblockcfg0, $str, 1);
}
if($str == "")
	return;
$str = preg_replace("/[ \t]+/", " ", $str);
if(!($n = preg_match_all("/^([+-] ?.+)/m", $str, $m)))
	return;

$denyfirst = 1;
if($m[1][0][0] == "+")
	$denyfirst = 0;
$refererdenied = "\x01^(?:";
$refererallowed = "\x01^(?:";
for($i=0; $i<$n; $i++){
	$s = preg_quote(trim(substr($m[1][$i], 1)));
	if($s == "")
		continue;
	$s = str_replace(" ", "|", $s);
	if($m[1][$i][0] == "+")
		$refererallowed .= "$s|";
	else
		$refererdenied .= "$s|";
}
$refererdenied = substr($refererdenied, 0, -1).")\x01";
$refererallowed = substr($refererallowed, 0, -1).")\x01";
$denied = 1;
if($denyfirst || $refererallowed == "\x01^(?)\x01"){
	if($refererdenied == "\x01^(?)\x01" ||
	  !preg_match($refererdenied, $referer) ||
	  ($refererallowed != "\x01^(?)\x01" &&
	   preg_match($refererallowed, $referer)))
		$denied = 0;
}else{
	if(preg_match($refererallowed, $referer) &&
	  ($refererdenied == "\x01^(?)\x01" ||
	   !preg_match($refererdenied, $referer)))
		$denied = 0;
}
if($denied){
	closedb($db);
	include_once("mywikix/refererblocked.php");
	exit;
}
?>
