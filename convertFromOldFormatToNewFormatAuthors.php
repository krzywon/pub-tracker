<?php

$host = 'localhost';
$user = 'root';
$pass = '';
$dbase = 'publications';

//makeAuthorPubLink($host, $user, $pass, $dbase); //Completed 3/21/2012
//calculateNumberOfAuthors($host, $user, $pass, $dbase); //Completed 3/26/2012

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

function makeAuthorPubLink($host, $user, $pass, $dbase)
{
	$query = "SELECT pub_id,auth_ids from sanspublications ORDER BY pub_id;";
	$results = mysqliconnect($query, $host, $user, $pass, $dbase);
	while($row = mysqli_fetch_row($results))
	{
		$pubId = $row[0];
		$authorIds = $row[1];
		$authIds = explode(',', $authorIds);
		for($i = 0; $i < sizeof($authIds); $i++)
		{
			$j = $i + 1;
			$author = $authIds[$i];
			$queryAuth = "INSERT INTO authorpublink (authorIndex, publicationIndex, authorNumber) VALUES ('$author', '$pubId', '$j')";
			$insertResults = mysqliconnect($queryAuth, $host, $user, $pass, $dbase);
		}
	}
}

function calculateNumberOfAuthors($host, $user, $pass, $dbase)
{
	$query = "SELECT pub_id,auth_ids from sanspublications ORDER BY pub_id";
	$results = mysqliconnect($query, $host, $user, $pass, $dbase);
	while($row = mysqli_fetch_row($results))
	{
		$pubID = $row[0];
		$authorIDs = $row[1];
		$authIDs = explode(',', $authorIDs);
		$number = sizeof($authIDs);
		$query2 = "UPDATE sanspublications SET numberOfAuthors = '$number' WHERE pub_id = '$pubID'";
		$insertResults = mysqliconnect($query2, $host, $user, $pass, $dbase);
		var_dump($insertResults);
	}
}

?>