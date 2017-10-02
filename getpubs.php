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

$year = $_POST['year'];
$instrument = strtolower($_POST['instrument']);

$host = 'localhost';
$user = 'root';
$pass = '';
$dbase = 'publications';

$query = "SELECT * FROM publist WHERE";

if ($instrument == 'all') {
	$query .= "";
	$query .= year($year, $instrument);
} else if ($instrument == 'all chrns') {
	$query .= " publist.ng3sans = '1' &&";
	$query .= year($year, $instrument);
} else {
	$query .= " publist.$instrument = '1' &&";
	$query .= year($year, $instrument);
}

function year($year, $instrument) {
	if ($year == 'Press and Submitted') {
		$query = " publist.volume = 'Press' OR publist.volume = 'Submitted'";
	} else if ($year == 'Press') {
		$query = " publist.volume = 'Press'";
	} else if ($year == 'All') {
		$query = "";
	} else {
		$query = " publist.year = '$year' && publist.volume != 'Press' && publist.volume != 'Submitted'";
	}
	return $query;
}

if ($year == 'All' && $instrument == 'all') {
	$query = substr("$query", 0, -6);
	print "<h1>All Publications from All Years</h1>\n";
} else if ($year == 'All' && $instrument != 'all') {
	$query = substr("$query", 0, -3);
	$instrument = strtoupper("$instrument");
	print "<h1>All $instrument Publications</h1>\n";
} else if ($year != 'All' && $instrument == 'all') {
	print "<h1>All $year Publications</h1>\n";
} else {
	$instrument = strtoupper("$instrument");
	print "<h1>$year $instrument Publications</h1>\n";
}

$query .= " ORDER BY title";

getpubs($query, $host, $user, $pass, $dbase);

function mysqliconnect($query, $host, $user, $pass, $dbase) {
	require_once 'DB.php';
	$db = mysqli_connect($host, $user, $pass, $dbase);
	if (!$db) {
    printf("Connection failed: %s\n", mysqli_connect_error());
    exit();
	}
	$query_return = mysqli_query($db, $query);
	mysqli_close($db);
	return $query_return;
}

function getpubs($query, $host, $user, $pass, $dbase) {
$q = mysqliconnect($query, $host, $user, $pass, $dbase);

print "<table><tr><th>#</th><th>Article Title</th><th>Authors</th><th>Journal Title</th><th>Volume</th><th>Issue</th><th>Page Number</th><th>Year</th></tr>\n";
	
if (mysqli_num_rows($q)==0) {
	print "<tr><td>No articles available.</td></tr>";
}	else {
	$i = 0;
	while ($row = mysqli_fetch_row($q)) {
		$i++;
		$query2 = "SELECT Pub_abbrev FROM abbreviations WHERE abbreviations.index = '$row[5]'";
		$jname = mysqliconnect($query2, $host, $user, $pass, $dbase);
		$j = mysqli_fetch_row($jname);
		$journal = htmlspecialchars($j[0], ENT_QUOTES, "UTF-8");
		$title_temp = htmlspecialchars($row[9], ENT_QUOTES, "UTF-8");
		$linkquery = "SELECT doi FROM publist WHERE publist.pub_id = '$row[0]'";
		$link = mysqliconnect($linkquery, $host, $user, $pass, $dbase);
		$l = mysqli_fetch_row($link);
		$title = "<a href=\"$l[0]\">$title_temp</a>";
		$auth_id = NULL;
		$authquery = "SELECT auth_ids FROM publist WHERE publist.pub_id = '$row[0]'";
		$auth = mysqliconnect($authquery, $host, $user, $pass, $dbase);
		$authtemp1 = mysqli_fetch_row($auth);
		$authtemp2 = explode(', ', $authtemp1[0]);
		$size = sizeof($authtemp2);
		for ($j = 0; $j < $size; $j++) {
			$authquery2 = "SELECT firstname, middlename, lastname FROM authors WHERE authors.author_id = '$authtemp2[$j]'";
			$author = mysqliconnect($authquery2, $host, $user, $pass, $dbase);
			$author1 = mysqli_fetch_row($author);
			$firstinit1 = $author1[0];
			$firstinit = $firstinit1[0];
			$middleinit1 = $author1[1];
			$middleinit = substr($middleinit1, 0, 1);
			if ($middleinit != NULL) {
			$auth_id[$j] = "$firstinit. $middleinit. $author1[2]";
		} else {
			$auth_id[$j] = "$firstinit. $author1[2]";
		}
		}
		$authlist_temp = implode(', ', $auth_id);
		$authlist = htmlspecialchars(str_replace(', .', '', $authlist_temp), ENT_QUOTES, "UTF-8");
		$final = "<tr><td>$i</td><td>$title</td><td>$authlist</td><td>$journal</td><td>$row[2]</td><td>$row[3]</td><td>$row[4]</td><td>$row[1]</td></tr>\n";
		print "$final";
	}
}

print '</table>';
}

?>

</body>
</html>