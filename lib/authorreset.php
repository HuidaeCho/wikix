<?
$str = "";
if(file_exists($authorResetCfg)){
	$fp = fopen($authorResetCfg, "r");
	$str .= fread($fp, filesize($authorResetCfg));
	fclose($fp);
}
if($authorResetcfg != "" && ($id = pageid($authorResetcfg))){
	$str .= page_content($id, "page.version")."\n";
	$str = include_page($authorresetcfg0, $str, 1);
}
if($str == "")
	return;
$str = preg_replace("/[ \t]+/", " ", $str);
if(!($n = preg_match_all("/^([+-] ?[0-9a-zA-Z\x80-\xff ]+)/m", $str, $m)))
	return;

$denyfirst = 1;
if($m[1][0][0] == "+")
	$denyfirst = 0;
$authordenied = "/^(?:";
$authorallowed = "/^(?:";
for($i=0; $i<$n; $i++){
	$s = trim(substr($m[1][$i], 1));
	if($s == "")
		continue;
	$s = str_replace(" ", "|", $s);
	if($m[1][$i][0] == "+")
		$authorallowed .= "$s|";
	else
		$authordenied .= "$s|";
}
$authordenied = substr($authordenied, 0, -1).")$/";
$authorallowed = substr($authorallowed, 0, -1).")$/";
$denied = 1;
if($denyfirst || $authorallowed == "/^(?)/"){
	if($authordenied == "/^(?)/" || !preg_match($authordenied, $author) ||
	  ($authorallowed != "/^(?)/" && preg_match($authorallowed, $author)))
		$denied = 0;
}else{
	if(preg_match($authorallowed, $author) &&
	  ($authordenied == "/^(?)/" || !preg_match($authordenied, $author)))
		$denied = 0;
}
if($denied){
	$authorok = 0;
	$author = $ip;
}
?>
