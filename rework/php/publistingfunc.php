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
		case "ngb10m":
			$intno = 8;
			break;
		case "ngb30m":
			$intno = 9;
			break;
		case "ng3":
			$intno = 10;
			break;
		case "ng7":
			$intno = 11;
			break;
		case "bt5":
			$intno = 12;
			break;
		case "igor":
			$intno = 13;
			break;
		default:
			print "<p>The instrument must either be ng1, ngb10m, ngb30m, ng3, ng7, bt5 or igor.  Please try again with a valid entry.</p>";
			return;
	}
	$list = array();
	$item = array();
	@$open = file_get_contents($filename, "r");
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
		if(empty($test)) {
			print "<ul>\r\n<li>None</li>\r\n</ul>\r\n";
		}
		else {	
			// Modifies each line into some HTML output
			$i = 1;
			sort($list2);
			print "<table class=\"pubs\"><tr><th>#</th><th>Article Title</th><th>Authors</th><th>Journal Title</th><th>Volume</th><th>Issue</th><th>Page Number</th><th>Year</th></tr>";
			foreach($list2 as $value) {
				if(!$value) {}
				else {
					$item = explode(', ', $value);
					foreach($item as $key => $value1) {
						$item[$key] = str_replace(":;:;:", ",", $value1);
					}
					if ($item[6] == TRUE) {
					 	$title = "<a href=\"".$item[6]."\">".$item[0]."</a>";
					}
					else {
						$title = $item[0];
					}
					$journal = $item[1];
					$authors = '';
					$size = sizeof($item);
					for($j = 14; $j < $size; $j++) {
						$authori = explode(", ", $item[$j]);
						if(!@$authori[2]) {
							$authors .= $authori[1].". ".$authori[0].", ";
						}
						else {
							$authors .= $authori[1].".".$authori[2].". ".$authori[0].", ";
						}
					}
					$authors = substr($authors, 0, -2);
					$volume = $item[2];
					$issue = $item[3];
					$page = $item[4];
					print "<tr><td>$i</td><td>$title</td><td>$authors</td><td>$journal</td><td>$volume</td><td>$issue</td><td>$page</td><td>$year</td>\r\n";
					$i++;
				}
			}
			print "</table>";
		}
	}
}

?>
