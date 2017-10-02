<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"><html>
<head>
<title>NIST - Center for Neutron Research - Small-Angle Neutron Scattering Group</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="http://www.ncnr.nist.gov/programs/sans/scripts/style.css" type="text/css">
<script src="http://www.ncnr.nist.gov/programs/sans/scripts/java_scripts.js" type="text/javascript"></script>
<style type="text/css">
	body {
	margin: 10px;
	}
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

$host = 'localhost';
$user = 'root';
$pass = '';
$dbase = 'publications';

$title = strip_tags($_POST['title']);
$year = strip_tags($_POST['year']);
$volume = strip_tags($_POST['volume']);
$issue = strip_tags($_POST['issue']);
$firstpage = strip_tags($_POST['firstpage']);
$journal = strip_tags($_POST['journal']);
$authors = strip_tags($_POST['authors']);
$js = strip_tags($_POST['jscheck']);
$pdf = 0;
$nistauthor = 0;
$instr = $_POST['instr'];
$instrvalues = array();
$error_message = '';

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

foreach ($instr as $value) {
	if ($value == TRUE) {
		$instrvalues[] = 1;
	}
	else {
		$instrvalues[] = 0;
	}
}

print "<form action=\"pubaddfinal.php\" method=\"post\">\n";

if ($authors == NULL) {
	$error_message .= "<p>The number of authors was left blank.</p>\n";
} if ($title == NULL) {
	$error_message .= "<p>The article title was omitted.</p>\n";
} if (gettype($authors) != string) {
	$error_message .= "<p>The article title was not formatted properly.</p>\n";
} if ($year == NULL) {
	$error_message .= "<p>The article year was omitted.</p>\n";
} if ($year > '9999' OR $year <= '999') {
	$error_message .= "<p>The article year must be a four digit integer.</p>\n";
} 
if ($volume != 'Press' && $volume != 'Submitted') {
	if ($volume == NULL) {
		$error_message .= "<p>The volume number was omitted.</p>\n";
	} if ($firstpage == NULL) {
		$error_message .= "<p>The first page was omitted.</p>\n";
	} if ($issue == NULL) {
		$error_message .= "<p>The issue was omitted.</p>\n";
	}
}

if ($error_message == NULL) {
	if ($journal == "Other...") {
		print "<p><label for=\"title\">Journal Title:</label><input type=\"text\" size=\"60\" name=\"jtitle\"></p>";
		print "<p><label for=\"title\">Journal Abbreviation:</label><input type=\"text\" size=\"60\" name=\"jabbrev\"></p>";
	} else {
		$getjournalid = "SELECT * FROM abbreviations WHERE abbreviations.Pub_name = '$journal'";
		$journal_temp = mysqli_fetch_row(mysqliconnect($getjournalid, $host, $user, $pass, $dbase));
		$journal = $journal_temp[0];
	}
	
	print "<p>Please enter the authors names in the format \"Last, First, Middle\".</p>\n";
	for ($i = 1; $i <= $authors; $i++) {
	print "<p>\n <label for=\"author$i\">Author #$i:</label>\n";
	print "<input type=\"text\" size=\"60\"name=\"author$i\">\n";
	print "</p>\n";
	}
	
	print "<p>Please verify the spelling of the authors names and the information entered previously.  If everything is correct, please submit the article.</p>\n";
	print "<p>year = $year<br> volume = $volume<br> issue = $issue<br> first page = $firstpage<br> journal title = $journal<br> article title = $title<br> nistauthor = $nistauthor<br> pdf = $pdf<br> instruments (ng1, ng3, ng7, bt5, igor) = $ng1, $ng3, $ng7, $bt5, $igor</p>\n";
	
	print "<input type=\"hidden\" name=\"ng1\" value =\"$instrvalues\">\n";
	print "<input type=\"hidden\" name=\"nistauthor\" value =\"$nistauthor\">\n";
	print "<input type=\"hidden\" name=\"pdf\" value =\"$pdf\">\n";
	print "<input type=\"hidden\" name=\"title\" value =\"$title\">\n";
	print "<input type=\"hidden\" name=\"year\" value =\"$year\">\n";
	print "<input type=\"hidden\" name=\"volume\" value =\"$volume\">\n";
	print "<input type=\"hidden\" name=\"issue\" value =\"$issue\">\n";
	print "<input type=\"hidden\" name=\"firstpage\" value =\"$firstpage\">\n";
	print "<input type=\"hidden\" name=\"journal\" value =\"$journal\">\n";
	print "<input type=\"hidden\" name=\"authors\" value =\"$authors\">\n";
	
	print "<p>\n <INPUT type=\"submit\" value=\"Submit Article\"> <INPUT type=\"reset\">\n </p>\n";
} else {
	print "<p>Please go back and enter the required information.</p>\n";
}

print $error_message;
print "</form>\n";
	
?>

</body>
</html>