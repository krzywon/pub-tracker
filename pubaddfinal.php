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

// Replace with db_credentials.php
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

function addleadingzero($number, $string) {
	$numString = strval($number);
	if ($number < 10) {
		$final = $string."0".$numString;
	} else {
		$final = $string.$numString;
	}
	return $final;
}

$title = $_POST['title'];
$year = $_POST['year'];
$volume = $_POST['volume'];
$issue = $_POST['issue'];
$firstpage = $_POST['firstpage'];
$journal = $_POST['journal'];
print "<p>journal = $journal</p>";
$authors = $_POST['authors'];
@$jtitle = $_POST['jtitle'];
@$jabbrev = $_POST['jabbrev'];
$doi = $_POST['doi'];

include 'checkMetrics.php';

if ($journal == "Other...") {
	$jentry = "INSERT INTO journalinformation (journalName, journalAbbreviation) VALUES ('$jtitle', '$jabbrev')";
	$jentry2 = mysqliconnect($jentry, $host, $user, $pass, $dbase);
	$getjournalid = "SELECT * FROM journalinformation WHERE journalinformation.journalName = '$jtitle'";
	$journal_temp = mysqli_fetch_row(mysqliconnect($getjournalid, $host, $user, $pass, $dbase));
	$journal = $journal_temp[0];
}

//TODO: pub_id -> publicationIndex, journal -> journalIndex, author_id ->authorIndex, index -> journalIndex
	
$queryfinal = "INSERT INTO sanspublications (year, volume, issue, firstpage, journal, numberOfAuthors, nistauthor, pdf, title, ng1sans, ngb10msans, ngb30msans, ng3sans, ng7sans, bt5usans, igor, doi) VALUES('$year', '$volume', '$issue', '$firstpage', '$journal', '$authors', '$nistauthor', '$pdf', '$title', '$ng1', '$ngb10m', '$ngb30m', '$ng3', '$ng7', '$bt5', '$igor', '$doi')";
$jname = mysqliconnect($queryfinal, $host, $user, $pass, $dbase);

$getpubid = "SELECT pub_id FROM sanspublications WHERE sanspublications.title = '$title' && sanspublications.volume = '$volume' && sanspublications.issue = '$issue' && sanspublications.firstpage = '$firstpage' && sanspublications.doi = '$doi'";
$pubid = @implode('', mysqli_fetch_row(mysqliconnect($getpubid, $host, $user, $pass, $dbase)));
@print "<p>publication = $pubid</p>\n";

$queryse = "INSERT INTO sampleenvironment (pub_id, sample_changer, rheometer, shear_cell_boulder, shear_cell_12plane, shear_cell_plateplate, closed_cycle_refrigerator, electromagnet, superconducting_magnet, polarization, humidity_cell, user_equipment, other) VALUES('$pubid', '$changer', '$rheometer', '$bsc', '$sc12plane', '$scplateplate', '$ccr', '$em', '$scm', '$pa', '$humidity', '$userequip', '$otherequip')";
$seequip = mysqliconnect($queryse, $host, $user, $pass, $dbase);

for ($i = 1; $i <= $authors; $i++) {
	$currentAuthor = addleadingzero($i, 'author');
	$authorNoPeriods = preg_replace("/\./", "", $_POST["$currentAuthor"]);
	$author = explode(' ', $authorNoPeriods);
	if(@$author[2] === NULL) {
		@$author[2] = $author[1];
		$author[1] = NULL;
	}
	$query = "SELECT author_id FROM authors WHERE authors.lastname = '$author[2]' && authors.firstname = '$author[0]' && authors.middlename = '$author[1]'";
	$author_i = mysqliconnect($query, $host, $user, $pass, $dbase);
	$auth = @implode('', mysqli_fetch_row($author_i));
	if ($auth == NULL) {
		$query2 = "INSERT INTO authors (lastname, firstname, middlename) VALUES ('$author[2]', '$author[0]', '$author[1]')";
		$author_j = mysqliconnect($query2, $host, $user, $pass, $dbase);
		$author_k = mysqliconnect($query, $host, $user, $pass, $dbase);
		$auth = implode('', mysqli_fetch_row($author_k));
	}
	$query3 = "INSERT INTO authorpublink (authorIndex, publicationIndex, authorNumber) VALUES ('$auth', '$pubid', '$i')";
	mysqliconnect($query3, $host, $user, $pass, $dbase);
	print "<p>author_id = $auth</p>\n";
}

print "<p>Your article has been successfully entered.</p>\n";
print "<p>year = $year<br> volume = $volume<br> issue = $issue<br> first page = $firstpage<br> journal title = $jtitle<br> article title = $title<br> nistauthor = $nistauthor<br> pdf = $pdf<br> instruments (ng1, ng3, ng7, ngb10m, ngb30m, bt5, igor) = $ng1, $ng3, $ng7, $ngb10m, $ngb30m, $bt5, $igor</p>\n";

?>

<p><a href="http://localhost/publications/injection.html">Submit Another Article</a></p>

</form>

</body>
</html>