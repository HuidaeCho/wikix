<?
include_once("config.php");
include_once("mywikix/config.php");
include_once("lib/misc.php");
include_once("lib/get_php_vars.php");

$aval .= (preg_match("/^.+:(.+):.+$/", $wikiXauthor, $m)?$m[1]:"");
if($referer == "-")
	$referer = "index.php";
if($arg == "?"){
	$m = "wikiXmycss4$aval";
	if(isset($cookie[$m]))
		$mycss = $cookie[$m];
	else{
		$m = "wikiXmytheme4$aval";
		$mytheme = (isset($cookie[$m])?$cookie[$m]:$mytheme);
		$mycss = "$mytheme/wikix.css";
	}
}else
if($arg != ""){
	$mycss = $arg;
	setcookie("wikiXmycss4$aval", $mycss, time()+10*$expireTime);
	header("Location: $referer");
}else{
	setcookie("wikiXmycss4$aval", "");
	header("Location: $referer");
}
?>
<html>
<head>
<title>MyCSS</title>
</head>
<body>
<a href="index.php">wikiX</a> MyCSS: "<?=$mycss?>" selected.
</body>
</html>
