<?
include_once("config.php");
include_once("mywikix/config.php");
include_once("lib/misc.php");
include_once("lib/get_php_vars.php");

$aval = (preg_match("/^.+:(.+):.+$/", $wikiXauthor, $m)?"$scriptdir/$m[1]":"");
if($referer == "-")
	$referer = "index.php";
if($arg == "?"){
	$m = "wikiXmytheme4$aval";
	$mytheme = (isset($cookie[$m])?$cookie[$m]:$mytheme);
}else
if(is_dir($arg)){
	$mytheme = $arg;
	setcookie("wikiXmytheme4$aval", $mytheme, time()+10*$expireTime);
	header("Location: $referer");
}else{
	setcookie("wikiXmytheme4$aval", "");
	header("Location: $referer");
}
?>
<html>
<head>
<title>MyTheme</title>
</head>
<body>
<a href="index.php">wikiX</a> MyTheme: "<?=$mytheme?>" selected.
</body>
</html>
