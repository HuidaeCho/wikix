<?
$m = "wikiXmytheme4$aval";
$mytheme = (isset($cookie[$m])&&is_dir($cookie[$m])?$cookie[$m]:$mytheme);
$m = "wikiXmycss4$aval";
$mycss = (isset($cookie[$m])?$cookie[$m]:"$mytheme/wikix.css");

include_once("$mytheme/wikix.php");
?>
