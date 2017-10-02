<?php

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

function check_exists($filename,$data,$firstline = "") {
	if (!file_exists($filename)) {
		$mode = "w";
		$open = fopen($filename,$mode);
		fclose($open);
	}
}

function append_data($filename,$mode = "a",$data) {
	$open = fopen($filename,$mode);
	if (is_writable($filename)) {
		fwrite($open,$data);
		fclose($open);
	}
	else {
		print "<p>Unable to write to the file, $filename.</p>";
	}
}

function read_all_data($filename,$mode = "r",$line) {
	$open = fopen($filename,$mode);
	if (is_readable($filename)) {
		$file_contents = fread($open);
		return $file_contents;
	}
}

function array_search_recursive($needle, $haystack, $nodes=array()) {     
	foreach ($haystack as $key1=>$value1) {
		if (is_array($value1))
		$nodes = array_search_recursive($needle, $value1, $nodes);
		elseif (($key1 == $needle) or ($value1 == $needle))
		$nodes[] = array($key1=>$value1);
	}
	return $nodes;
}

?>
