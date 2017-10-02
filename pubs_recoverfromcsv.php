<!DOCTYPE html>
<html>
	<head>
		<title>SANS Publications Submission Form</title>
		<meta charset="UTF-8">
		<link rel="stylesheet" href="http://www.ncnr.nist.gov/programs/sans/scripts/style.css" type="text/css">
		<link rel="stylesheet" href="style.css" type="text/css">
		<script src="http://www.ncnr.nist.gov/programs/sans/scripts/java_scripts.js" type="text/javascript"></script>
		<script src="changetable.js" type="text/javascript"></script>
		<script src="formcheck.js" type="text/javascript"></script>
		<script src="livesearch.js" type="text/javascript"></script>
	</head>
	
	<body>
	
	<?php
	
	// TODO: Make this more general - Use to recover the entire database using a CSV file.
	
	//Use this to recover as many of the authors as possible from the .csv file
	
	$file = "publications.csv";
	$dbase = 'publications';
	include ('pubGeneralFunctions.php');
	require("db_credentials.php");
	
	// Step (1) - Open Pub File and Get All Pubs From DB
	check_exists($file, "");
	$query_getallpubs = "SELECT * FROM sanspublications";
	$pubs = mysqliconnect($query_getallpubs, $host, $user, $pass, $dbase);
	$fp = fopen($file, "r");
	
	$i = 0;
	while (!feof($fp)) {
		$i++;
		// Step (2) - Step through each line of the file
		$line = fgets($fp);
		// Step (2a) - Separate line into parts (comma separated)
		$items = explode(", ", $line);
		// Step (2b) - Get Pub ID from DB based on title
		$title = str_replace(":;:;:", ",", $items[0]);
		$query_pubid = "SELECT pub_id,numberOfAuthors FROM sanspublications WHERE title = \"$title\"";
		$pub_id_array = mysqli_fetch_array(mysqliconnect($query_pubid, $host, $user, $pass, $dbase));
		// TODO: Check if the publication exists branch from here.
		// TODO: If the pub exists, compare all values and update accordingly, including authorpublink, authors (if unfound).
		// TODO: If not found, insert into the sanspublications DB and walk through authors
		$pub_id = $pub_id_array['pub_id'];
		$no_authors = $pub_id_array['numberOfAuthors'];
		print "<h2>Publication #$pub_id:</h2>\n";
		// Step (2c) - Get Auth IDs from DB based on Author List
		for ($j = 14; $j < sizeof($items); $j++) {
			$auth_no = $j - 13;
			$auth_name_raw = $items[$j];
			$auth_separated = explode(":;:;: ", $auth_name_raw);
			$query_author = "SELECT author_id FROM authors WHERE lastname = \"".$auth_separated[0]."\" AND firstname LIKE \"".$auth_separated[1][0]."%\"";
			if (sizeof($auth_separated) == 3) {
				$query_author .= " AND middlename LIKE \"".$auth_separated[2][0]."%\"";
			}
			else {
				$query_author .= " AND middlename = \"\"";
			}
			$author_results_mysqli = mysqliconnect($query_author, $host, $user, $pass, $dbase);
			$no_results = mysqli_num_rows($author_results_mysqli);
			$author_results = mysqli_fetch_array($author_results_mysqli);
			$author_index = $author_results[0];
			if (!$author_index) {
				print "<p class=\"warning\">$auth_name_raw give NO results</p>\n";
			}
			elseif($no_results > 1) {
				print "<p>$auth_name_raw gives MULTIPLE results - ".$no_results."</p>\n";
			}
			
			// Step (2d) - For each author, add item to authpublink
			$query_addauthpublink = "INSERT INTO authorpublink (authorIndex, authorNumber, publicationIndex) VALUES ('$author_index', '$auth_no', '$pub_id')";
			print "<p>$query_addauthpublink";
			$itworked = mysqliconnect($query_addauthpublink, $host, $user, $pass, $dbase);
			if ($itworked) {
				print " - Successful! </p>\n";
			}
			else {
				print " - FAILURE! </p>\n";
			}
		}
		if ($no_authors != $auth_no) {
			print "<p class =\"warning\">The number of authors is not correct</p>\n";
		}
		break;
	}
	?>
	
	</body>
</html>