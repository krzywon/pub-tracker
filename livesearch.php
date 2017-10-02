<?php

$q = $_GET['q'];
$id = $_GET['id'];
$input = $_GET['input'];
$host = 'localhost';
$user = 'root';
$pass = '';
$dbase = 'publications';

$con = mysqli_connect($host, $user, $pass, $dbase);
if (!$con) {
  die('Could not connect: ' . mysqli_error($con));
}

$qlist = explode(' ', $q);

$result = array();
foreach ($qlist as $key => $value) {
	$sql = "SELECT * from authors WHERE (firstname LIKE '".$value."%' OR middlename LIKE '".$value."%' OR lastname LIKE '".$value."%')";
	$intermed = mysqli_fetch_all(mysqli_query($con,$sql));
	$result[$key] = array();
	for ($i = 0; $i < sizeof($intermed); $i++) {
		if ($intermed[$i][1] != NULL) {
			$result[$key][$i] = $intermed[$i][0]." ".$intermed[$i][1]." ".$intermed[$i][2];
		}
		else {
			$result[$key][$i] = $intermed[$i][0]." ".$intermed[$i][2];
		}
	}
}

if (sizeof($result) == 1) {
	$final_results = $result[$key];
} elseif (sizeof($result) > 1) {
	$final_results = call_user_func_array('array_intersect', $result);
} else {
	exit();
}

asort($final_results);

foreach ($final_results as $final_value) {
	echo "<p><a href=\"javascript:fillbox(".$id.", '".$final_value."', '".$input."')\">".$final_value."</a></p>";
}


mysqli_close($con);
?> 