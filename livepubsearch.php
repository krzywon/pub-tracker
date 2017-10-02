<?php

$year = $_GET['year'];
require 'db_credentials.php';
$dbase = 'publications';

$con = mysqli_connect($host, $user, $pass, $dbase);
if (!$con) {
  die('Could not connect: ' . mysqli_error($con));
}

$sql = "SELECT pub_id, title from sanspublications";
if ($year != "0") {
	$sql .= " WHERE year = '$year'";
}
$sql .= " ORDER BY title";
var_dump($sql);
$results = mysqli_fetch_all(mysqli_query($con, $sql));

echo "<option SELECTED>&nbsp;&nbsp;</option>";

foreach ($results as $value) {
	echo "<option value=\"$value[0]\">$value[1]</option>\r";
}

mysqli_close($con);
?>