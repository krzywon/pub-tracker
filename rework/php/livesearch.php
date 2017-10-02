<?php

include 'database_class.php';
$connect = new connection();

$q = $_GET['q'];
$id = $_GET['id'];
$input = $_GET['input'];

$qlist = explode(' ', $q);

$result = array();
foreach ($qlist as $key => $value) {
	$rows = ["*"];
	$cons = ["(first_name LIKE '".$value."%' OR middle_name LIKE '".$value."%' OR last_name LIKE '".$value."%')"];
	$intermed = $connect->get_all_results('author', $rows, $cons);
	$result[$key] = array();
	for ($i = 0; $i < sizeof($intermed); $i++) {
		if ($intermed[$i][2] != NULL) {
			$result[$key][$i] = $intermed[$i][1]." ".$intermed[$i][2]." ".$intermed[$i][3];
		}
		else {
			$result[$key][$i] = $intermed[$i][1]." ".$intermed[$i][3];
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

$i = 0;
foreach ($final_results as $final_value) {
	if ($i == 0) {
		$class = " class=\"highlighted\"";
	} else {
		$class = "";
	}
	echo "<p".$class."><a href=\"javascript:fillbox(".$id.", '".$final_value."', '".$input."')\">".$final_value."</a></p>";
	$i++;
}

?> 