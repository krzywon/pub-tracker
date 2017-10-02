<?php

$pubtitle = $_GET['pubtitle'];
$base_id = $_GET['pubid'];
require 'db_credentials.php';
$dbase = 'publications';
$con = mysqli_connect($host, $user, $pass, $dbase);
if (!$con) {
  die('Could not connect: ' . mysqli_error($con));
} 

$query_pubbyid = "SELECT * from sanspublications WHERE pub_id = '$base_id'";
$pubidresults = mysqli_fetch_all(mysqli_query($con, $query_pubbyid));
$pubid_row = $pubidresults[0];

$query_pubid = "SELECT * from sanspublications WHERE title = '$pubtitle' AND pub_id != '$base_id'";
$matches = mysqli_fetch_all(mysqli_query($con, $query_pubid));
$success = NULL;
$i = 0;
$failures = "<ul>";
foreach ($matches as $match) {
	$same = TRUE;
	for($key = 1; $key < sizeof($match); $key++) {
		$value = $match[$key];
		if ($value != $pubid_row[$key]) {
			$same = FALSE;
			break;
		}
	}
	$pubid = $match[0];
	
	$pubdel = array();
	$success = TRUE;
	if ($same) {
		$query_delete_pub = "DELETE FROM sanspublications WHERE pub_id = '$pubid'";
		$query_delete_sampenv = "DELETE FROM sampleenvironment WHERE pub_id = '$pubid'";
		$query_delete_authpub = "DELETE FROM authorpublink WHERE publicationIndex = '$pubid'";
		
		if (!mysqli_query($con, $query_delete_pub)) {
			$success = FALSE;
			$failures .= "<li>pubid: $pubid - did not delete from sanspublications</li>\n";
		}
		if (!$pubdel[$i][1] = mysqli_query($con, $query_delete_sampenv) {
			$success = FALSE;
			$failures .= "<li>pubid: $pubid - did not delete from sampleenvironment</li>\n";
		}
		if (!$pubdel[$i][2] = mysqli_query($con, $query_delete_authpub) {
			$success = FALSE;
			$failures .= "<li>pubid: $pubid - did not delete from authorpublink</li>\n";
		}
	}
	
	$i++;
}

mysqli_close($con);

if ($success) {
	header('Location: http://localhost/publications/modification_js.html');
}
elseif ($success === NULL) {
	echo '<script type="text/javascript">alert("No duplicates were found.")</script>';
	header('Location: http://localhost/publications/modification_js.html');
}
else {
	echo("<p>The deletion process was unsuccessful.</p>");
	echo($failures);
}

?>