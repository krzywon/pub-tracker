<?php

// #TODO: Finish this. There is no substance here.
// Compare posted values to current values and modify changed values.

include 'publication_class.php';

// Pub ID
var_dump($_POST);
$pub_id = $_POST['pub_id'];

// Bibliographic Information
$title = $_POST['title'];
$year = $_POST['year'];
$volume = $_POST['volume'];
$issue = $_POST['issue'];
$firstpage = $_POST['firstpage'];
$journal_id = $_POST['journal'];
$doi = $_POST['doi'];

// Instrumentation and NIST associtations
$pdf = $_POST['pdf'];
$nistauthor = $_POST['nistauthor'];
$ng1 = $_POST['ng1'];
$ngb10msans = $_POST['ngb10msans'];
$ngb30msans = $_POST['ngb30msans'];
$ng3sans = $_POST['ng3sans'];
$ng7sans = $_POST['ng7sans'];
$bt5usans = $_POST['bt5usans'];
$igor = $_POST['igor'];

// Sample Environment
$changer = $_POST['changer'];
$rheometer = $_POST['rheometer'];
$bsc = $_POST['bsc'];
$sc12 = $_POST['12sc'];
$ppsc = $_POST['ppsc'];
$ccr = $_POST['ccr'];
$em = $_POST['em'];
$scm = $_POST['scm'];
$pa = $_POST['pa'];
$humidity = $_POST['humidity'];
$userequip = $_POST['userequip'];
$otherequip = $_POST['otherequip'];

// Authors
//#TODO: Get author information

//Get existing publication by id
$original = publication::with_id($pub_id);

// Compare existing to modified values
$preamble = "UPDATE ".$dbase.".publication SET";
$modifications = "";
$modifications += $title == $original->reference->title ? "" : " title = '".$title."',";
$modifications += $year == $original->reference->year ? "" : " year = '".$year."',";
$modifications += $volume == $original->reference->volume ? "" : " volume = '".$volume."',";
$modifications += $issue == $original->reference->issue ? "" : " issue = '".$issue."',";
$modifications += $firstpage == $original->reference->firstpage ? "" : " firstpage = '".$firstpage."',";
$modifications += $doi == $original->reference->doi ? "" : " doi = '".$doi."',";
$modifications += $pdf == $original->reference->pdf ? "" : " pdf = '".$pdf."',";
$modifications += $journal_id == $original->reference->journal_id ? "" : " journal_id = '".$journal_id."',";
$modifications = rtrim($modifications, ",");

print $modifications;

?>