<h1 class="title"><a accesskey="z" class="general" href="index.php?display=<?=$pageName?>">diff of <?=$pagename?></a></h1>

<p><b>
<?=
"page id: $id, $page[hits] $Hits, ".
($page['locked']?"locked":"unlocked").", ".
($page['hidden']?"hidden":"unhidden").", ".
"current: v$page[version]<br />\n".
"v$v0:$data0[mtime]($size0), v$v1:$data1[mtime]($size1)<br />\n".
"diff v$v0:$data0[author] v$v1:$data1[author]
"?>
</b></p>

<table width="100%" cellspacing="0px" cellpadding="0px">

<?
#$diff_method = "geni0";
#$diff_method = "geni";
$diff_method = "diff0";
#$diff_method = "diff";
$hdiff = 1;
#$diff_method = "ext";

switch($diff_method){
################################################################################
case "geni0":
for($s=0; $s<$nlines0&&$s<$nlines1&&$line0[$s]===$line1[$s]; $s++)
	echo "<tr><td><tt>= $line1[$s]</tt></td></tr>\n";
for($e0=$nlines0-1,$e1=$nlines1-1; $e0>=$s&&$e1>=$s&&$line0[$e0]===$line1[$e1];
		$e0--,$e1--);
$e0++;
$e1++;
for($i0=$s,$i1=$s; $i0<$e0; $i0++){
	for($i=$i1; $i<$e1&&$line0[$i0]!==$line1[$i]; $i++);
	if($i < $e1){
		for($j=$i1; $j<$i; $j++)
			echo "<tr><td class=\"diff_added\"><tt>+ $line1[$j]</tt></td></tr>\n";
		echo "<tr><td><tt>= $line1[$j]</tt></td></tr>\n";
		$i1 = $i + 1;
	}else{
		echo "<tr><td class=\"diff_deleted\"><tt>- $line0[$i0]</tt></td></tr>\n";
		$j = $i0 + 1;
		if($i1 < $e1)
			for(; $j<$e0&&$line0[$j]!==$line1[$i1]; $j++);
		if($j < $e0){
			for($i=$i0+1; $i<$j; $i++)
				echo "<tr><td class=\"diff_deleted\"><tt>- $line0[$i]</tt></td></tr>\n";
			$i0 = $j - 1;
		}
	}
}
for(; $i1<$e1; $i1++)
	echo "<tr><td class=\"diff_added\"><tt>+ $line1[$i1]</tt></td></tr>\n";
for($i1=$e1; $i1<$nlines1; $i1++)
	echo "<tr><td><tt>= $line1[$i1]</tt></td></tr>\n";
break;
################################################################################
case "geni":
for($i0=0,$i1=0; $i0<$nlines0; $i0++){
	for($i=$i1; $i<$nlines1&&$line0[$i0]!==$line1[$i]; $i++);
	if($i < $nlines1){
		for($j=$i1; $j<$i; $j++)
			echo "<tr><td class=\"diff_added\"><tt>+ $line1[$j]</tt></td></tr>\n";
		echo "<tr><td><tt>= $line1[$j]</tt></td></tr>\n";
		$i1 = $i + 1;
	}else{
		echo "<tr><td class=\"diff_deleted\"><tt>- $line0[$i0]</tt></td></tr>\n";
		$j = $i0 + 1;
		if($i1 < $nlines1)
			for(; $j<$nlines0&&$line0[$j]!==$line1[$i1]; $j++);
		if($j < $nlines0){
			for($i=$i0+1; $i<$j; $i++)
				echo "<tr><td class=\"diff_deleted\"><tt>- $line0[$i]</tt></td></tr>\n";
			$i0 = $j - 1;
		}
	}
}
for(; $i1<$nlines1; $i1++)
	echo "<tr><td class=\"diff_added\"><tt>+ $line1[$i1]</tt></td></tr>\n";
break;
################################################################################
case "diff0":
for($s=0; $s<$nlines0&&$s<$nlines1&&$line0[$s]===$line1[$s]; $s++)
	echo "<tr><td><tt>= $line1[$s]</tt></td></tr>\n";
for($e0=$nlines0-1,$e1=$nlines1-1; $e0>=$s&&$e1>=$s&&$line0[$e0]===$line1[$e1];
		$e0--,$e1--);
$e0++;
$e1++;
$m = $n = $s;
$delta = lcs($line0, $line1, $l, $s, $e0, $e1);
for($i=0; $i<$l; $i++,$m++,$n++){
	list($x, $y) = explode(",", $delta[$i]);
	if($hdiff && $x > $m && $y > $n){
		for(; $m<$x&&$n<$y; $m++,$n++){
			echo "<tr><td class=\"diff_modified\"><tt>* ";
			$l0 = str2charray($line0[$m], $nl0);
			$l1 = str2charray($line1[$n], $nl1);
			for($is=0; $is<$nl0&&$s<$nl1&&$l0[$is]===$l1[$is];$is++)
				echo $l1[$is];
			for($ie0=$nl0-1,$ie1=$nl1-1; $ie0>=$is&&$ie1>=$is&&
					$l0[$ie0]===$l1[$ie1]; $ie0--,$ie1--);
			$ie0++;
			$ie1++;
			$im = $in = $is;
			$idelta = lcs($l0, $l1, $il, $is, $ie0, $ie1);
			for($ii=0; $ii<$il; $ii++,$im++,$in++){
				list($ix, $iy) = explode(",", $idelta[$ii]);
				if($ix > $im){
					echo "<span class=\"diff_modified_deleted\">";
					for(; $im<$ix; $im++)
						echo $l0[$im];
					echo "</span>";
				}
				if($iy > $in){
					echo "<span class=\"diff_modified_added\">";
					for(; $in<$iy; $in++)
						echo $l1[$in];
					echo "</span>";
				}
				if($in < $ie1)
					echo $l1[$in];
			}
			for($ii=$ie1; $ii<$nl1; $ii++)
				echo $l1[$ii];
			echo "</td></tr>\n";
		}
	}
	if($x > $m){
		for(; $m<$x; $m++)
			echo "<tr><td class=\"diff_deleted\"><tt>- $line0[$m]</tt></td></tr>\n";
	}
	if($y > $n){
		for(; $n<$y; $n++)
			echo "<tr><td class=\"diff_added\"><tt>+ $line1[$n]</tt></td></tr>\n";
	}
	if($n < $e1)
		echo "<tr><td><tt>= $line1[$n]</tt></td></tr>\n";
}
for($i=$e1; $i<$nlines1; $i++)
	echo "<tr><td><tt>= $line1[$i]</tt></td></tr>\n";
break;
################################################################################
case "diff":
$m = $n = 0;
$delta = lcs($line0, $line1, $l);
for($i=0; $i<$l; $i++,$m++,$n++){
	list($x, $y) = explode(",", $delta[$i]);
	if($hdiff && $x > $m && $y > $n){
		for(; $m<$x&&$n<$y; $m++,$n++){
			echo "<tr><td class=\"diff_modified\"><tt>* ";
			$l0 = str2charray($line0[$m], $nl0);
			$l1 = str2charray($line1[$n], $nl1);
			$im = $in = 0;
			$idelta = lcs($l0, $l1, $il);
			for($ii=0; $ii<$il; $ii++,$im++,$in++){
				list($ix, $iy) = explode(",", $idelta[$ii]);
				if($ix > $im){
					echo "<span class=\"diff_modified_deleted\">";
					for(; $im<$ix; $im++)
						echo $l0[$im];
					echo "</span>";
				}
				if($iy > $in){
					echo "<span class=\"diff_modified_added\">";
					for(; $in<$iy; $in++)
						echo $l1[$in];
					echo "</span>";
				}
				if($in < $nl1)
					echo $l1[$in];
			}
			echo "</td></tr>\n";
		}
	}
	if($x > $m){
		for(; $m<$x; $m++)
			echo "<tr><td class=\"diff_deleted\"><tt>- $line0[$m]</tt></td></tr>\n";
	}
	if($y > $n){
		for(; $n<$y; $n++)
			echo "<tr><td class=\"diff_added\"><tt>+ $line1[$n]</tt></td></tr>\n";
	}
	if($n < $nlines1)
		echo "<tr><td><tt>= $line1[$n]</tt></td></tr>\n";
}
break;
################################################################################
case "ext":
$file0 = tempnam("tmpfile", "wikiXdiff");
$file1 = tempnam("tmpfile", "wikiXdiff");
$fp0 = fopen($file0, "w");
$fp1 = fopen($file1, "w");
fwrite($fp0, $content0);
fwrite($fp1, $content1);
fclose($fp0);
fclose($fp1);
ob_start();
system("$path[diff] $file0 $file1");
$str = ob_get_contents();
ob_end_clean();
unlink($file0);
unlink($file1);
$line = explode("\n", $str);
$nlines = count($line);
for($i=0; $i<$nlines; $i++)
	echo "<tr><td".
			($line[$i][0]=="<"?" class=\"diff_deleted\"":
			($line[$i][0]==">"?" class=\"diff_added\"":"")).
		"><tt> $line[$i]</tt></td></tr>\n";
break;
################################################################################
}
$v_ = "$v1,";
if(preg_match("/^\\\\RedirectTo:([^\r\n]+)/", $line1[0]))
	$v_ = "";
?>

</table>

<hr noshade />

<table width="100%" cellspacing="0px" cellpadding="0px" style="margin:0px;">
<tr valign="top"><td>

<a accesskey="x" href="index.php?<?="${v_}display=$pageName"?>">ViewPage</a> |
<a href="index.php?info=<?=$pageName?>">info</a> |
<?=$diff_do?>
<br />
<i>
<?="$data1[mtime] v$v1:$author_do"?>
<?=$current_do?>
<br />
<?="$page[hits] $hits"?>
</i>

</td><td align="right">

<?include_once("$mytheme/loginout.php")?>

</td></tr>
</table>
