<?
$wikiXversion = "v1.1.7c";

$wikiXlogo = "<b>wikiX $wikiXversion</b>";
$wikiXpages = "wikiXpages";
$interWikiMap = "interwiki.map";

# Original WikiWord
#$wikiXword = "(?<![\x02\\\\0-9a-zA-Z])((?:[A-Z][a-z]+){2,})(?![0-9a-zA-Z])/";
# Original wikiXword: wikiX
#$wikiXword = "(?<![\x02\\\\0-9a-zA-Z])([A-Z]?[a-z]+[A-Z](?:[a-z]+[A-Z]?)*)(?![0-9a-zA-Z])";
# Extended wikiXword: wikiX, FreeBSD, PHP(Hmm... it's a bad idea?)
#$wikiXword = "(?<![\x02\\\\0-9a-zA-Z])((?:[A-Z]?[a-z]+[A-Z]+|[A-Z]{2,})[a-zA-Z]*)(?![0-9a-zA-Z])";
# Extended wikiXword: wikiX, FreeBSD
$wikiXword = "(?<![\x02\\\\0-9a-zA-Z])((?:[A-Z]?[a-z]+[A-Z]+|[A-Z]{2,}[a-z])[a-zA-Z]*)(?![0-9a-zA-Z])";

$notUploadable = "^\.|\.(?:cgi|php[0-9]*|[ps]html?)$";

# mailto: should be placed at first.
$netLink = "mailto:|http://|https://|ftp://|telnet://|news://|file://";
$imgExt = "png|bmp|jpg|jpeg|gif";

$htmlDtag =
	# style
	"link|style".
	# table
	"|table|tr|th|td|caption".
	# heading
	"|h1|h2|h3|h4|h5|h6".
	# listing
	"|ul|ol|dl|li|dt|dd".
	# font
	"|b|i|tt|sup|sub|big|small".
	# block
	"|pre|blockquote|p|div|span".
	# link
	"|a".
	# linebreak
	"|nobr";

$htmlStag =
	# comment
	"!--".
	# link
	"|img".
	# linebreak
	"|br|hr";

################################################################################
$wikiXheaderFile = "mywikix/wikix.header";
$wikiXfooterFile = "mywikix/wikix.footer";

$fp = fopen($wikiXheaderFile, "r");
$wikiXheader = substr(fread($fp, filesize($wikiXheaderFile)), 0, -1);
fclose($fp);

$fp = fopen($wikiXfooterFile, "r");
$wikiXfooter = fread($fp, filesize($wikiXfooterFile));
fclose($fp);
################################################################################

$db = 0;
srand((double)microtime()*1000000);
?>
