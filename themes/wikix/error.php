<h1 class="title">ERROR</h1>
<br />
	<?
	if($error=='hidden_site'){echo $msg['HIDDEN SITE'];}
	if($error=='no_page'){echo $msg['NO_SUCH_PAGE'];}
	if($error=='hidden_page'){echo $msg['HIDDEN PAGE'];} 
	if($error=='no_version'){echo $msg['NO_SUCH_VERSION'];}
	?>
	<hr>
	<a href="javascript:history.go(-1)">GoBack</a>

