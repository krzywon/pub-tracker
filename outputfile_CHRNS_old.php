<?php

require ('DB.php');
include ('pubGeneralFunctions.php');
$host = 'localhost';
$user = 'root';
$pass = '';
$dbase = 'publications';
$filename = 'publications_CHRNS_2010.doc';
$filename_temp = 'publications_CHRNS_2010_unsorted.doc';
$mode = 'a';
$firstline = '';
$queryAll = "SELECT * from publist WHERE (`ng3sans` =\"1\" OR `bt5usans` =\"1\" )AND `year` = \"2010\"";
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
	check_exists($filename_temp, '', $firstline);
	$query = "SELECT * FROM authors";
	$authResult = mysqliconnect($query, $host, $user, $pass, $dbase);
	$rows = mysqli_num_rows($authResult);
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
		$authI = array();
		foreach($authList as $value) {
			mysqli_data_seek($authResult, 0);
			for($j = 0; $j <= $rows; $j++) {
				$rowi = mysqli_fetch_row($authResult);
				if ($value === $rowi[3]) {
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
		$query = "SELECT Pub_abbrev FROM abbreviations WHERE abbreviations.index = '$jentry'";
		$jTitleResult = mysqli_fetch_row(mysqliconnect($query, $host, $user, $pass, $dbase));
		$journalname = str_replace(",", ":;:;:", $jTitleResult[0]);
		$instr = "";
		if ($line[11] == 1 && $line[13] == 1) {
			$instr = "SANS uSANS";
		}
		elseif ($line[11] == 1) {
			$instr = "SANS";
		}
		elseif ($line[13] == 1) {
			$instr = "uSANS";
		}
		else {
			$instr = "ERROR";
		}
		$data = "$authors, \"$title\", $journalname, ".str_replace(", ", ":;:;:", $line[2]).", ".str_replace(",", ":;:;:", $line[4])." (".str_replace(",", ":;:;:", $line[1]).") (".$instr.")";
		$data .= "\r\n";
		append_data($filename_temp, $mode, $data);
		$i++;
	}
	$contents = file($filename_temp);
	$firstauthors[] = "";
	foreach($contents as $key=>$value) {
		$pubi = explode(",", $value);
		$author1 = preg_split("/. /", $pubi[0]);
		$firstauthors[$key] = $author1[0];
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
