<?php

// TODO: replace this all by constructing a publication instance based on the title
//			and id and then use native methods to delete all matching records

$pubtitle = $_GET['pubtitle'];
$base_id = $_GET['pubid'];

include 'publication_class.php';
$pub_keeper = publication::with_id($base_id);
$matching_pubs = publication::with_title_all($pubtitle);
$success_array = array();
$failures = "";
$success = NULL;

foreach ($matching_pubs as $pub) {
	$pub_id = $pub->get_id();
	if ($pub_id != $base_id) {
		$pub->remove_entry();
		$get_pub = publication::with_id($pub_id);
		if ($get_pub == NULL) {
			$success_array[$pub_id] = TRUE;
		} else {
			$success_array[$pub_id] = FALSE;
			$failures .= "Publication with ID ".$pub_id." was not removed properly.";
		}
	}
}

if (in_array(FALSE, $success_array) && sizeof($success_array) > 0) {
	$success = TRUE;
} elseif (sizeof($success_array) != 0) {
	$success = FALSE;
}

if ($success) {
	//header('Location: ../modification.html');
}
elseif ($success === NULL) {
	echo '<!DOCTYPE html><html><head><title>None</title><script type="text/javascript">function startup() { alert("No duplicates were found.")}</script></head><body onload=startup()></body></html>';
	//header('Location: ../modification.html');
}
else {
	echo("<p>The deletion process was unsuccessful.</p>");
	echo($failures);
}

?>