<!--
	TODO:
		(1) Make page more appealing
-->

<!DOCTYPE html>
<html>
<head>
	<title>NIST - Center for Neutron Research - Small-Angle Neutron Scattering Group</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link rel="stylesheet" href="http://www.ncnr.nist.gov/programs/sans/scripts/style.css" type="text/css">
	<link rel="stylesheet" href="../stylesheets/style.css" type="text/css">
	<style type="text/css">
		body {
		margin: 10px;
		}
		th {
		background: blue;
		color: white;
		text-align: center;
		}
		tr td {
		background: white;
		color: black;
		text-align: left;
		border-bottom: 1px solid gray;
		}
		tr td a {
		color: blue;
		text-decoration: underline;
		}
		tr td a:hover {
		color: red;
		text-decoration: underline;
		}
	</style>
</head>

<body onload="timer=setTimeout(function(){ window.location='../injection.html';}, 30000)">

<?php

include 'publication_class.php';
	
include 'pub_handle_posted_data.php';

// Create all-new article
$article = publication::with_values($refer, $authors, $se, $usage);


print "<h2>Your article has been successfully entered.</h2>\n";
print "<div class=\"bordered\">";
print "<p>publication id = ".$article->get_id()."</p>\n";
print "<p>year = ".$article->reference->year."<br> volume = ".$article->reference->volume."<br> issue = ".$article->reference->issue."<br> first page = ".$article->reference->firstpage."<br> journal title = ".$article->reference->journal->name."<br> article title = ".$article->reference->title."<br> nistauthor = ".$article->usage_metrics->nistauthor."<br> pdf = ".$article->reference->pdf."<br> instruments (ng1, ng3, ng7, ngb10m, ngb30m, bt5, igor) = ".$article->usage_metrics->ng1sans.", ".$article->usage_metrics->ng3sans.", ".$article->usage_metrics->ng7sans.", ".$article->usage_metrics->ngb10msans.", ".$article->usage_metrics->ngb30msans.", ".$article->usage_metrics->bt5usans.", ".$article->usage_metrics->igor."</p>\n";

?>

<p><a href="../injection.html">Submit Another Article</a> - You will be automatically redirected in 30 seconds.</p>
<p><a href="../modification.html">Modify Your Entry</a> - Is something incorrect? Fix it now.</p>

</form>

</body>
</html>