<?php

include "db_credentials.php";

function addleadingzero($number, $string) {
	$numString = strval($number);
	if ($number < 10) {
		$final = $string."0".$numString;
	} else {
		$final = $string.$numString;
	}
	return $final;
}

/*
/ Class for connecting to MySQL database
*/
class connection {
	private $host;
	private $user;
	private $pass;
	private $db = "";
	private $tbls = "";
	
	public function __construct($db, $tbls) {
		$this->host = $GLOBALS['host'];
		$this->user = $GLOBALS['user'];
		$this->pass = $GLOBALS['pass'];
		$this->db = $db;
		$this->tbls = $tbls;
	}
	
	public function is_tbl_in_tbls($tbl) {
		return in_array($tbl, $this->tbls);
	}
	public function is_row_in_tbl($row, $tbl) {
		if ($this->is_tbl_in_tbls($tbl)) {
			return in_array($row, $this->tbls[$tbl]);
		} else {
			return FALSE;
		}
	}
	
	/*
	/ Keep this private and callable only within this class.
	/ This will allow all queries to be checked for maliciousness.
	*/
	private function mysqliconnect($query) {
		//print "<li>$query: ";
		if ($this->is_query_acceptable($query)) {
			$mysqli = new mysqli($this->host, $this->user, $this->pass, $this->db);
			if ($mysqli->connect_error) {
				die('Connection Error: ('.$mysqli->connect_errno.')'.$mysqli->connect_error);
			}
			$mysqli_result = $mysqli->query($query);
			$mysqli->close();
			return $mysqli_result;
		}
		return NULL;
	}
	
	/*
	/ Checks that a query meets viable specifications
	*/
	private function is_query_acceptable($query) {
		//TODO: Make a list of acceptable queries and check that all incoming queries meet the reqs.
		return TRUE;
	}
	
	/*
	/ Exposing the query generation tools
	*/
	public function get_first_result($tbl, $rowlist, $constraints) {
		$all_results = $this->get_all_results($tbl, $rowlist, $constraints);
		if ($all_results) {
			$row = $all_results[0];
			/*
			if ($row) {
				print implode(', ', $row)."</li>\n";
			} else {
				print "No Results</li>\n";
			}
			*/
			return $row;
		} else {
			return FALSE;
		}
	}
	public function get_all_results($tbl, $rowlist, $constraints) {
		$query = $this->create_select_query($tbl, $rowlist, $constraints);
		$result = $this->mysqliconnect($query);
		if ($result) {
			$result = $result->fetch_all();
		}
		return $result;
	}
	public function insert($tbl, $rowlist, $values) {
		$query = $this->create_insert_query($tbl, $rowlist, $values);
		return $this->mysqliconnect($query);
	}
	public function delete_entry($tbl, $constraints, $limit=0) {
		$query = $this->create_delete_query($tbl, $constraints, $limit);
		return $this->mysqliconnect($query);
	}
	
	/*
	/ Query generation tools
	*/
	private function create_select_query($tbl, $rowlist, $constraints) {
		$query = "SELECT ";
		foreach($rowlist as $row) {
			$query .= "$row,";
		}
		$query = substr($query, 0, -1);
		$query .= " FROM ".$tbl;
		if ($constraints) {
			$query .= " WHERE ";
			foreach ($constraints as $constraint) {
				$query .= $constraint. " AND ";
			}
			$query = substr($query, 0, -4);
		}
		return $query;
	}
	private function create_insert_query($tbl, $rowlist, $values) {
		$query = "INSERT INTO ".$tbl." (";
		foreach($rowlist as $row) {
			$query .= "$row,";
		}
		$query = substr($query, 0, -1);
		$query .= ") VALUES (";
		foreach ($values as $value) {
			$query .= "'".$value."',";
		}
		$query = substr($query, 0, -1);
		$query .= ")";
		return $query;
	}
	private function create_delete_query($tbl, $constraints, $limit=0) {
		if ($constraints) {
			$query = "DELETE FROM ".$tbl." WHERE ";
			foreach ($constraints as $constraint) {
				$query .= $constraint." AND ";
			}
			$query = substr($query, 0, -5);
		}
		else {
			return FALSE;
		}
		if ($limit) {
			$query .= " LIMIT $limit";
		}
		return $query;
	}
	
	/*
	/ Resets the DB indices to specific values - Useful for testing purposes and will not overwrite existing indices
	*/
	public function reset_indices($pub, $author, $journal) {
		$query = "ALTER TABLE publication AUTO_INCREMENT $pub";
		$this->mysqliconnect($query);
		$query = "ALTER TABLE author AUTO_INCREMENT $author";
		$this->mysqliconnect($query);
		$query = "ALTER TABLE journal AUTO_INCREMENT $journal";
		$this->mysqliconnect($query);
	}
}

?>