<?php

require ('DB.php');
include ('pubGeneralFunctions.php');
$host = 'localhost';
$user = 'root';
$pass = '';
$dbase = 'publications';
$filename = 'publications.csv';
$mode = 'a';
$firstline = "Article Title, Journal Name, Volume, Issue, First Page, Year, DOI, NG1, NGB 10m, NGB 30m, NG3, NG7, BT5, IGOR, Authors\r\n";
$queryAll = "SELECT * from sanspublications";
$result = array();
$output = array();
$line = NULL;
$error = NULL;

$result = mysqliconnect($queryAll, $host, $user, $pass, $dbase);
if (!mysqli_num_rows($result)) {
	$error .= "<p>No records were found for the query".$queryAll."</p>";
}

if ($error) {
	print "<p>There were errors with the query:</p>".$error;
}
else {
	check_exists($filename, '', $firstline);
	$queryAuthors = "SELECT * FROM authors";
	$authResult = mysqliconnect($queryAuthors, $host, $user, $pass, $dbase);
	$rows = mysqli_num_rows($authResult);
	while($line = mysqli_fetch_row($result)) {
		$authors = "";
		$index = $line[0];
		//TODO: Change to innerjoin - publicationIndex
		$queryAuthorPubLink = "SELECT * FROM authorpublink WHERE publicationIndex = '$index' ORDER BY authornumber";
		$authorList = mysqliconnect($queryAuthorPubLink, $host, $user, $pass, $dbase);
		$title = str_replace(",", ":;:;:", $line[9]);
		if ($line[17] != NULL) {
			$doi = str_replace(",", ":;:;:", $line[17]);
		}
		else {
			$doi = '';
		}
		$authI = array();
		while($value = mysqli_fetch_row($authorList)) {
			var_dump($value);
			mysqli_data_seek($authResult, 0);
			for($i = 0; $i <= $rows; $i++) {
				$rowi = mysqli_fetch_row($authResult);
				if ($value[0] === $rowi[3]) {
					$authI = $rowi;
					break;
				}
			}
			if(!$authI[1]) {
				$authors .= ", ".$authI[2].":;:;: ".$authI[0][0];
			}
			else {
				$authors .= ", ".$authI[2].":;:;: ".$authI[0][0].":;:;: ".$authI[1][0];
			}
		}
		$authors = substr($authors, 2);
		$jentry = $line[5];
		$query = "SELECT journalAbbreviation FROM journalinformation WHERE journalinformation.index = '$jentry'";
		$jTitleResult = mysqli_fetch_row(mysqliconnect($query, $host, $user, $pass, $dbase));
		$journalname = str_replace(",", ":;:;:", $jTitleResult[0]);
		$data = "$title, $journalname, ".str_replace(",", ":;:;:", $line[2]).", ".str_replace(",", ":;:;:", $line[3]).", ".str_replace(",", ":;:;:", $line[4]).", ".str_replace(",", ":;:;:", $line[1]).", ".$doi.", ".$line[10].", ".$line[11].", ".$line[12].", ".$line[13].", ".$line[14].", ".$line[15].", ".$line[16].", ".$authors;
		print "<p>$i. index:$index, Journal: $data</p>\r\n";
		$data .= "\r\n";
		append_data($filename, $mode, $data);
	}
}

?>
