<?
include_once("config.php");
include_once("mywikix/config.php");
include_once("lib/misc.php");
include_once("lib/get_php_vars.php");
$aval = (preg_match("/^.+:(.+):.+$/", $wikiXauthor, $m)?"$scriptdir/$m[1]":"");
include_once("lib/get_mythemeNcss.php");
$action = "file";
include_once("mywikix/header0.php");
include_once("$mytheme/file.php");
include_once("mywikix/footer0.php");
?>
