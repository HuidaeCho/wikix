<?
function pm_connect($hostname, $dbname, $username, $password){
	return pg_connect(($hostname!=""?"host=$hostname ":"").
			($username!=""?"user=$username ":"").
			($password!=""?"password=$password ":"").
			"dbname=$dbname");
}

function pm_pconnect($hostname, $dbname, $username, $password){
	return pg_pconnect(($hostname!=""?"host=$hostname ":"").
			($username!=""?"user=$username ":"").
			($password!=""?"password=$password ":"").
			"dbname=$dbname");
}

function pm_close($db){
	return pg_close($db);
}

function pm_query($db, $query){
	return pg_query($db, $query);
}

function pm_fetch_array($result, $row){
	return pg_fetch_array($result, $row, PGSQL_ASSOC);
}

function pm_fetch_object($result, $row){
	return pg_fetch_object($result, $row);
}

function pm_fetch_row($result, $row){
	return pg_fetch_row($result, $row);
}

function pm_fetch_result($result, $row, $field_number){
	return pg_fetch_result($result, $row, $field_number);
}

function pm_free_result($result){
	return pg_free_result($result);
}

function pm_num_rows($result){
	return pg_num_rows($result);
}

function pm_num_fields($result){
	return pg_num_fields($result);
}

function pm_field_name($result, $field_number){
	return pg_field_name($result, $field_number);
}

function pm_last_error($db){
	return pg_last_error($db);
}
?>
