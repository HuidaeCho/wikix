<?
$dbBack = ""; # mysql for MySQL, pg for PostgreSQL
$dbName = "wikix";
$dbHost = "";
$dbUser = "";
$dbPass = "";
$db_ = ""; # DB table prefix
$initIP = ""; # wikiX initializer IP

# wikiX root directory ex) /usr/home/user/html/wikix
$wikiXdir = "";
# Do not use the following line if possible.
#$wikiXdir = realpath(".");

# Warning! Author id can not contain any symbol characters including
# whitespaces. Login system does not allow such an id.
$adminAuthor = "wikiX";
$adminPassword = "[w!k!X|p@ssw*rd]"; # changeable

$charSet = ""; # iso-8859-1 for English, euc-kr for Korean
################################################################################

#$mytheme = "themes/wikix";
$mytheme = "mytheme";

$wikiXfrontpage0 = "wikiX";
$_wikiXfrontpage = 0;

$Interwikimap0 = "InterWikiMap";
$_Interwikimap = 0;

$agentBlock = 0;
$agentBlockCfg = "mywikix/agentblock.cfg";
$agentblockcfg0 = "agentBlockCfg";
$_agentblockcfg = 0;

$refererBlock = 0;
$refererBlockCfg = "mywikix/refererblock.cfg";
$refererblockcfg0 = "refererBlockCfg";
$_refererblockcfg = 0;

$ipBlock = 0;
$ipBlockCfg = "mywikix/ipblock.cfg";
$ipblockcfg0 = "ipBlockCfg";
$_ipblockcfg = 0;

$authorReset = 0;
$authorResetCfg = "mywikix/authorreset.cfg";
$authorresetcfg0 = "authorResetCfg";
$_authorresetcfg = 0;

$moreAdmins = 0;
$noAnonymous = 0;
$nomoreUsers = 0;
$mustLogin = 0;
$uniqLogin = 0;
$pageLock = 0;
$pageHide = 0;
$caseinsensitiveSearch = 0;
$highlightedSearch = 1;
$highlightedSearchExtra = 30;
$expireTime = 1209600; # 14 * 86400;
################################################################################

$path = array(
	"hds2l"		=>	"bin/hds2l",
	"mafi"		=>	"bin/mafi",

	"diff"		=>	"/usr/bin/diff",
	"rm"		=>	"/bin/rm",
	"tex"		=>	"/usr/local/bin/tex",
	"latex"		=>	"/usr/local/bin/latex",
	"dvips"		=>	"/usr/local/bin/dvips",
	"gs"		=>	"/usr/local/bin/gs",
	"gs_alias"	=>	"/usr/local/bin/gs -dTextAlphaBits=4 -dGraphicsAlphaBits=4",
	"pnmcrop"	=>	"/usr/local/bin/pnmcrop",
	"pnmtopng"	=>	"/usr/local/bin/pnmtopng",
	"gnuplot"	=>	"/usr/local/bin/gnuplot",
);
################################################################################

$PageListFT = 0;
################################################################################

# Set your timezone other than that of the server.
#putenv("TZ=");

/******************************************************************************
$script = (isset($_SERVER)?$_SERVER['PHP_SELF']:$HTTP_SERVER_VARS['PHP_SELF']);
$sub = basename(dirname($script));
if($sub != ""){
	switch($sub){
	case "sub1":
		$dbName = $sub;
		break;
	default:
		$dbName .= "_$sub";
		break;
	}
	$wikiXdir .= "/$sub";
}
******************************************************************************/

$DEBUG = 0;

# php 4.0.4 or higher
#ob_start("ob_gzhandler");
?>
