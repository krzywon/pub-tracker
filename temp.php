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
include 'pubGeneralFunctions.php';

foreach ($instr as $value) {
	if ($value == TRUE) {
		$instrvalues[] = 1;
	}
	else {
		$instrvalues[] = 0;
	}
}



?>

</body>
</html>
