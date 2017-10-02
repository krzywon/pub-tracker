<?php

/*
	TODO:
		(1) Make $refer, and $author arrays
*/

$id = $_GET['id'];

include 'publication_class.php';
$pub = publication::with_id($id);

$action = 'php/modify_pub.php';

$pubid = $pub->get_id();
$title = $pub->reference->title;

echo "<form action=\"$action\" method=\"post\" id=\"modifyform\" class=\"bordered\">\n";
echo "<input type=\"text\" value=\"$title\" class=\"hidden\" name=\"pub_id\">";
echo "<h2>Modify the information below to change the bibliographic record for this publication.</h2>";
echo "\t<div class=\"bordered\">\n";
echo "\t\t<h3>Bibliographic Information</h3>\n";
echo "\t\t\t<p class=\"hidden\"><input type=\"text\" name=\"id\" value=\"$pubid\"></p>\n";
echo "\t\t\t<p><label for=\"title\">Article Title:<input type=\"text\" size=\"120\" name=\"title\" id=\"title\" value=\"$title\"></label></p>\n";
echo "\t\t\t<p>\n\t\t\t\t<label for=\"year\">Year Published:<input type=\"text\" size=\"4\" name=\"year\" id=\"year\" value=\"".$pub->reference->year."\"></label>\n
				\t\t\t\t<label for=\"volume\">Volume:<input type=\"text\" size=\"6\" name=\"volume\" id=\"volume\" value=\"".$pub->reference->volume."\"></label>\n
				\t\t\t\t<label for=\"issue\">Issue:<input type=\"text\" size=\"3\" name=\"issue\" id=\"issue\" value=\"".$pub->reference->issue."\"></label>\n
				\t\t\t\t<label for=\"firstpage\">Number of First Page:<input type=\"text\" size=\"6\" name=\"firstpage\" id=\"firstpage\" value=\"".$pub->reference->firstpage."\"></label>\n
				\t\t\t</p>\n";
echo "\t\t\t<p>
			<label for=\"journal\">Journal Name:
				<select name=\"journal\" id=\"journal\">\n";
$connect = new connection('publications_rework', 'journal');
$order = 'name';
$rows = ['*'];
$q = $connect->get_all_results('journal', $rows, NULL, $order);
$jid = $pub->reference->journal->get_id();
foreach ($q as $row) {
	$jId = $row[0];
	$jname = htmlentities($row[1]);
	echo "\t\t\t\t\t<option value=\"$jId\"";
	if ($jId == $jid) {
		echo " SELECTED";
	}
	echo ">$jname</option>\n";
}
echo "\t\t\t\t\t<option>Other...</option>\n\t\t\t\t</select></label>\n</p>\n";

$doi = $pub->reference->doi;
echo "<p><label for=\"doi\">Article Document Object Identifier Permalink:<input type=\"text\" size=\"60\" name=\"doi\" id=\"doi\" value=\"$doi\"></label></p>\n";
echo "</div>\n";

@$pdf = ($pub->reference->pdf == TRUE ? " checked" : "");
@$nistauthor = ($pub->usage_metrics->nistauthor == TRUE ? " checked" : "");
@$ng1 = ($pub->usage_metrics->ng1sans == TRUE ? " checked" : "");
@$ngb10m = ($pub->usage_metrics->ngb10msans == TRUE ? " checked" : "");
@$ngb30m = ($pub->usage_metrics->ngb30msans == TRUE ? " checked" : "");
@$ng3 = ($pub->usage_metrics->ng3sans == TRUE ? " checked" : "");
@$ng7 = ($pub->usage_metrics->ng7sans == TRUE ? " checked" : "");
@$bt5 = ($pub->usage_metrics->bt5usans == TRUE ? " checked" : "");
@$igor = ($pub->usage_metrics->igor == TRUE ? " checked" : "");
@$changer = ($pub->se_metrics->sample_changer == TRUE ? " checked" : "");
@$rheometer = ($pub->se_metrics->rheometer == TRUE ? " checked" : "");
@$bsc = ($pub->se_metrics->shear_cell_boulder == TRUE ? " checked" : "");
@$sc12plane = ($pub->se_metrics->shear_cell_12plane == TRUE ? " checked" : "");
@$scplateplate = ($pub->se_metrics->shear_cell_plateplate == TRUE ? " checked" : "");
@$ccr = ($pub->se_metrics->closed_cycle_refrigerator == TRUE ? " checked" : "");
@$em = ($pub->se_metrics->electromagnet == TRUE ? " checked" : "");
@$scm = ($pub->se_metrics->superconducting_magnet == TRUE ? " checked" : "");
@$pa = ($pub->se_metrics->polarization == TRUE ? " checked" : "");
@$humidity = ($pub->se_metrics->humidity_cell == TRUE ? " checked" : "");
@$userequip = ($pub->se_metrics->user_equipment == TRUE ? " checked" : "");
@$otherequip = ($pub->se_metrics->other == TRUE ? " checked" : "");

echo "<div class=\"bordered\">\n";
echo "<h3>Instrumentation Record Keeping</h3>\n";
echo "<p>Various Demographics:</p>\n";
echo "<p class=\"indented\"><label>Did a NIST affiliate (co)author the paper:<input type=\"checkbox\" name=\"usage[nistauthor]\" id=\"nistauthor\"></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label>Do you have a PDF?:<input type=\"checkbox\" name=\"pdf\" id=\"pdf\" onchange=\"pdfCheck('pdf', 'pdfinput')\"></label></p>\n";
echo "<p>Which Instruments/Software were used?:</p>\n";
echo "<p class=\"indented\" id=\"usage\"><label>NG3 30m SANS:<input type=\"checkbox\" name=\"usage[ng3sans]\" id=\"ng3sans\"></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label>NG7 30m SANS:<input type=\"checkbox\" name=\"usage[ng7sans]\" id=\"ng7sans\"></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label>NGB 10m SANS:<input type=\"checkbox\" name=\"usage[ngb10msans]\" id=\"ngb10msans\"></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label>NGB 30m SANS:<input type=\"checkbox\" name=\"usage[ngb30msans]\" id=\"ngb30msans\"></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label>BT5 USANS:<input type=\"checkbox\" name=\"usage[bt5usans]\" id=\"bt5usans\"></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label>IGOR:<input type=\"checkbox\" name=\"usage[igor]\" id=\"igor\"></label></p>\n";
echo "<p>Which Sample Environment(s) were utilized?</p>\n";
echo "<p class=\"indented\" id=\"se\"><label>Sample Changer (10CB/7HB/9P):<input type=\"checkbox\" name=\"se[sample_changer]\"></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label>Rheometer:<input type=\"checkbox\" name=\"se[rheometer]\"></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label>Shear Cell - Boulder:<input type=\"checkbox\" name=\"se[sc_boulder]\"></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label>Shear Cell - 1,2-Plane:<input type=\"checkbox\" name=\"se[sc_12]\"></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label>Shear Cell - Plate/Plate:<input type=\"checkbox\" name=\"se[sc_pp]\"></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label>Closed-Cycle Refrigerator:<input type=\"checkbox\" name=\"se[ccr]\"></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label>Electromagnet (HM1/Titan/etc):<input type=\"checkbox\" name=\"se[em]\"></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label>Superconducting Magnet:<input type=\"checkbox\" name=\"se[scm]\"></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label>Polarization (He3/PASANS):<input type=\"checkbox\" name=\"se[pol]\"></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label>Humidity Chamber:<input type=\"checkbox\" name=\"se[humidity]\"></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label>User Provided Equipment:<input type=\"checkbox\" name=\"se[userequip]\"></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label>Other:<input type=\"checkbox\" name=\"se[other]\"></label></p>\n";
echo "</div>\n";

$id = $pub->get_id();
echo "<div class=\"bordered\">\n";
echo "<h3>Author Information<input type=\"hidden\" id=\"no_authors\" name=\"authors\" value=\"2\"></h3>\n";
echo "<blockquote>\n<table id=\"authors\">\n<thead><tr class=\"bold\"><td>Author Number</td><td>Author Name</td></tr></thead>\n";
$int = 0;
foreach ($pub->authors as $author) {
	$int++;
	$authnumber = str_pad($int, 2, 0, STR_PAD_LEFT);
	$authid = $author->author->get_id();
	$authOrder = sprintf("%02s", $author->author_number);
	echo "<tr id=\"auth$authnumber\"><td><input type=\"text\" name=\"author".$authnumber."order\" class=\"cheminput\" value=\"$authOrder\"></td><td><input type=\"text\" name=\"author$authnumber\" id=\"author$authnumber\" class=\"cheminput\" size=\"60\" onkeyup=\"showResult(this, this.value, 'author$authnumber')\" onfocus=\"addClass('auth".$authnumber."list', 'displaybox')\" onblur=\"addClass('auth".$authnumber."list', 'displaybox invisible')\" value=\"".$author->author->get_full_name()."\"><br><div id=\"auth".$authnumber."list\" class=\"displaybox invisible\"></div></td><td id=\"addauthor$authnumber\"><input type=\"button\" class=\"add-plus\" name=\"addauthor$authnumber\" onclick=\"addRowInline('authors', 'auth$authnumber')\" title=\"Add Additional Author After This Author\"></td><td id=\"delete$authnumber\"><input type=\"button\" name=\"deleteauth$authnumber\"class=\"delete-x\" onclick=\"removeRowInline('authors', $authnumber)\" title=\"Remove Author From Publication\"></td></tr>\n";
}
echo "</table>\n</blockquote>\n</div>\n";

//TODO: Give Submit Changes button functionality

echo "<p><input type=\"submit\" value=\"Submit Changes\"><input type=\"button\" value=\"Delete Publication\" onclick=\"deleteentry('mainbody','$title')\"><input type=\"button\" value=\"Delete Duplicates\" onclick=\"deletematches('mainbody','$title', '$pubid')\"></p>\n</form>\n";

?>