<?php

include 'publication_database_class.php';

$db = 'publications_rework';
$tables = array(
								'publication' => array('id', 'title', 'journal_id', 'volume', 'issue', 'firstpage', 'year', 'number_of_authors', 'pdf', 'doi'), 
								'author' => array('id', 'first_name', 'middle_name', 'last_name'), 
								'publication_author' => array('author_id', 'publication_id', 'author_number'), 
								'journal' => array('id', 'name', 'abbreviation'), 
								'sampleenvironmentmetrics' => array('publication_id', 'sample_changer', 'rheometer', 'shear_cell_boulder', 'shear_cell_12plane', 'shear_cell_plateplate', 'closed_cycle_refrigerator', 'electromagnet', 'superconducting_magnet', 'polarization', 'humidity_cell', 'user_equipment', 'other'),
								'usagemetrics' => array('publication_id', 'nist_author', 'ng3_30m_sans', 'ng7_30m_sans', 'ng1_8m_sans', 'ngb_10m_sans', 'ngb_30m_sans', 'bt5_usans', 'igor_macros')
								);

class publication {
	//Bibliographic Information
	private $id = 0;
	// Will be a Reference object
	public $reference = NULL;
	// An array of Author objects
	private $authors = NULL;
	protected $number_of_authors = 0;
	public $pdf = FALSE;
	// A SampleEnvironmentMetrics object
	public $se_metrics = NULL;
	// A UsageMetrics object
	public $usage_metrics = NULL;
	// A connection object
	private $connect = NULL;
	private $authpub_tbl = NULL;
	public $citation = NULL;
	public $chrns_citation = NULL;
	public $cvs_citation = NULL;
	public $cvs_printable = NULL;
	
	public function __construct($refer, $authors = NULL, $se = NULL, $usage = NULL, $pdf = FALSE) {
		// Handle reference information
		// Check instance type and go from there
		$this->connect = new connection($GLOBALS['db'], $GLOBALS['tables']);
		$this->authpub_tbl = 'publication_author';
		$this->authpub_cols = $GLOBALS['tables'][$this->authpub_tbl];
		// Handle the bibliographic information
		if ($refer instanceof Reference) {
			$this->reference = $refer;
		} elseif (is_array($refer)) {
			$journal = new Journal(@$refer['journal.id'], @$refer['journal.name'], @$refer['journal.abbreviation']);
			$this->reference = new Reference(@$refer['id'], @$refer['title'], @$refer['volume'], @$refer['issue'], @$refer['firstpage'], $journal, @$refer['year'], @$refer['doi']);
		} else {
			//TODO: Throw useful error.
			print "Unknown reference information type.";
		}
		if ($this->reference->get_id() != 0) {
			$this->id = $this->reference->get_id();
		}
		
		// Handle the authors
		if ($authors == NULL || sizeof($authors) == 0) {
			$authors = $this->get_authors_by_pub_id();
		}
		$i = 1;
		$this->authors = array();
		foreach($authors as $author) {
			
			$this->add_author_at_end($author, $i);
			$i++;
		}
		$this->set_number_of_authors();
		
		// Handle pdf metric
		$this->pdf = (bool) $pdf;
		//Handle usage metrics
		$this->usage_metrics = new UsageMetrics($this->id, $usage);
		//Handle sample environment metrics
		$this->se_metrics = new SampleEnvironmentMetrics($this->id, $se);
		$this->generate_citation();
		$this->generate_chrns_citation();
		$this->generate_cvsfile_citation();
		$this->cvs_citation_to_print();
	}
	
	/*
	/ ID specific functions
	*/
	public function get_id() {
		return $this->id;
	}

	/*
	/ Author specific functions
	*/
	//Need to be careful here - potential for infinite loop
	//This function is only called from set_number_of_authors
	protected function strip_empty_authors() {
		foreach($this->authors as $key => $value) {
			if (!$value instanceof Author) {
				unset($this->authors[$key]);
			}
		}
		$this->set_number_of_authors();
	}
	protected function set_number_of_authors() {
		$size = sizeof($this->authors);
		$i = 0;
		foreach ($this->authors as $value) {
			if ($value instanceof Author) {
				$i++;
			}
		}
		if ($i == $size) {
			$this->numberOfAuthors = $i;
		} else {
			$this->strip_empty_authors();
		}
	}
	public function get_number_of_authors() {
		if ($this->numberOfAuthors != sizeof($this->authors)) {
			$this->set_number_of_authors();
		}
		return $this->numberOfAuthors;
	}
	public function get_author_by_author_number($author_number) {
		$list = array();
		$this->strip_empty_authors();
		foreach($this->authors as $author) {
			if ($author->author_number == $author_number) {
				array_push($list, $author);
			}
		}
		return $list;
	}
	public function add_author_at_end($author, $i = 0) {
		if ($i == 0) {
			$i = $this->get_number_of_authors() + 1;
		}
		if ($author instanceof Author) {
			$author_new = $author;
		} elseif (is_array($author)) {
			$author_new = new Author($authid = 0, $author['firstname'], $author['middlename'], $author['lastname'], $this->id, $i);
		} elseif (is_string($author)) {
			$author_array = explode(" ", $author);
			$length = sizeof($author_array);
			if ($length == 1) {
				$author_new = new Author($author_array[0], "", "", "", $this->id, $i);
			} elseif ($length == 2) {
				$first = $author_array[0];
				$middle = "";
				$last = $author_array[1];
				$id = 0;
				$author_new = new Author($id, $first, $middle, $last, $this->id, $i);
			} elseif ($length >= 3) {
				$first = $author_array[0];
				$middle = "";
				for ($j = 1; $j < $length - 1; $j++) {
					$middle .= $author_array[$j]." ";
				}
				$middle = substr($middle, 0, -1);
				$last = $author_array[$length - 1];
				$id = 0;
				$author_new = new Author($id, $first, $middle, $last, $this->id, $i);
			} else {
				//TODO: Add Error Handling
			}
		} else {
			//TODO: Add Error Handling
		}
		array_push($this->authors, $author_new);
	}
	protected function get_authors_by_pub_id() {
		$constraints = array($this->authpub_cols[1]." = '$this->id'");
		$authors = $this->connect->get_all_results($this->authpub_tbl, $this->authpub_cols, $constraints);
		$auth_list = array();
		$i = 1;
		foreach($authors as $author) {
			$auth_list[$i] = new Author($author[0], "", "", "", $this->id, $author[2]);
			$i++;
		}
		return $auth_list;
	}
	
	/*
	/ Delete the entire publication entry from the database.
	*/
	public function remove_entry() {
		$this->reference->remove_entry();
		$this->se_metrics->remove_entry();
		$this->usage_metrics->remove_entry();
		foreach($this->authors as $author) {
			$author->remove_auth_pub_link();
		}
	}
	
	/*
	/ Print/Display/Output specific functions
	*/
	public function generate_citation() {
		$this->citation = "";
		foreach($this->authors as $author) {
			$this->citation .= $author->get_mla_name();
			$this->citation .= !$author->middlename ? ". ":" ";
		}
		$this->citation .= "\"".$this->reference->title.".\" <i>".$this->reference->journal->name."</i> ".$this->reference->volume.".".$this->reference->issue." (".$this->reference->year."): ".$this->reference->firstpage.". Print.\n";
	}
	public function generate_chrns_citation() {
		$this->chrns_citation = "";
		foreach ($this->authors as $author) {
			$this->chrns_citation .= $author->get_printed_name().", ";
		}
		$this->chrns_citation .= "\"".$this->reference->title."\", ".$this->reference->journal->abbreviation.", ".$this->reference->volume.", ".$this->reference->firstpage." (".$this->reference->year.")\n";
	}
	public function generate_cvsfile_citation() {
		$this->cvs_citation = str_replace(",", ":;:;:", $this->reference->title).",".str_replace(",", ":;:;:", $this->reference->journal->abbreviation).",".$this->reference->volume.",".$this->reference->issue.",".$this->reference->year.",".$this->reference->doi.",";
		$this->cvs_citation .= $this->usage_metrics->ng1sans.",".$this->usage_metrics->ngb10msans.",".$this->usage_metrics->ngb30msans.",".$this->usage_metrics->ng3sans.",".$this->usage_metrics->ng7sans.",".$this->usage_metrics->bt5usans.",".$this->usage_metrics->igor.",";
		foreach ($this->authors as $author) {
			$this->cvs_citation .= $author->get_printed_name().",";
		}
		$this->cvs_citation = rtrim($this->cvs_citation, ",");
	}
	public function cvs_citation_to_print() {
		$array = explode(",", $this->cvs_citation);
		$row = "<tr>";
		foreach($array as $key=>$value) {
			$use_me = str_replace(":;:;:", ",", $value);
			if ($key < 6 OR $key > 12) {
				$row .= "<td>$use_me</td>";
			}
		}
		$row .= "</tr>";
		$this->cvs_printable = $row;
	}
}

/*
/ Class all data types below should extend.
/ Ensures common functionality for publication class
*/
class dataclass {
	// Unique ID - may map to another ID.
	protected $id;
	// DB connection params
	protected $connect;
	protected $tbl;
	protected $cols;
	// Boolean to say if object is a new entry to the database
	public $new;
	
	public function get_id() {
		return $this->id;
	}
	public function get_by_id($id) {
		$constraints = array($this->cols[0]." ='$id'");
		return $this->connect->get_first_result($this->tbl, $this->cols, $constraints);
	}
	protected function add_new($rows, $values) {
		$this->new = TRUE;
		return $this->connect->insert($this->tbl, $rows, $values);
	}
	public function remove_entry() {
		$constraints = array($this->cols[0]."='".$this->id."'");
		$result = $this->connect->delete_entry($this->tbl, $constraints, 0);
	}
	
}

/*
/ Class holding bibliographic information of an article
*/
class Reference extends dataclass {
	// ID maps directly to publication.id
	// No method should override this behavior
	private $publication_id = 0;
	// Bibliographic information
	public $title = '';
	public $volume = 0;
	public $issue = 0;
	public $firstpage = 0;
	public $journal = NULL;
	public $year = 0;
	public $doi = '';
	
	public function __construct($id=0, $title="", $volume=0, $issue=0, $firstpage=0, $journal=0, $year=0, $doi="") {
		// DB connection parameters
		$this->connect = new connection($GLOBALS['db'], $GLOBALS['tables']);
		$this->tbl = 'publication';
		$this->cols = $GLOBALS['tables'][$this->tbl];
		// Reference information
		$this->journal = $journal;
		$this->title = $title;
		$this->volume = $volume;
		$this->issue = $issue;
		$this->firstpage = $firstpage;
		$this->year = $year;
		$this->doi = $doi;
		$this->id = $id;
		$valid = $this->check_inputs();
		foreach ($this as $key => $value) {
			if ($key != "id" && $key != "publication_id" && !$value) {
				$result = $this->get_by_id($id);
				break;
			}
		}
		if ($id == 0) {
			if ($valid['title'] && $valid['volume'] && $valid['issue'] && $valid['firstpage'] && $valid['journal.id'] && $valid['year'] && $valid['doi']) {
				$this->check_journal($this->journal);
				$rows = array($this->cols[1], $this->cols[2], $this->cols[3], $this->cols[4], $this->cols[5], $this->cols[6], $this->cols[9]);
				$values = array($this->title, $this->journal->id, $this->volume, $this->issue, $this->firstpage, $this->year, $this->doi);
				if ($this->add_new($rows, $values)) {
					$result = $this->get_by_title($this->title);
				}
				else {
					//TODO: ERROR HANDLING
					$result = NULL;
				}
			}
		}
		if (isset($result)) {
			$this->id = $result[0];
			$this->title = $result[1];
			$journal = $result[2];
			$this->check_journal($journal);
			$this->volume = $result[3];
			$this->issue = $result[4];
			$this->firstpage = $result[5];
			$this->year = $result[6];
			$this->doi = $result[9];
		}
		
		$this->publication_id = $this->id;
	}
	
	public function get_by_title() {
		$constraints = array($this->cols[1]." ='$this->title'");
		return $this->connect->get_first_result($this->tbl, $this->cols, $constraints);
	}
	private function check_inputs() {
		$valid['id'] = (isset($this->id) && ($this->id != 0));
		$valid['title'] = (strlen($this->title) > 0);
		$valid['journal.id'] = ($this->journal != FALSE);
		$valid['issue'] = ((strtolower($this->volume) == "press") || ($this->issue != FALSE) || (strtolower($this->volume) == "submitted"));
		$valid['volume'] = ((strtolower($this->volume) == "press") || ($this->volume != FALSE) || (strtolower($this->volume) == "submitted"));
		$valid['firstpage'] = ((strtolower($this->volume) == "press") || ($this->firstpage != FALSE) || (strtolower($this->volume) == "submitted"));
		$valid['year'] = ($this->year != FALSE);
		$valid['doi'] = ((preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$this->doi)) || ($this->doi == FALSE));
		return $valid;
	}
	private function check_journal($journal) {
		if ($journal instanceof Journal) {
			$this->journal = $journal;
		} else {
			$this->journal = new Journal($journal);
		}
	}
}

/*
/ Class holding information on a journal
*/
class Journal extends dataclass {
	// Journal information as it is in the DB
	public $name = "";
	public $abbreviation = "";
	
	public function __construct($id=0, $name="", $abbrev="") {
		$this->connect = new connection($GLOBALS['db'], $GLOBALS['tables']);
		$this->tbl = 'journal';
		$this->cols = $GLOBALS['tables'][$this->tbl];
		// Assume the values initially sent are valid, but reassign them later
		$this->id = $id;
		$this->name = $name;
		$this->abbreviation = $abbrev;
		if ($id != 0) {
			$result = $this->get_by_id($id);
		} elseif ($name != "") {
			$result = $this->get_by_name();
		} else {
			//TODO: Add Error Handling
			return;
		}
		if (!$result) {
			if (isset($name,$abbrev)) {
				$rows = array($this->cols[1], $this->cols[2]);
				$values = array($this->name, $this->abbreviation);
				if ($this->add_new($rows, $values)) {
					$result = $this->get_by_name($this->name);
				}
				else {
					//TODO: ERROR HANDLING
					$result = NULL;
				}
			}
		}
		if ($id && $name){
			if ($result[1] != $name) {
				//TODO: Add Error Handling
			}
		}
		if ($result) {
			$this->id = $result[0];
			$this->name = $result[1];
			$this->abbreviation = $result[2];
		}
	}
	
	/*
	/ Database interaction functions
	*/
	public function get_by_name() {
		$constraints = array($this->cols[1]." ='$this->name'");
		return $this->connect->get_first_result($this->tbl, $this->cols, $constraints);
	}
}

/*
/ Class holding information on an individual author
*/
class Author extends dataclass {
	public $firstname = "";
	public $middlename = "";
	public $lastname = "";
	private $printed_name = "";
	private $mla_name = "";
	public $author_number = 0;
	private $publication_id = 0;
	private $tbl1 = "";
	
	public function __construct($id = 0, $first = "", $middle = "", $last = "", $pub_id = 0, $number = "") {
		// DB connection parameters
		$this->connect = new connection($GLOBALS['db'], $GLOBALS['tables']);
		$this->tbl = 'author';
		$this->tbl1 = 'publication_author';
		$this->cols = $GLOBALS['tables'][$this->tbl];
		$this->cols1 = $GLOBALS['tables'][$this->tbl1];
		// Author Information
		$this->firstname = $first;
		$this->middlename = $middle;
		$this->lastname = $last;
		
		$this->publication_id = $pub_id;
		$this->author_number = $number;
		$this->id = $id;
		
		
		if ($id == 0) {
			if ($first == "" && $middle == "" && $last == "" && $pub_id == 0 && $number == "") {
				$result = NULL;
			}elseif ($first == "" && $middle == "" && $last == "") {
				$this->id = $this->get_by_publink()[0][0];
				$result = $this->get_by_id($this->id);
			}
			else {
				$result = $this->get_by_name();
			}
		} else {
			$result = $this->get_by_id($id);
		}
		if ($result) {
			$this->id = (($id != 0 && $id != $result[0])? Null : $result[0]); //TODO: Add Error Handling;
			$this->firstname = (($first != "" && $first != $result[1]) ? Null : $result[1]); //TODO: Add Error Handling;
			$this->middlename = (($middle != "" && $middle != $result[2]) ? Null : $result[2]); //TODO: Add Error Handling;
			$this->lastname = (($last != "" && $last != $result[3]) ? Null : $result[3]); //TODO: Add Error Handling;
		}
		elseif (!$result && $this->firstname != "") {
			$rows = array($this->cols[1], $this->cols[2], $this->cols[3]);
			$values = array($this->firstname, $this->middlename, $this->lastname);
			if ($this->add_new($rows, $values)) {
				$result = $this->get_by_name();
				$this->id = $result[0];
			}
		}
		$authpub_result = $this->get_auth_pub_link();
		if ($number && $this->publication_id && !$authpub_result) {
			$result = $this->add_auth_pub_link();
		} elseif ($authpub_result) {
			$this->author_number = $authpub_result[0][2];
		}
		$this->create_printed_name();
		$this->create_mla_name();
	}
	
	private function get_by_name() {
		$constraints = array($this->cols[1]." ='$this->firstname' && ".$this->cols[2]." ='$this->middlename' &&".$this->cols[3]." ='$this->lastname'");
		return $this->connect->get_first_result($this->tbl, $this->cols, $constraints);
	}
	private function get_auth_pub_link() {
		$constraints = array($this->cols1[0]." = '$this->id'", $this->cols1[1]." = '$this->publication_id'");
		if ($this->author_number) {
			array_push($constraints, $this->cols1[2]." = '$this->author_number'");
		}
		return $this->connect->get_all_results($this->tbl1, $this->cols1, $constraints);
	}
	public function get_by_publink() {
		$constraints = array($this->cols1[1]." = '$this->publication_id'", $this->cols1[2]." = '$this->author_number'");
		return $this->connect->get_all_results($this->tbl1, $this->cols1, $constraints);
	}
	public function add_auth_pub_link() {
		$rows = array('author_id', 'publication_id', 'author_number');
		$values = array($this->id, $this->publication_id, $this->author_number);
		$this->connect->insert($this->tbl1, $rows, $values);
	}
	public function remove_auth_pub_link() {
		$constraints = array($this->cols1[0]." = '$this->id'", $this->cols1[1]." = '$this->publication_id'", $this->cols1[2]." = '$this->author_number'");
		$result = $this->connect->delete_entry($this->tbl1, $constraints, $limit = 1);
	}
	private function create_printed_name() {
		$print_name = "";
		if (preg_match("/[-]/", $this->firstname)) {
			$list = explode("-", $this->firstname);
			foreach($list as $value) {
				$print_name .= substr($value, 0, 1).".-";
			}
			$print_name = substr($print_name, 0, -1);
		}
		else {
			$print_name .= substr($this->firstname, 0, 1).".";
		}
		if ($this->middlename) {
			$list = explode(" ", $this->middlename);
			foreach($list as $value) {
				$print_name .= substr($value, 0, 1).".";
			}
		}
		$print_name .= " ".$this->lastname;
		$this->printed_name = $print_name;
	}
	private function create_mla_name() {
		$this->mla_name = $this->lastname.", ".$this->firstname;
		if ($this->middlename) {
			$middlenames = explode(" ", $this->middlename);
			foreach($middlenames as $name) {
				$this->mla_name .= " ".$name[0].".";
			}
		}
	}
	public function get_printed_name() {
		return $this->printed_name;
	}
	public function get_mla_name() {
		return $this->mla_name;
	}
	public function get_publication_id() {
		return $this->publication_id;
	}
}

/*
/ Class holding information on the usage metrics
*/
class UsageMetrics extends dataclass {
	public $nistauthor = FALSE;
	public $ng3sans = FALSE;
	public $ng7sans = FALSE;
	public $ng1sans = FALSE;
	public $bt5usans = FALSE;
	public $ngb10msans = FALSE;
	public $ngb30msans = FALSE;
	public $igor = FALSE;
	
	public function __construct($pub_id = 0, $metrics_array = NULL) {
		// DB connection parameters
		$this->connect = new connection($GLOBALS['db'], $GLOBALS['tables']);
		$this->tbl = 'usagemetrics';
		$this->cols = $GLOBALS['tables'][$this->tbl];
		// Reference information
		$this->id = $pub_id;
		$result = $this->get_by_id($this->id);
		if (!$result) {
			$values[0] = $pub_id;
			$values[1] = isset($metrics_array['nistauthor']) ? $metrics_array['nistauthor'] : FALSE;
			$values[2] = isset($metrics_array['ng3sans']) ? $metrics_array['ng3sans'] : FALSE;
			$values[3] = isset($metrics_array['ng7sans']) ? $metrics_array['ng7sans'] : FALSE;
			$values[4] = isset($metrics_array['ng1sans']) ? $metrics_array['ng1sans'] : FALSE;
			$values[5] = isset($metrics_array['bt5usans']) ? $metrics_array['bt5usans'] : FALSE;
			$values[6] = isset($metrics_array['ngb30msans']) ? $metrics_array['ngb30msans'] : FALSE;
			$values[7] = isset($metrics_array['ngb10msans']) ? $metrics_array['ngb10msans'] : FALSE;
			$values[8] = isset($metrics_array['igor']) ? $metrics_array['igor'] : FALSE;
			$this->add_new($this->cols, $values);
			$result = $this->get_by_id($this->id);
		}
		if ($result && is_array($metrics_array)) {
			@$this->nistauthor = $metrics_array['nistauthor'] == $result[1] ? $result[1] : FALSE;
			@$this->ng3sans = $metrics_array['ng3sans'] == $result[2] ? $result[2] : FALSE;
			@$this->ng7sans = $metrics_array['ng7sans'] == $result[3] ? $result[3] : FALSE;
			@$this->ng1sans = $metrics_array['ng1sans'] == $result[4] ? $result[4] : FALSE;
			@$this->bt5usans = $metrics_array['bt5usans'] == $result[5] ? $result[5] : FALSE;
			@$this->ngb30msans = $metrics_array['ngb30msans'] == $result[6] ? $result[6] : FALSE;
			@$this->ngb10msans = $metrics_array['ngb10msans'] == $result[7] ? $result[7] : FALSE;
			@$this->igor = $metrics_array['igor'] == $result[8] ? $result[8] : FALSE;
		} elseif ($result) {
			$this->nistauthor = $result[1];
			$this->ng3sans = $result[2];
			$this->ng7sans = $result[3];
			$this->ng1sans = $result[4];
			$this->bt5usans = $result[5];
			$this->ngb30msans = $result[6];
			$this->ngb10msans = $result[7];
			$this->igor = $result[8];
		} else {
			// All values remain false if nothing is sent to the class
		}
	}
}

/*
/ Class holding information on the se metrics
*/
class SampleEnvironmentMetrics extends dataclass  {
	public $sample_changer = FALSE;
	public $rheometer = FALSE;
	public $shear_cell_boulder = FALSE;
	public $shear_cell_12plane = FALSE;
	public $shear_cell_plateplate = FALSE;
	public $closed_cycle_refrigerator = FALSE;
	public $electromagnet = FALSE;
	public $superconducting_magnet = FALSE;
	public $polarization = FALSE;
	public $humidity_cell = FALSE;
	public $user_equipment = FALSE;
	public $other = FALSE;
	
	public function __construct($pub_id = 0, $metrics_array = NULL) {
		// DB connection parameters
		$this->connect = new connection($GLOBALS['db'], $GLOBALS['tables']);
		$this->tbl = 'sampleenvironmentmetrics';
		$this->cols = $GLOBALS['tables'][$this->tbl];
		// Reference information
		$this->id = $pub_id;
		$result = $this->get_by_id($this->id);
		if (!$result) {
			$values[0] = $pub_id;
			$values[1] = isset($metrics_array['sample_changer']) ? $metrics_array['sample_changer'] : FALSE;
			$values[2] = isset($metrics_array['rheometer']) ? $metrics_array['rheometer'] : FALSE;
			$values[3] = isset($metrics_array['sc_boulder']) ? $metrics_array['sc_boulder'] : FALSE;
			$values[4] = isset($metrics_array['sc_12']) ? $metrics_array['sc_12'] : FALSE;
			$values[5] = isset($metrics_array['sc_pp']) ? $metrics_array['sc_pp'] : FALSE;
			$values[6] = isset($metrics_array['ccr']) ? $metrics_array['ccr'] : FALSE;
			$values[7] = isset($metrics_array['em']) ? $metrics_array['em'] : FALSE;
			$values[8] = isset($metrics_array['scm']) ? $metrics_array['scm'] : FALSE;
			$values[9] = isset($metrics_array['pol']) ? $metrics_array['pol'] : FALSE;
			$values[10] = isset($metrics_array['humidity']) ? $metrics_array['humidity'] : FALSE;
			$values[11] = isset($metrics_array['userequip']) ? $metrics_array['userequip'] : FALSE;
			$values[12] = isset($metrics_array['other']) ? $metrics_array['other'] : FALSE;
			$this->add_new($this->cols, $values);
			$result = $this->get_by_id($this->id);
		}
		if ($result && is_array($metrics_array)) {
			@$this->sample_changer = $metrics_array['sample_changer'] == $result[1] ? $result[1] : FALSE;
			@$this->rheometer = $metrics_array['rheometer'] == $result[2] ? $result[2] : FALSE;
			@$this->shear_cell_boulder = $metrics_array['sc_boulder'] == $result[3] ? $result[3] : FALSE;
			@$this->shear_cell_12plane = $metrics_array['sc_12'] == $result[4] ? $result[4] : FALSE;
			@$this->shear_cell_plateplate = $metrics_array['sc_pp'] == $result[5] ? $result[5] : FALSE;
			@$this->closed_cycle_refrigerator = $metrics_array['ccr'] == $result[6] ? $result[6] : FALSE;
			@$this->electromagnet = $metrics_array['em'] == $result[7] ? $result[7] : FALSE;
			@$this->superconducting_magnet = $metrics_array['scm'] == $result[8] ? $result[8] : FALSE;
			@$this->polarization = $metrics_array['pol'] == $result[9] ? $result[9] : FALSE;
			@$this->humidity_cell = $metrics_array['humidity'] == $result[10] ? $result[10] : FALSE;
			@$this->user_equipment = $metrics_array['userequip'] == $result[11] ? $result[11] : FALSE;
			@$this->other = $metrics_array['other'] == $result[12] ? $result[12] : FALSE;
		} elseif ($result) {
			$this->sample_changer = $result[1];
			$this->rheometer = $result[2];
			$this->shear_cell_boulder = $result[3];
			$this->shear_cell_12plane = $result[4];
			$this->shear_cell_plateplate = $result[5];
			$this->closed_cycle_refrigerator = $result[6];
			$this->electromagnet = $result[7];
			$this->superconducting_magnet = $result[8];
			$this->polarization = $result[9];
			$this->humidity_cell = $result[10];
			$this->user_equipment = $result[11];
			$this->other = $result[12];
		} else {
			// All values remain false if nothing is sent to the class
		}
	}
}

?>