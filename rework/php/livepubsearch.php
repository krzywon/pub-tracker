<?php

include 'database_class.php';
$connect = new connection();

$year = $_GET['year'];

$cols = ["id", "title"];
$tbl = "publication";
$con = array();

if ($year != "0") {
	$con[0] = "year = '$year'";
}
$order = "title";
$results = $connect->get_all_results($tbl, $cols, $con, $order);

echo "<option SELECTED>&nbsp;&nbsp;</option>";

foreach ($results as $value) {
	echo "<option value=\"$value[0]\">$value[1]</option>\r";
}

mysqli_close($con);
?>