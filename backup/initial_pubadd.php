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

$filename = 'C:\xampp\htdocs\publications\sans_publications_3.txt';
$host = 'localhost';
$user = 'root';
$pass = '';
$dbase = 'publications';
require_once 'DB.php';

if (file_exists($filename)) {
	$pubs = file_get_contents($filename);
	$data = explode("\r", $pubs);
	$num_rows = sizeof($data);
	} else {
	print "<p>File does not exist.</p>\n";
	exit;
}

for($i = 0; $i <= $num_rows; $i++) {	
	print "<p>i = $i</p>\n";
	$row = getline($i, $data);
		
	$authors = getauthors($row);
	$authformat = authornames($authors, $host, $user, $pass, $dbase);
	
	$issuevol = getissuevolume($row);
	$issue = $issuevol[1];
	$volume = $issuevol[0];
	$journalno = journalentry($row, $host, $user, $pass, $dbase);
	
	$final = explode(';', $row);
	$index = sprintf('%u', $final[0]);
	$nistauthor = $final[2];
	$pdf = $final[7];
	$firstpage = $final[6];
	$year = $final[4];
	$title = htmlentities($final[10]);
	$instr = instruments($final[8]);
	$ng1 = $instr[0];
	$ng3 = $instr[1];
	$ng7 = $instr[2];
	$bt5 = $instr[3];
	$igor = $instr[4];
	
	$journalentry = array("$index", "$title", "$volume", "$issue", "$firstpage", "$year", "$journalno", "$nistauthor", "$pdf", "$igor", "$ng1", "$ng3", "$ng7", "$bt5", "$authformat");
	for ($k = 0; $k < sizeof($journalentry); $k++) {
		print "<p>journal information[$k] = $journalentry[$k]</p>\n";
	}
	
	if ($pdf = '1') {
		$pdfform = sprintf("%04s.pdf", $index);
		$query = "INSERT INTO pdfs (pub_id, url) VALUES('$index', '$pdfform')";
		$inject = mysqliconnect($query, $host, $user, $pass, $dbase);
	}
	
	$query = "INSERT INTO publist (pub_id, title, volume, issue, firstpage, year, journal, nistauthor, pdf, igor, ng1sans, ng3sans, ng7sans, bt5usans, auth_ids) VALUES('$journalentry[0]', '$journalentry[1]', '$journalentry[2]', '$journalentry[3]', '$journalentry[4]', '$journalentry[5]', '$journalentry[6]', '$journalentry[7]', '$journalentry[8]', '$journalentry[9]', '$journalentry[10]', '$journalentry[11]', '$journalentry[12]', '$journalentry[13]', '$journalentry[14]')";
	$inject = mysqliconnect($query, $host, $user, $pass, $dbase);
	
	print "<hr>\n";
}

function getline($linenumber, $data) {
	$line = $data[$linenumber];
	return $line;
}

function getauthors($string) {
	$list = explode(";", $string);
	$author01 = $list[1];
	$author02 = $list[11];
	$author03 = $list[12];
	if ($list[13] != 'et al.') {
		$authlist = "$list[1], $list[11], $list[12], $list[13]";
	} else {
		$authlist = "$list[1], $list[11], $list[12]";
	}
	print "<p>authors = $authlist</p>\n";
	return $authlist;
}

function authornames($string, $host, $user, $pass, $dbase) {
	$string2 = str_replace(' ,', '', $string);
	$auth_i = explode(', ', $string2);
	$size = sizeof($auth_i);
	print "<p>auth_i size = $size</p>\n";
	
	for($i = 0; $i < $size; $i++) {
		$auth = $auth_i[$i];
		$auth_format = explode(' ', $auth);
		$firstmiddle = $auth_format[1];
		print "<p>author_result[$i] = $auth_format[0]</p>\n";
		$query = "SELECT author_id FROM authors WHERE authors.lastname = '$auth_format[0]' && authors.firstname = '$firstmiddle[0]' && authors.middlename = '$firstmiddle[1]'";
		$db = mysqliconnect($query, $host, $user, $pass, $dbase);
		$db2 = mysqli_fetch_row($db);
		$db_results = implode(" ", $db2);
		print "<p>authors[$i] = $db_results</p>\n";
		if(!$db_results) {
			$query2 = "INSERT INTO authors (firstname, middlename, lastname) VALUES ('$firstmiddle[0]', '$firstmiddle[1]', '$auth_format[0]')";
			mysqliconnect($query2, $host, $user, $pass, $dbase);
			$db = mysqliconnect($query, $host, $user, $pass, $dbase);
			$db2 = mysqli_fetch_row($db);
			$db_results = implode(' ', $db2);
			print "<p>authors[$i] = $db_results</p>\n";
		}
		$final[$i] = $db_results;
	}
	$result_temp = implode(', ', $final);
	$result = str_replace(', 18', '', $result_temp);
	print "<p>result = $result</p>\n";
	return $result;
}

function getissuevolume ($string) {
	$list = explode(";", $string);
	$issuevol = $list[5];
	$issuevoltemp = explode(' (', $issuevol);
	$issuevoltemp[1] = str_replace(')', '', $issuevoltemp[1]);
	return $issuevoltemp;
}

function journalentry($string, $host, $user, $pass, $dbase) {
	$info = explode(';', $string);
	$query = "SELECT * FROM abbreviations WHERE abbreviations.Pub_name = '$info[3]'";
	$jno = mysqliconnect($query, $host, $user, $pass, $dbase);
	$jkl = mysqli_fetch_row($jno);
	return $jkl[0];
}

function instruments($string) {
	$final = array('0','0','0','0','0');
	$data = explode(' ', str_replace('and ', "", $string));
	for ($i = 0; $i <= sizeof($data); $i++) {
		if ($data[$i] == 'NG1SANS') {
			$final[0] = '1';
		} if ($data[$i] == 'NG3SANS') {
			$final[1] = '1';
		} if ($data[$i] == 'NG7SANS') {
			$final[2] = '1';
		} if ($data[$i] == 'USANS') {
			$final[3] = '1';
		} if ($data[$i] == 'IGOR') {
			$final[4] = '1';
		}
	}
	return $final;
}

function mysqliconnect($query, $host, $user, $pass, $dbase) {
	$db = mysqli_connect($host, $user, $pass, $dbase);
	if (!$db) {
    printf("Connection failed: %s\n", mysqli_connect_error());
    exit();
	}
	print "<p>input = $query</p>\n";
	$query_return = mysqli_query($db, $query);
	mysqli_close($db);
	return $query_return;
}

?>

</body>
</html>