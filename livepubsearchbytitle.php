<?php

$title = $_GET['title'];
require 'db_credentials.php';
$dbase = 'publications';
$action = 'modify_pub.php';

$con = mysqli_connect($host, $user, $pass, $dbase);
if (!$con) {
  die('Could not connect: ' . mysqli_error($con));
}

$sql = "SELECT * from sanspublications WHERE pub_id = '$title'";
$result = mysqli_fetch_row(mysqli_query($con, $sql));

$pubid = $result[0];
$title = $result[9];

echo "<form action=\"$action\" onsubmit=\"\" id=\"modifyform\" class=\"bordered\">\n";
echo "<input type=\"text\" value=\"$title\" class=\"hidden\" name=\"pub_id\">";
echo "<h2>Modify the information below to change the bibliographic record for this publication.</h2>";
echo "\t<div class=\"bordered\">\n";
echo "\t\t<h3>Bibliographic Information</h3>\n";
echo "\t\t\t<p class=\"hidden\"><input type=\"text\" name=\"id\" value=\"$pubid\"></p>\n";
echo "\t\t\t<p><label for=\"title\">Article Title:<input type=\"text\" size=\"120\" name=\"title\" id=\"title\" value=\"$title\"></label></p>\n";
echo "\t\t\t<p>\n\t\t\t\t<label for=\"year\">Year Published:<input type=\"text\" size=\"4\" name=\"year\" id=\"year\" value=\"".$result[1]."\"></label>\n
				\t\t\t\t<label for=\"volume\">Volume:<input type=\"text\" size=\"6\" name=\"volume\" id=\"volume\" value=\"".$result[2]."\"></label>\n
				\t\t\t\t<label for=\"issue\">Issue:<input type=\"text\" size=\"3\" name=\"issue\" id=\"issue\" value=\"".$result[3]."\"></label>\n
				\t\t\t\t<label for=\"firstpage\">Number of First Page:<input type=\"text\" size=\"6\" name=\"firstpage\" id=\"firstpage\" value=\"".$result[4]."\"></label>\n
				\t\t\t</p>\n";
echo "\t\t\t<p>
			<label for=\"journal\">Journal Name:
				<select name=\"journal\" id=\"journal\">\n";
$jid = $result[5];
$q = mysqli_query($con,"SELECT * FROM journalinformation ORDER BY journalName");
while ($row = mysqli_fetch_row($q)) {
	$jId = $row[0];
	$jname = htmlentities($row[1]);
	echo "\t\t\t\t\t<option value=\"$jId\"";
	if ($jId == $jid) {
		echo " SELECTED";
	}
	echo ">$jname</option>\n";
}
echo "\t\t\t\t\t<option>Other...</option>\n\t\t\t\t</select></label>\n</p>\n";

$metrics_results = mysqli_query($con,"SELECT * FROM sampleenvironment WHERE sampleenvironment.pub_id = '$pubid'");
@$metrics = mysqli_fetch_row($metrics_results);
@$new_metric = mysqli_data_seek($metrics_results, 1);

$doi = $result[17];
echo "<p><label for=\"doi\">Article Document Object Identifier Permalink:<input type=\"text\" size=\"60\" name=\"doi\" id=\"doi\" value=\"$doi\"></label></p>\n";
echo "</div>\n";

$pdf = ($result[8] == TRUE ? " checked" : "");
$nistauthor = ($result[7] == TRUE ? " checked" : "");
$ng1 = ($result[10] == TRUE ? " checked" : "");
$ngb10m = ($result[11] == TRUE ? " checked" : "");
$ngb30m = ($result[12] == TRUE ? " checked" : "");
$ng3 = ($result[13] == TRUE ? " checked" : "");
$ng7 = ($result[14] == TRUE ? " checked" : "");
$bt5 = ($result[15] == TRUE ? " checked" : "");
$igor = ($result[16] == TRUE ? " checked" : "");
$changer = ($metrics[1] == TRUE ? " checked" : "");
$rheometer = ($metrics[2] == TRUE ? " checked" : "");
$bsc = ($metrics[3] == TRUE ? " checked" : "");
$sc12plane = ($metrics[4] == TRUE ? " checked" : "");
$scplateplate = ($metrics[5] == TRUE ? " checked" : "");
$ccr = ($metrics[6] == TRUE ? " checked" : "");
$em = ($metrics[7] == TRUE ? " checked" : "");
$scm = ($metrics[8] == TRUE ? " checked" : "");
$pa = ($metrics[9] == TRUE ? " checked" : "");
$humidity = ($metrics[10] == TRUE ? " checked" : "");
$userequip = ($metrics[11] == TRUE ? " checked" : "");
$otherequip = ($metrics[12] == TRUE ? " checked" : "");

echo "<div class=\"bordered\">\n";
echo "<h3>Instrumentation Record Keeping</h3>\n";
echo "<p>Various Demographics:</p>\n";
echo "<p><blockquote><label for=\"nistauthor\">Is there a NIST author?:<input type=\"checkbox\" name=\"nistauthor\" id=\"nistauthor\"$nistauthor></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label for=\"pdf\">Do you have a PDF?:<input type=\"checkbox\" name=\"pdf\" id=\"pdf\"$pdf></label></blockquote></p>\n";
echo "<p>Which Instruments/Software were used?:</p>\n";
echo "<p><blockquote><label for=\"ng3sans\">NG3 30m SANS:<input type=\"checkbox\" name=\"ng3sans\" id=\"ng3sans\"$ng3></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label for=\"ng7sans\">NG7 30m SANS:<input type=\"checkbox\" name=\"ng7sans\" id=\"ng7sans\"$ng7></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label for=\"ngb10msans\">NGB 10m SANS:<input type=\"checkbox\" name=\"ngb10msans\" id=\"ngb10msans\"$ngb10m></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label for=\"ngb30msans\">NGB 30m SANS:<input type=\"checkbox\" name=\"ngb30msans\" id=\"ngb30msans\"$ngb30m></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label for=\"bt5usans\">BT5 USANS:<input type=\"checkbox\" name=\"bt5usans\" id=\"bt5usans\"$bt5></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label for=\"igor\">IGOR:<input type=\"checkbox\" name=\"igor\" id=\"igor\"$igor></label></blockquote></p>\n";
echo "<p>Which Sample Environment(s) were utilized?</p>\n";
echo "<p><blockquote><label for=\"changer\">Sample Changer (10CB/7HB/9P):<input type=\"checkbox\" name=\"changer\"$changer></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label for=\"rheometer\">Rheometer:<input type=\"checkbox\" name=\"rheometer\"$rheometer></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label for=\"bsc\">Shear Cell - Boulder:<input type=\"checkbox\" name=\"bsc\"$bsc></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label for=\"12sc\">Shear Cell - 1,2-Plane:<input type=\"checkbox\" name=\"12sc\"$sc12plane></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label for=\"ppsc\">Shear Cell - Plate/Plate:<input type=\"checkbox\" name=\"ppsc\"$scplateplate></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label for=\"ccr\">Closed-Cycle Refrigerator:<input type=\"checkbox\" name=\"ccr\"$ccr></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label for=\"em\">Electromagnet (HM1/Titan/etc):<input type=\"checkbox\" name=\"em\"$em></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label for=\"scm\">Superconducting Magnet:<input type=\"checkbox\" name=\"scm\"$scm></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label for=\"pa\">Polarization (He3/PASANS):<input type=\"checkbox\" name=\"pa\"$pa></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label for=\"humidity\">Humidity Chamber:<input type=\"checkbox\" name=\"humidity\"$humidity></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label for=\"userequip\">User Provided Equipment:<input type=\"checkbox\" name=\"userequip\"$userequip></label>&nbsp;&nbsp;|&nbsp;&nbsp;<label for=\"otherequip\">Other:<input type=\"checkbox\" name=\"otherequip\"$otherequip></label></blockquote></p>\n";
echo "</div>\n";

$id = $result[0];
$sql_authids = "SELECT authorIndex, authorNumber from authorpublink WHERE publicationIndex = '$id' ORDER BY authorNumber";
$authids = mysqli_query($con, $sql_authids);
echo "<div class=\"bordered\">\n";
echo "<h3>Author Information<input type=\"hidden\" id=\"no_authors\" name=\"authors\" value=\"2\"></h3>\n";
echo "<blockquote>\n<table id=\"authors\">\n<thead><tr class=\"bold\"><td>Author Number</td><td>Author Name</td></tr></thead>\n";
$int = 0;
while ($value = mysqli_fetch_row($authids)) {
	$int++;
	$authnumber = str_pad($int, 2, 0, STR_PAD_LEFT);
	$authid = $value[0];
	$authOrder = sprintf("%02s", $value[1]);
	$sql_author = "SELECT * from authors WHERE author_id = '$authid'";
	$author = mysqli_fetch_row(mysqli_query($con, $sql_author));
	$name = $author[0]." ";
	if ($author[1]) {
		$name .= $author[1]." ";
	}
	$name .= $author[2];
	
	echo "<tr id=\"auth$authnumber\"><td><input type=\"text\" name=\"author".$authnumber."order\" class=\"cheminput\" value=\"$authOrder\"></td><td><input type=\"text\" name=\"author$authnumber\" id=\"author$authnumber\" class=\"cheminput\" size=\"60\" onkeyup=\"showResult(this, this.value, 'author$authnumber')\" onfocus=\"addClass('auth".$authnumber."list', 'displaybox')\" onblur=\"addClass('auth".$authnumber."list', 'displaybox invisible')\" value=\"$name\"><br><div id=\"auth".$authnumber."list\" class=\"displaybox invisible\"></div></td><td id=\"addauthor$authnumber\"><input type=\"button\" class=\"add-plus\" name=\"addauthor$authnumber\" onclick=\"addRowInline('authors', 'auth$authnumber')\" title=\"Add Additional Author After This Author\"></td><td id=\"delete$authnumber\"><input type=\"button\" name=\"deleteauth$authnumber\"class=\"delete-x\" onclick=\"removeRowInline('authors', $authnumber)\" title=\"Remove Author From Publication\"></td></tr>\n";
}
echo "</table>\n</blockquote>\n</div>\n";

//TODO: Give Submit Changes button functionality

echo "<p><input type=\"submit\" value=\"Submit Changes\"><input type=\"button\" value=\"Delete Publication\" onclick=\"deleteentry('mainbody','$title')\"><input type=\"button\" value=\"Delete Duplicates\" onclick=\"deletematches('mainbody','$title', '$pubid')\"></p>\n</form>\n";

mysqli_close($con);
?>