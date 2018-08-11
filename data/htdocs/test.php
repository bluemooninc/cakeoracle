<?php
	$hostname = $_SERVER['RDS_HOSTNAME'];
	$username = $_SERVER['RDS_USERNAME'];
	$password = $_SERVER['RDS_PASSWORD'];
	$dbname = $_SERVER['RDS_DB_NAME'];

    $connect = mysqli_connect($hostname, $username, $password);
    mysql_select_db($dbname);

	$sql = "select 1";
//	$sqlq = mysql_query($sql, $connect);

//	mysql_free_result($sqlq);
    mysql_close($connect);
    phpinfo();
