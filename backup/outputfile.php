<?php

require ('DB.php');
include ('pubGeneralFunctions.php');
$host = 'localhost';
$user = 'root';
$pass = '';
$dbase = 'publications';
$filename = 'publications.csv';
$mode = 'a';
$firstline = "Article Title, Journal Name, Volume, Issue, First Page, Year, DOI, NG1, NG3, NG7, BT5, IGOR, Authors\r\n";
$queryAll = "SELECT * from publist";
$result = array();
$output = array();
$line = NULL;
$error = NULL;

$result = mysqliconnect($queryAll, $host, $user, $pass, $dbase);
if (!mysqli_num_rows($result)) {
	$error .= "<p>No records were found for the query".$queryAll."</p>";
}
else {
	while($line = mysqli_fetch_row($result)) {
		$output[] = $line;
	}
}

if ($error) {
	print "<p>There were errors with the query:</p>".$error;
}
else {
	check_exists($filename, '', $firstline);
	$i = 0;
	while($output[$i]) {
		$line = $output[$i];
		$index = $line[0];
		$title = str_replace(",", ":;:;:", $line[9]);
		if ($line[15] != NULL) {
			$doi = str_replace(",", ":;:;:", $line[15]);
		}
		else {
			$doi = '';
		}
		$authors = "";
		$authList = explode(', ', $line[6]);
		foreach($authList as $value) {
			$query = "SELECT firstname, middlename, lastname FROM authors WHERE author_id = '$value'";
			$authResult = mysqli_fetch_row(mysqliconnect($query, $host, $user, $pass, $dbase));
			if(!$authResult[1]) {
				$authors .= ", ".$authResult[2].":;:;: ".$authResult[0][0];
			}
			else {
				$authors .= ", ".$authResult[2].":;:;: ".$authResult[0][0].":;:;: ".$authResult[1][0];
			}
		}
		$authors = substr($authors, 2);
		$jentry = $line[5];
		$query = "SELECT Pub_abbrev FROM abbreviations WHERE abbreviations.index = '$jentry'";
		$jTitleResult = mysqli_fetch_row(mysqliconnect($query, $host, $user, $pass, $dbase));
		$journalname = str_replace(",", ":;:;:", $jTitleResult[0]);
		$data = "$title, $journalname, ".str_replace(",", ":;:;:", $line[2]).", ".str_replace(",", ":;:;:", $line[3]).", ".str_replace(",", ":;:;:", $line[4]).", ".str_replace(",", ":;:;:", $line[1]).", ".$doi.", ".$line[10].", ".$line[11].", ".$line[12].", ".$line[13].", ".$line[14].", ".$authors;
		print "<p>$i. index:$index, Journal: $data</p>\r\n";
		$data .= "\r\n";
		append_data($filename, $mode, $data);
		$i++;
	}
}

?>
