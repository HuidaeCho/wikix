<?
function pm_connect($hostname, $dbname, $username, $password){
	$db=mysql_connect($hostname, $username, $password);
	mysql_select_db($dbname, $db);
	return $db;
}

function pm_pconnect($hostname, $dbname, $username, $password){
	$db=mysql_pconnect($hostname, $username, $password);
	mysql_select_db($dbname, $db);
	return $db;
}

function pm_close($db){
	return mysql_close($db);
}

function pm_query($db, $query){
	return mysql_query($query, $db);
}

function pm_fetch_array($result, $row){
	mysql_data_seek($result, $row);
	return mysql_fetch_assoc($result);
#	return mysql_fetch_array($result, MYSQL_ASSOC);
}

function pm_fetch_object($result, $row){
	mysql_data_seek($result, $row);
	return mysql_fetch_object($result);
}

function pm_fetch_row($result, $row){
	mysql_data_seek($result, $row);
	return mysql_fetch_row($result);
}

function pm_fetch_result($result, $row, $field_number){
	return mysql_result($result, $row, $field_number);
}

function pm_free_result($result){
	return mysql_free_result($result);
}

function pm_num_rows($result){
	return mysql_num_rows($result);
}

function pm_num_fields($result){
	return mysql_num_fields($result);
}

function pm_field_name($result, $field_number){
	return mysql_field_name($result, $field_number);
}

function pm_last_error($db){
	return mysql_error($db);
}
?>
