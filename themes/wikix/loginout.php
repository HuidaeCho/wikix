<?if($login){?>
<form action="index.php?<?=$arg?>" method="post">
Logout <input accesskey="l" type="submit" value="<?=$author?>" />
<input type="password" name="password" size="10" />
<input type="hidden" name="author" value="<?=$author?>" />
<?}else{?>
<form action="login.php?<?=$arg?>" method="post">
Login <input accesskey="l" name="author" size="10" maxlength="100" />
<?}?>
<br />
<i><?=runTime()?> sec</i>
<br />
<a name="Bottom" href="#">Top</a>
</form>
