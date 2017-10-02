<?php

// Get bibliographic information
$refer['title'] = $_POST['title'];
$refer['year'] = $_POST['year'];
$refer['volume'] = $_POST['volume'];
$refer['issue'] = $_POST['issue'];
$refer['firstpage'] = $_POST['firstpage'];
$refer['journal'] = $_POST['journal'];
$refer['doi'] = $_POST['doi'];

// Get journal posted data and create new Journal instance
$refer['jtitle'] = '';
$refer['jabbrev'] = '';
if ($refer['journal'] == "Other...")
{
	$refer['jtitle'] = $_POST['jtitle'];
	$refer['jabbrev'] = $_POST['jabbrev'];
}

// Get posted author information and use as-is
$authors = $_POST['author'];

// Get usage metrics
@$refer['pdf'] = $_POST['pdf'];
$se = array();
foreach($_POST['se'] as $key => $value) {
	$se[$key] = TRUE;
}
$usage = array();
foreach($_POST['usage'] as $key => $value) {
	$usage[$key] = TRUE;
}

?>