<html>
<head>
<title>wikiX</title>
<link rel="stylesheet" type="text/css" href="<?=$mycss?>" />
</head>
<body>

<form name="login" action="<?=$uri?>" method="post">
<b>Administrator Login</b><br />
<table>
<tr>
	<td align="right">Author:</td>
	<td><input name="author" /></td>
</tr><tr>
	<td align="right">Password:</td>
	<td><input type="password" name="password" /></td>
</tr><tr>
	<td></td>
	<td><input type="submit" value="Login" />
	<input type="reset" value="Reset" /></td>
</tr>
</table>
</form>

<script language="JavaScript" type="text/javascript">
<!--
document.forms.login.elements.author.focus();
//-->
</script>

</body>
</html>
