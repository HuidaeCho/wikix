<?
$phpversion = phpversion();
$oldphp = ($phpversion>="4.2.0"?"":"_old");
if($oldphp == ""){
	$bs = "\\\\";
	$server = $_SERVER;
	$get = $_GET;
	$post = $_POST;
	$cookie = $_COOKIE;
	$files = $_FILES;
}else{
	$bs = ($phpversion>="4.1.1"?"\\\\":"\\");
	$server = $HTTP_SERVER_VARS;
	$get = $HTTP_GET_VARS;
	$post = $HTTP_POST_VARS;
	$cookie = $HTTP_COOKIE_VARS;
	$files = $HTTP_POST_FILES;
}

ini_set("display_errors", false);
# It doesn't work at all.
#ini_set("magic_quotes_gpc", false);
#ini_set("magic_quotes_runtime", false);

$magic_quotes_gpc = ini_get("magic_quotes_gpc");
$register_globals = ini_get("register_globals");
if($magic_quotes_gpc){
	array_stripslashes($server);
	array_stripslashes($get);
	array_stripslashes($post);
	array_stripslashes($cookie);
}
if($register_globals){
	foreach($get as $key => $val){
		if($key != "get" && $key != "post" && $key != "cookie")
			unset($$key);
	}
	foreach($post as $key => $val){
		if($key != "get" && $key != "post" && $key != "cookie")
			unset($$key);
	}
	foreach($cookie as $key => $val){
		if($key != "get" && $key != "post" && $key != "cookie")
			unset($$key);
	}
}

$uri = str_replace('"', '&quot;', geni_specialchars0($server['REQUEST_URI']));
$query_string = $server['QUERY_STRING'];
$arg = urldecode($query_string);
$script = $server['PHP_SELF'];
$scriptdir = dirname($script);
$scriptdir = ($scriptdir=="/"?"":$scriptdir);
$scriptfile = $server['PATH_TRANSLATED'];
$host = $server['HTTP_HOST'];
$ip = $server['REMOTE_ADDR'];
$agent = $server['HTTP_USER_AGENT'];
$referer = (isset($server['HTTP_REFERER'])?$server['HTTP_REFERER']:"-");

$aid = "wikiXauthor4$scriptdir";
$wikiXauthor = (isset($cookie[$aid])?$cookie[$aid]:"");
$aval = "$scriptdir/";
?>
