<?php

include "db_credentials.php";
include "pubGeneralFunctions.php";

$tbl = "publications_rework";

$query = "SELECT id, nistauthor, igor, ng1sans, ng3sans, ng7sans, ngb10msans, ngb30msans, bt5usans FROM publication";

$results = mysqliconnect($query, $host, $user, $pass, $tbl);

foreach ($results as $result)
{
	$query_insert = "INSERT INTO usagemetrics (publication_id, nist_author, ng3_30m_sans, ng7_30m_sans, ng1_8m_sans, ngb_10m_sans, ngb_30m_sans, bt5_usans, igor_macros) VALUES (";
	$query_insert .= $result['id'].", ";
	$query_insert .= $result['nistauthor'].", ";
	$query_insert .= $result['ng3sans'].", ";
	$query_insert .= $result['ng7sans'].", ";
	$query_insert .= $result['ng1sans'].", ";
	$query_insert .= $result['ngb10msans'].", ";
	$query_insert .= $result['ngb30msans'].", ";
	$query_insert .= $result['bt5usans'].", ";
	$query_insert .= $result['igor'].")";
	
	$insert_result = mysqliconnect($query_insert, $host, $user, $pass, $tbl);
	
	if ($insert_result)
	{
		print "Insert of publication.id ".$result['id']." was successful.";
	}
	else
	{
		print "Insert failed.";
	}
}

?>