<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"><html>
<head>
<title>NIST - Center for Neutron Research - Small-Angle Neutron Scattering Group</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="http://www.ncnr.nist.gov/programs/sans/scripts/style.css" type="text/css">
<script src="http://www.ncnr.nist.gov/programs/sans/scripts/java_scripts.js" type="text/javascript"></script>
<style type="text/css">
	th {
	background: blue;
	color: white;
	text-align: center;
	}
	tr td {
	background: white;
	color: black;
	text-align: left;
	border-bottom: 1px solid gray;
	}
	tr td a {
	color: blue;
	text-decoration: underline;
	}
	tr td a:hover {
	color: red;
	text-decoration: underline;
	}
</style>
</head>

<body>
	
<?php

// General INSERT query: $query = "INSERT INTO table (value1, value2, etc) VALUES('$var1', '$var2', '$etc')";
// General SELECT query: $query = "SELECT row1 FROM table WHERE table.row2 = '$variable'";

$host = 'localhost';
$user = 'root';
$pass = '';
$dbase = 'publications';
require_once 'DB.php';

$query1 = "SELECT author_id FROM authors";
$return1 = mysqliconnect($query1, $host, $user, $pass, $dbase);

$i = 0;
while ($row1 = mysqli_fetch_row($return1)) {	
	$final = NULL;
	$result = NULL;
	$line1 = getline('0', $row1);
	print "<p>i = $i</p>\n";
	print "<p>line[$i] = $line1</p>\n";
	$query2 = "SELECT auth_ids, pub_id FROM publist";
	$return2 = mysqliconnect($query2, $host, $user, $pass, $dbase);

	//Check each publication individually for every author
	$j = 0;
	while ($row2 = mysqli_fetch_row($return2)) {
		$bool = 'FALSE';
		$line2 = getline('0', $row2);
		$line3 = getline('1', $row2);
		$list2 = explode(', ', $line2);
		
		//Compare the values in each publist for the authornumber
		$numrows = sizeof($list2);
		for ($k = 0; $k <= $numrows; $k++) {
			if ($line1 == $list2[$k]) {
				$bool = 'TRUE';
				}
		}
		if ($bool != 'FALSE') {
			$result[$j] = $line3;
		}
		$j++;
	}
	
	$final = implode(', ', $result);
	$queryfinal = "UPDATE authors SET publist = '$final' WHERE authors.author_id = '$line1'";
	$inject = mysqliconnect($queryfinal, $host, $user, $pass, $dbase);
	
	print "<p>results = $final</p>\n";
	print "<hr>\n";
	$i++;
}

function getline($linenumber, $data) {
	$line = $data[$linenumber];
	return $line;
}

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

?>

</body>
</html>