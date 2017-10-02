<h3 style="color:#800000">Previous Publications</h3>
<blockquote>
<p>

<?php

$basename = "publications";
$firstyear = 2001;
$currentyear = date("Y");
$printme = "";

$currentarray = explode("/", $_SERVER['REQUEST_URI']);
$number = sizeof($currentarray) - 1;
$current = $currentarray[$number];

if ($current == $basename.".html") {
	print "Recent Pubs&nbsp;&nbsp;|&nbsp;&nbsp;";
} else {
	print "<a href=\"$basename.html\">Recent Pubs</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
}

for ($i = $currentyear - 1; $i >= $firstyear; $i--) {
	$href = $basename."_".$i.".html";
	if ($href == $current) {
		$printme .= "$i&nbsp;&nbsp;|&nbsp;&nbsp;";
	} else {
		$printme .= "<a href=\"$href\">$i</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
	}
}
$printme = substr($printme, 0, -25);
print $printme;

?>

</p>
</blockquote>