<?php

include ('pubGeneralFunctions.php');

function pubList($year, $instr, $filename, $press = false) {
	$instr = strtolower($instr);
	switch($instr) {
		case "all":
			$intno = 1;
			break;
		case "ng1":
			$intno = 7;
			break;
		case "ng3":
			$intno = 8;
			break;
		case "ng7":
			$intno = 9;
			break;
		case "bt5":
			$intno = 10;
			break;
		case "igor":
			$intno = 11;
			break;
		default:
			print "<p>The instrument must either be ng1, ng3, ng7, bt5 or igor.  Please try again with a valid entry.</p>";
			return;
	}
	$list = array();
	$item = array();
	$open = file_get_contents($filename, "r");
	if(!$open) {
		print "<p>Unable to open file $filename.</p>\r\n";
		return;
	}
	else {
		$list = explode("\r\n", $open);
		foreach($list as $value) {
			if($press) {
				$item = explode(", ", $value);
				if($item[2] != 'Press' OR ($item[$intno] != 1 && $intno != 1)) {
					$open = str_replace($value, '', $open);
				}
				$year = '';
			}
			else {
				$item = explode(", ", $value);
				if(($item[$intno] != 1 && $intno != 1) OR $item[5] != $year OR $item[2] == 'Press' OR $item[2] == 'Submitted') {
					$open = str_replace($value, '', $open);
				}
			}
		}
		$list2 = explode("\r\n", $open);
		$test = implode('', $list2);
		$listing = '';
		if(empty($test)) {
			print "<ul>\r\n<li>None</li>\r\n</ul>\r\n";
		}
		else {	
			// Modifies each line into some HTML output
			$i = 1;
			sort($list2);
			print "<table class=\"pubs\"><tr><th>Authors</th><th>Article Title</th><th>Journal Title</th><th>Volume</th><th>Issue</th><th>Page Number</th><th>Year</th></tr>";
			foreach($list2 as $value) {
				if(!$value) {}
				else {
					$item = explode(', ', $value);
					$title = str_replace(":;:;:", "", $item[0]);
					$journal = str_replace(":;:;:", "", $item[1]);
					$authors = '';
					$size = sizeof($item);
					for($j = 12; $j < $size; $j++) {
						$authori = explode(":;:;: ", $item[$j]);
						if(!$authori[2]) {
							$authors .= $authori[0].", ".$authori[1]."., ";
						}
						else {
							$authors .= $authori[0].", ".$authori[1].".".$authori[2].", ";
						}
					}
					$authors = substr($authors, 0, -2);
					$volume = str_replace(":;:;:", "", $item[2]);
					if(!$item[3]) {
						$issue = '';
					}
					else {
						$issue = " (".str_replace(":;:;:", "", $item[3]).")";
					}
					$page = str_replace(":;:;:", "", $item[4]);
					$listing .= "<tr><td>$authors</td><td>$title</td><td>$journal</td><td>$volume</td><td>$issue</td><td>$page</td><td>$year</td>\r\n";
					$i++;
				}
			}
			$listingIntermediate = explode("\r\n", $listing);
			sort($listingIntermediate);
			$listing = implode("\r\n", $listingIntermediate);
			print $listing;
			print "</table>";
		}
	}
}

?>
