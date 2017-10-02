<?php

$host = 'localhost';
$user = 'root';
$pass = '';
$dbase = 'publications';

require 'DB.php';
function mysqliconnect($query, $host, $user, $pass, $dbase) {
	$db = mysqli_connect($host, $user, $pass, $dbase);
	if (!$db) {
		printf("Connection failed: %s\n", mysqli_connect_error());
		exit();
	}
	$query_return = mysqli_query($db, $query);
	mysqli_close($db);
	return $query_return;
}

$query = "SELECT id,pdf FROM publist";
$db = mysqli_fetch_row(mysqliconnect($query, $host, $user, $pass, $dbase));
$query_pdf = "SELECT url FROM pdfs";
$db_pdf = mysqli_fetch_row(mysqliconnect($query_pdf, $host, $user, $pass, $dbase));

$size = sizeof($db)/2;

for ($i = 0; $i >= $size; $i++) {
	if ($db[$i] == 0) {
		$pdf = NULL;
	}
	if ($db[$i] == 1) {
		$pdf = $db_pdf[$i];
	}
	$query_change = 'UPDATE publist SET pdf = "$pdf" WHERE pub_id = "$db[$i,1]"';
	mysqliconnect($query_change, $host, $user, $pass, $dbase);
}

?>
