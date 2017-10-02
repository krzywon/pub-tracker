<?php

require ('DB.php');
require ('pubGeneralFunctions.php');
$host = 'localhost';
$user = 'root';
$pass = '';
$dbase = 'publications';
$comparison = ">=";
$year = "2014";
$firstline = "Article Title, Journal Name, Volume, Issue, First Page, Year, DOI, NG1, NG3, NG7, BT5, IGOR, Authors\r\n";
$filename = "publications_CHRNS_".$year.".doc";
$filename_temp = "publications_CHRNS_".$year."_unsorted.doc";
$mode = 'a';
$queryAll = "SELECT * from sanspublications WHERE (ng3sans = \"1\" OR bt5usans = \"1\") AND year ".$comparison." \"".$year."\"";
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
		$title = str_replace(",", ":;:;:", $line[9]);
		if ($line[15] != NULL) {
			$doi = str_replace(",", ":;:;:", $line[15]);
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
				$authors .= ", ".$authI[0][0].". ".$authI[2];
			}
			else {
				$authors .= ", ".$authI[0][0].". ".$authI[1][0].". ".$authI[2];
			}
		}
		$authors = substr($authors, 2);
		$jentry = $line[5];
		$query = "SELECT journalAbbreviation FROM journalinformation WHERE journalinformation.index = '$jentry'";
		$jTitleResult = mysqli_fetch_row(mysqliconnect($query, $host, $user, $pass, $dbase));
		
		$data =  str_replace(":;:;:", ",", "$authors, \"$title\", ".$jTitleResult[0].", $line[2], $line[4] ($line[1])");
		$data .= "\r\n";
		append_data($filename_temp, $mode, $data);
	}
	$contents = file($filename_temp);
	$firstauthors[] = "";
	foreach($contents as $key=>$value) {
		$pubi = explode(",", $value);
		$author1 = preg_split("/. /", $pubi[0]);
		$size = sizeof($author1) - 1;
		$firstauthors[$key] = $author1[$size];
	}
	asort($firstauthors, SORT_STRING);
	check_exists($filename, '', $firstline);
	foreach($firstauthors as $key=>$value) {
		$data = $contents[$key];
		append_data($filename, $mode, $data);
		print "<p>$data</p>\r\n";
	}
}

?>
