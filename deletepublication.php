<?php

$pubtitle = $_GET['pubtitle'];
require 'db_credentials.php';
$dbase = 'publications';
$con = mysqli_connect($host, $user, $pass, $dbase);
if (!$con) {
  die('Could not connect: ' . mysqli_error($con));
} 

$query_pubid = "SELECT pub_id from sanspublications WHERE title = '$pubtitle'";
$matches = mysqli_fetch_row(mysqli_query($con, $query_pubid));
$pubid = $matches[0];

$query_delete_pub = "DELETE FROM sanspublications WHERE pub_id = '$pubid'";
$query_delete_sampenv = "DELETE FROM sampleenvironment WHERE pub_id = '$pubid'";
$query_delete_authpub = "DELETE FROM authorpublink WHERE publicationIndex = '$pubid'";
		
$pubdel[0] = mysqli_query($con, $query_delete_pub);
$pubdel[1] = mysqli_query($con, $query_delete_sampenv);
$pubdel[2] = mysqli_query($con, $query_delete_authpub);

foreach($pubdel as $deleted) {
	var_dump($deleted);
}

mysqli_close($con);

header('Location: http://localhost/publications/modification_js.html');

?>