<?php

require ('DB.php');
include ('pubGeneralFunctions.php');
$host = 'localhost';
$user = 'root';
$pass = '';
$dbase = 'publications';
$filename = 'publications_ALL_20142015.doc';
$filename_temp = 'publications_ALL_20142015_unsorted.doc';
$mode = 'a';
$firstline = "Article Title, Journal Name, Volume, Issue, First Page, Year, DOI, NG1, NG3, NG7, BT5, IGOR, Authors\r\n";
$queryAll = "SELECT * from sanspublications WHERE (year >= \"2014\")";
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
		$queryAuthorPubLink = "SELECT * FROM authorpublink WHERE publicationIndex = '$index' ORDER BY authornumber";
		$authorList = mysqliconnect($queryAuthorPubLink, $host, $user, $pass, $dbase);
		$title = str_replace(":;:;:", ",", $line[9]);
		if ($line[15] != NULL) {
			$doi = str_replace(":;:;:", ",", $line[15]);
		}
		else {
			$doi = '';
		}
		$authI = array();
		while($value = mysqli_fetch_row($authorList)) {
			mysqli_data_seek($authResult, 0);
			for($i = 0; $i <= $rows; $i++) {
				$rowi = mysqli_fetch_row($authResult);
				if ($value[0] === $rowi[3]) {
					$authI = $rowi;
					break;
				}
			}
			$authors .= ", ".$authI[2].", ".$authI[0][0].".";
			if($authI[1] != NULL) {
				$authors .= $authI[1][0].".";
			}
		}
		$authors = substr($authors, 2);
		$jentry = $line[5];
		$query = "SELECT journalAbbreviation FROM journalinformation WHERE journalinformation.index = '$jentry'";
		$jTitleResult = mysqli_fetch_row(mysqliconnect($query, $host, $user, $pass, $dbase));
		$journalname = str_replace(":;:;:", ", ", $jTitleResult[0]);
		
		$data =  str_replace(":;:;:", ", ", "$authors, \"$title\", $journalname, $line[2], $line[4]. $line[1]");
		$data .= "\r\n";
		append_data($filename_temp, $mode, $data);
	}
	$contents = file($filename_temp);
	asort($contents);
	check_exists($filename, '', $firstline);
	foreach($contents as $key => $value) {
		append_data($filename, $mode, $value);
		print "<p>$key -> $value</p>\r\n";
	}
}

?>
