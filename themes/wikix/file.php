<html>
<head>
<title>wikiX UploadFile</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?=$charSet?>" />
<link rel="stylesheet" type="text/css" href="<?=$mycss?>" />
</head>

<body>
<h3>
<a class="general" href="http://wikix.org">wikiX</a>
<a class="general" href="<?=$uri?>">UploadFile</a>
</h3>
<p>
<?
$p = $get['p'];
$n = $get['n'];
if(!isset($post['subaction']) || $post['subaction'] != "upload"){
	$P = geni_urlencode($p);
	echo
"http://$host$script?p=$P&n=TheNumberOfFiles<br />\n";
	if(!preg_match("/^[0-9]+$/", $n))
		$n = 1;
	if($n > 0){
?>
<form action="<?=$uri?>" method="post" enctype="multipart/form-data">
<input type="hidden" name="subaction" value="upload" />
<table>
<?/*
	<tr><td>
		Maximum size of a file: <?=ini_get("upload_max_filesize")?>B
	</td></tr>
*/?>
		<?for($i=0; $i<$n; $i++){?>
	<tr><td>
		<input type="file" name="file[]" size="50" />
	</td></tr>
		<?}?>
	<tr><td>
		<input type="submit" value="Upload" />
		<input type="reset" value="Reset" />
	</td></tr>
</table>
</form>
<?
	}
}else{
	$p0 = preg_replace("'//+'", "/", "/$p");
	$P0 = geni_urlencode($p0);

	$rootdir = "$wikiXdir/file"; #substr($scriptfile, 0, -4);
	$dir = $rootdir.($p==""?"":$p0);
	$fileP = "file$P0";

	$n = count($files['file']['tmp_name']);
	$str = "";
	$first = 1;
	for($i=0; $i<$n; $i++){
		if(!is_uploaded_file($files['file']['tmp_name'][$i]))
			continue;
		$fname = $files['file']['name'][$i];
		if($magic_quotes_gpc)
			$fname = stripslashes($fname);
		if(preg_match("/$notUploadable/i", $fname)){
			echo "<span class=\"emphasized\">$fname: Rejected for a security reason.</span><br />\n";
			continue;
		}
		if($first){
			if(!file_exists($dir)){
				$subdir = explode("/", $p);
				$path = $rootdir;
				$oldumask = umask(0);
				foreach($subdir as $d){
					$path .= "/$d";
					mkdir($path, 0777);
#					exec("chmod 1777 $path");
				}
				umask($oldumask);
			}
			if(realpath($dir) == $rootdir ||
					!is_dir($dir) || !is_writable($dir)){
				$dir = $rootdir."0";
				$fileP = "wikix/file0";
			}
			$first = 0;
		}
		$ifile = $fname;
		for($j=1; file_exists("$dir/$ifile"); $j++,$ifile="$j::$fname");
		if(!move_uploaded_file($files['file']['tmp_name'][$i],
								"$dir/$ifile"))
			continue;
		chmod("$dir/$ifile", 0644);
		$jfile = geni_urlencode($ifile);
		$ifile = str_replace("\x03", "\\\\",
				geni_specialchars0(escape_wikix($ifile)));
		$str .= "[$ifile|http://$fileP/$jfile]\n";
	}
	echo "<pre>\n$str</pre>\n";
}
?>
</p>
</body>
</html>
