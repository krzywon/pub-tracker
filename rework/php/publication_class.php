<?php

// TODO:
//	(1) __toString method for publications should include all other __toString() methods
//	(2) Add native methods for finding lists/groups of publications/authors/etc. by year/instrument/se/etc.

include 'database_class.php';

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
	public $authors = NULL;
	protected $number_of_authors = 0;
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
	
	protected function __construct() {
		// Handle reference information
		// Check instance type and go from there
		$this->connect = new connection();
		$this->authpub_tbl = 'publication_author';
		$this->authpub_cols = $GLOBALS['tables'][$this->authpub_tbl];
	}
	
	/*
	/ Various constructor types with different inputs
	*/
	public static function with_id($id) {
		$instance = new self();
		
		$instance->id = $id;
		$instance->reference = Reference::with_id($id);
		if (isset($instance->reference->title)) {
			$instance->se_metrics = SampleEnvironmentMetrics::with_id($id);
			$instance->usage_metrics = UsageMetrics::with_id($id);
			$instance->authors = $instance->get_authors_by_pub_id();
			$instance->always_run();
		} else {
			$instance = NULL;
		}
		return $instance;
	}
	public static function with_title($title) {
		$instance = NULL;
		$ref = Reference::get_by_title($title)[0];
		$id = $ref[0];
		if ($id) {
			$instance = self::with_id($id);
		}
		return $instance;
	}
	public static function with_title_all($title) {
		$instance_array = array();
		$refs = Reference::get_by_title($title);
		foreach($refs as $ref) {
			$id = $ref[0];
			$ref_instance = self::with_id($id);
			$instance_array[$id] = $ref_instance;
		}
		return $instance_array;
	}
	public static function with_values($refer, $authornames, $se, $usage) {
		$instance = new self();
		
		$instance->reference = Reference::with_values($refer);
		$instance->id = $instance->reference->get_id();
		
		$instance->authors = array();
		for($i = 0; $i < sizeof($authornames); $i++) {
			$j = $i + 1;
			$author_instance = Authorship::with_values($instance->id, (string)$j, $authornames[$i]);
			array_push($instance->authors, $author_instance);
		}
		$se['id'] = $instance->id;
		$usage['publications_id'] = $instance->id;
		$instance->se_metrics = SampleEnvironmentMetrics::with_values($se);
		$instance->usage_metrics = UsageMetrics::with_values($se);
		$instance->always_run();
		return $instance;
	}
	private function always_run() {
		$this->set_number_of_authors();
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
			if (!$value instanceof Authorship) {
				unset($this->authors[$key]);
			}
		}
		$this->set_number_of_authors();
	}
	protected function set_number_of_authors() {
		$size = sizeof($this->authors);
		$i = 0;
		foreach ($this->authors as $value) {
			if ($value instanceof Authorship) {
				$i++;
			}
		}
		if ($i == $size) {
			$this->numberOfAuthors = $i;
			$this->connect->modify('publication', array("number_of_authors" => $this->numberOfAuthors), array("id" => $this->get_id()));
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
			$author_name = Author::with_name($author['firstname'], $author['middlename'], $author['lastname']);
			$author_new = Authorship::with_author($this->get_id(), $i, $author_name);
		} elseif (is_string($author)) {
			$author_new = Authorship::with_values($this->get_id(), $i, $author);
		} else {
			//TODO: Add Error Handling
		}
		array_push($this->authors, $author_new);
		$this->always_run();
	}
	protected function get_authors_by_pub_id() {
		$constraints = array($this->authpub_cols[1]." = '$this->id'");
		$authors = $this->connect->get_all_results($this->authpub_tbl, $this->authpub_cols, $constraints);
		$auth_list = array();
		$i = 1;
		foreach($authors as $author) {
			$auth_list[$i] = Authorship::with_id($this->id, $author[2]);
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
		$this->usage_metrics->remove_entry();
		foreach($this->authors as $author) {
			$author->remove_entry();
		}
	}
	
	/*
	/ Print/Display/Output specific functions
	*/
	public function generate_citation() {
		$this->citation = "";
		foreach($this->authors as $author) {
			$this->citation .= $author->author->get_mla_name();
			$this->citation .= !$author->author->middlename ? ". ":" ";
		}
		$this->citation .= "\"".$this->reference->title.".\" <i>".$this->reference->journal->name."</i> ".$this->reference->volume.".".$this->reference->issue." (".$this->reference->year."): ".$this->reference->firstpage.". Print.\n";
	}
	public function generate_chrns_citation() {
		$this->chrns_citation = "";
		foreach ($this->authors as $author) {
			$this->chrns_citation .= $author->author->get_printed_name().", ";
		}
		$this->chrns_citation .= "\"".$this->reference->title."\", ".$this->reference->journal->abbreviation.", ".$this->reference->volume.", ".$this->reference->firstpage." (".$this->reference->year.")\n";
	}
	public function generate_cvsfile_citation() {
		$this->cvs_citation = str_replace(",", ":;:;:", $this->reference->title).",".str_replace(",", ":;:;:", $this->reference->journal->abbreviation).",";
		if ($this->reference->volume == "Press" || $this->reference->volume == "Submitted") {
			$this->cvs_citation .= $this->reference->volume.",".",".",".$this->reference->doi.",";
		}
		else {
			$this->cvs_citation .= $this->reference->volume.",".$this->reference->issue.",".$this->reference->year.",".$this->reference->doi.",";
		}
		$this->cvs_citation .= $this->usage_metrics->ng1sans.",".$this->usage_metrics->ngb10msans.",".$this->usage_metrics->ngb30msans.",".$this->usage_metrics->ng3sans.",".$this->usage_metrics->ng7sans.",".$this->usage_metrics->bt5usans.",".$this->usage_metrics->igor.",";
		foreach ($this->authors as $author) {
			$this->cvs_citation .= $author->author->get_printed_name().",";
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
	
	/*
	/	Comparison function(s)
	*/
	public function compare_to_other_pub($pub) {
		$comp_array = array();
		if ($pub instanceof publication) {
			$comp_array['refer'] = $this->reference->compare($pub->reference);
			$comp_array['usage'] = $this->usage_metrics->compare($pub->usage_metrics);
			$comp_array['se'] = $this->se_metrics->compare($pub->se_metrics);
			for ($i = 0; $i < sizeof($this->authors); $i++) {
				
			}
		} elseif ($pub instanceof String) {
			$pub = publication::with_id($pub);
			$comparison = $this->compare_to_other_pub($pub);
		}
	}
		
}

/*
/ Class all data types below should extend.
/ Ensures common functionality for publication class
*/
abstract class dataclass {
	// Unique ID - may map to another ID.
	protected $id;
	// DB connection params
	protected $connect;
	protected $tbl;
	protected $cols;
	// Boolean to say if object is a new entry to the database
	public $new;
	
	// Required function for printing/output purposes
	abstract public function __toString();
	// Required function for processing a row taken straight from the db
	abstract protected function process_raw_db_row($row);
	// Required constructor for all child classes
	protected function __construct() {
		// DB connection parameters
		$this->connect = new connection();
		$this->tbl = $this->tbl_name;
		$this->cols = $GLOBALS['tables'][$this->tbl];
	}
	
	// Pointed constructor for getting information based on the id
	public static function with_id($id) {
		$class = get_called_class();
		$instance = new $class();
		$result = $instance->get_by_id($id);
		if ($result) {
			$instance->process_raw_db_row($result);
		} else {
			$instance = NULL;
		}
		return $instance;
	}
	public static function with_values($values) {
		$class = get_called_class();
		$empty = new $class();
		$constraints = array();
		for ($i = 0; $i < sizeof($values); $i++) {
			if ($values[$i]) {
				$constraints[$i] = $empty->cols[$i]." = '".$values[$i]."'";
			}
		}
		if (sizeof($constraints) > 0) {
			$result = $empty->connect->get_first_result($empty->tbl, $empty->cols, $constraints);
		} else {
			return $empty;
		}
		if ($result) {
			$empty->process_raw_db_row($result);
		} else {
			$empty = self::add_new($values);
		}
		return $empty;
	}
	protected static function add_new($values) {
		$class = get_called_class();
		$empty = new $class();
		$empty->connect->insert($empty->tbl, $empty->cols, $values);
		return self::with_values($values);
	}
	public function remove_entry() {
		$constraints = array($this->cols[0]."='".$this->id."'");
		$result = $this->connect->delete_entry($this->tbl, $constraints, 0);
	}
	
	//FINISH THIS
	public function modify_entry($values) {
		foreach ($values as $key => $value) {
			
		}
	}
	
	//Helper functions for inherited classes
	public function get_id() {
		return $this->id;
	}
	protected function get_last_insert() {
		return $this->connect->get_most_recent_insert_id($this->cols[0], $this->tbl);
	}
	protected function get_by_id($id) {
		$constraints = array($this->cols[0]." ='$id'");
		return $this->connect->get_first_result($this->tbl, $this->cols, $constraints);
	}
}

/*
/ Class holding bibliographic information of an article
*/
class Reference extends dataclass {
	// ID maps directly to publication.id
	// No method should override this behavior
	private $publication_id;
	// Bibliographic information
	public $title;
	public $volume;
	public $issue;
	public $firstpage;
	public $journal;
	public $year;
	public $doi;
	public $pdf;
	protected $tbl_name = 'publication';
	protected static $input_names = ['id', 'title', 'journal', 'volume', 'issue', 'firstpage', 'year', 'number_of_authors', 'pdf', 'doi'];
	
	public static function with_values($values) {
		$jarray = array('id' => @$values['journal'], 'jtitle' => @$values['jtitle'], 'jabbrev' => @$values['jabbrev']);
		$journal = Journal::with_values($jarray);
		$values['journal'] = $journal->get_id();
		for ($i = 0; $i < sizeof(self::$input_names); $i++) {
			$valuearray[$i] = isset($values[self::$input_names[$i]]) ? $values[self::$input_names[$i]] : NULL;
			$valuearray[$i] = isset($values[$i]) ? $values[$i] : $valuearray[$i];
		}
		$instance = parent::with_values($valuearray);
		return $instance;
	}
	
	// Required functions needed to inherit from dataclass
	protected function process_raw_db_row($row) {
		$this->id = $row[0];
		$this->title = $row[1];
		$this->journal = Journal::with_id($row[2]);
		$this->volume = $row[3];
		$this->issue = $row[4];
		$this->firstpage = $row[5];
		$this->year = $row[6];
		$this->pdf = $row[8];
		$this->doi = $row[9];
		$this->publication_id = $this->id;
	}
	public function __toString() {
		$string = "\"".$this->title.".\" ".$this->journal->__toString()." ";
		if ($this->volume == "Press" || $this->volume == "Submitted") {
			$string .= $this->volume;
		}
		else {
			$string .= $this->volume.".".$this->issue. " (".$this->year."): ".$this->firstpage;
		}
		return $string;
	}
	
	public static function get_by_title($title) {
		$instance = new self();
		$instance->tbl = $instance->tbl_name;
		$instance->cols = $GLOBALS['tables'][$instance->tbl];
		$constraints = array($instance->cols[1]." ='$title'");
		return $instance->connect->get_all_results($instance->tbl, $instance->cols, $constraints);
	}
	private function check_inputs() {
		$valid['id'] = (isset($this->id) && ($this->id != 0));
		$valid['title'] = (strlen($this->title) > 0);
		$valid['journal'] = ($this->journal != FALSE);
		$valid['issue'] = ((strtolower($this->volume) == "press") || ($this->issue != FALSE) || (strtolower($this->volume) == "submitted"));
		$valid['volume'] = ((strtolower($this->volume) == "press") || ($this->volume != FALSE) || (strtolower($this->volume) == "submitted"));
		$valid['firstpage'] = ((strtolower($this->volume) == "press") || ($this->firstpage != FALSE) || (strtolower($this->volume) == "submitted"));
		$valid['year'] = ($this->year != FALSE);
		$valid['doi'] = ((preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$this->doi)) || ($this->doi == FALSE));
		return $valid;
	}
}

/*
/ Class holding information on a journal
*/
class Journal extends dataclass {
	// Journal information as it is in the DB
	public $name = "";
	public $abbreviation = "";
	protected $tbl_name = 'journal';
	protected static $input_names = ['id', 'jtitle', 'jabbrev'];
	
	public static function with_values($values) {
		for ($i = 0; $i < sizeof(self::$input_names); $i++) {
			$valuearray[$i] = isset($values[self::$input_names[$i]]) ? $values[self::$input_names[$i]] : NULL;
			$valuearray[$i] = isset($values[$i]) ? $values[$i] : $valuearray[$i];
		}
		$instance = parent::with_values($valuearray);
		return $instance;
	}
	protected function process_raw_db_row($row) {
		$this->id = $row[0];
		$this->name = $row[1];
		$this->abbreviation = $row[2];
	}
	public function __toString() {
		return $this->abbreviation;
	}
	
	/*
	/ Database interaction functions
	*/
	public static function with_title($title) {
		$instance = new self();
		$constraints = array($instance->cols[1]." ='$title'");
		$result = $instance->connect->get_first_result($instance->tbl, $instance->cols, $constraints);
		$instance->process_raw_db_row($result);
		return $instance;
	}
}

/*
/ Class holding information on an author of a specific article
*/
class Authorship extends dataclass {
	public $author = "";
	public $author_number = 0;
	private $publication_id = 0;
	protected $tbl_name = 'publication_author';
	
	// Override dataclass methods because table has no unique ids (many-to-many)
	public static function with_values($pub_id, $author_number, $full_name) {
		$author = Author::with_full_name($full_name);
		$values = array($author->get_id(), $pub_id, $author_number);
		$instance = parent::with_values($values);
		return $instance;
	}
	public static function with_author($pub_id, $author_number, $author) {
		if ($author instanceof Author) {
			$values = array($author->get_id(), $pub_id, $author_number);
			$instance = parent::with_values($values);
		}
		elseif ($author instanceof String) {
			$instance = self::with_values($pub_id, $author_number, $author);
		}
		return $instance;
	}
	public static function with_id($pub_id, $number) {
		$instance = new self();
		$result = $instance->get_by_publink($pub_id, $number);
		if ($result) {
			$row = $result[0];
			$instance->process_raw_db_row($row);
		}
		return $instance;
	}
	
	private function get_auth_pub_link() {
		$constraints = array($this->cols1[0]." = '$this->id'", $this->cols1[1]." = '$this->publication_id'");
		if ($this->author_number) {
			array_push($constraints, $this->cols1[2]." = '$this->author_number'");
		}
		return $this->connect->get_all_results($this->tbl1, $this->cols1, $constraints);
	}
	public function get_by_publink($publication_id, $author_number) {
		$constraints = array($this->cols[1]." = '$publication_id'", $this->cols[2]." = '$author_number'");
		return $this->connect->get_all_results($this->tbl, $this->cols, $constraints);
	}
	public function get_publication_id() {
		return $this->publication_id;
	}
	
	public function remove_entry() {
		$author_id = $this->author->get_id();
		$constraints = array($this->cols[0]."='".$author_id."'", $this->cols[1]."='".$this->publication_id."'");
		$result = $this->connect->delete_entry($this->tbl, $constraints, 0);
		$auth_constraints = array($this->cols[0]."='".$author_id."'");
		if (!$this->connect->get_all_results($this->tbl, $this->cols, $auth_constraints)) {
			$this->author->remove_entry();
		}
	}
	// Required methods for dataclass
	protected function process_raw_db_row($row) {
		$this->id = $row[1].".".$row[2];
		$this->author = Author::with_id($row[0]);
		$this->publication_id = $row[1];
		$this->author_number = $row[2];
	}
	public function __toString() {
		$printed_name = "";
		if ($this->author_number != 1) {
			$printed_name .= ", ";
		}
		if ($this->author instanceof Author) {
			$printed_name .= $this->author->__toString();
		}
		return $printed_name;
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
	private $full_name;
	protected $tbl_name = "author";
	
	public static function with_name($first, $middle, $last) {
		$empty = new self();
		$values = array(NULL, $first, $middle, $last);
		$instance = self::with_values($values);
		$instance->run_on_creation();
		return $instance;
	}
	public static function with_full_name($full_name) {
		$authorNoPeriods = preg_replace("/\./", "", $full_name);
		$author = explode(' ', $authorNoPeriods);
		$sizeme = sizeof($author);
		if($sizeme == 2) {
			$middle = NULL;
			$last = $author[1];
		} elseif ($sizeme == 3) {
			$last = $author[2];
			$middle = $author[1];
		} elseif ($sizeme > 3) {
			$last = $author[$sizeme - 1];
			$middle = "";
			for ($i = 1; $i < $sizeme - 1; $i++) {
				$middle .= $author[$i]." ";
			}
		} else {
			$middle = "";
			$last = "";
		}
		$first = $author[0];
		return self::with_name($first, $middle, $last);
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
	private function create_full_name() {
		$fullname = $this->firstname." ".$this->middlename." ".$this->lastname;
		$this->full_name = $fullname;
	}
	private function run_on_creation() {
		$this->create_printed_name();
		$this->create_mla_name();
		$this->create_full_name();
	}
	
	public function get_printed_name() {
		if (!isset($this->printed_name)) {
			$this->create_printed_name();
		}
		return $this->printed_name;
	}
	public function get_mla_name() {
		if (!isset($this->mla_name)) {
			$this->create_mla_name();
		}
		return $this->mla_name;
	}
	public function get_full_name() {
		if (!isset($this->full_name)) {
			$this->create_full_name();
		}
		return $this->full_name;
	}
	
	// Required methods to extend dataclass
	protected function process_raw_db_row($row) {
		$this->id = $row[0];
		$this->firstname = $row[1];
		$this->middlename = $row[2];
		$this->lastname = $row[3];
		$this->create_printed_name();
		$this->create_mla_name();
	}
	public function __toString() {
		return $this->get_printed_name();
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
	protected $tbl_name = 'usagemetrics';
	protected static $input_names = ['id', 'nistauthor', 'ng3sans', 'ng7sans', 'ng1sans', 'ngb10msans', 'ngb30msans', 'bt5usans', 'igor'];
	
	public static function with_values($values) {
		for ($i = 0; $i < sizeof(self::$input_names); $i++) {
			$valuearray[$i] = isset($values[self::$input_names[$i]]) ? $values[self::$input_names[$i]] : NULL;
			$valuearray[$i] = isset($values[$i]) ? $values[$i] : $valuearray[$i];
		}
		$instance = parent::with_values($valuearray);
		return $instance;
	}
	// Required functions to inherit from dataclass
	protected function process_raw_db_row($row) {
		$this->id = $row[0];
		$this->nistauthor = $row[1];
		$this->ng3sans = $row[2];
		$this->ng7sans = $row[3];
		$this->ng1sans = $row[4];
		$this->bt5usans = $row[7];
		$this->ngb10msans = $row[5];
		$this->ngb30msans = $row[6];
		$this->igor = $row[8];
	}
	public function __toString() {
		$string = "This publication used the following instruments:";
		$string .= $this->ng3sans == 1 ? " NG3 30m SANS," : "";
		$string .= $this->ng7sans == 1 ? " NG7 30m SANS," : "";
		$string .= $this->ng1sans == 1 ? " NG1 8m SANS," : "";
		$string .= $this->bt5usans == 1 ? " BT5 USANS," : "";
		$string .= $this->ngb10msans == 1 ? " NGB 10m SANS," : "";
		$string .= $this->ngb30msans == 1 ? " NGB 30m SANS," : "";
		$string = rtrim($string, ",");
		$string .= ".";
		if ($string == "This publication used the following instruments:.") {
			$string = "No NIST SANS instruments were used in this publication.";
		}
		if ($this->nistauthor) {
			$string .= " A NIST representative is an author on this publication.";
		}
		if ($this->igor) {
			$string .= " The SANS IGOR reduction and analysis macros were used to process the data in this publication.";
		}
		return $string;
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
	protected $tbl_name = 'sampleenvironmentmetrics';
	protected static $input_names = ['id', 'sample_changer', 'rheometer', 'sc_boulder', 'sc_12', 'sc_pp', 'ccr', 'em', 'scm', 'pol', 'humidity', 'userequip', 'other'];
	
	public static function with_values($values) {
		for ($i = 0; $i < sizeof(self::$input_names); $i++) {
			$valuearray[$i] = isset($values[self::$input_names[$i]]) ? $values[self::$input_names[$i]] : NULL;
			$valuearray[$i] = isset($values[$i]) ? $values[$i] : $valuearray[$i];
		}
		$instance = parent::with_values($valuearray);
		return $instance;
	}
	protected function process_raw_db_row($row) {
		$this->id = $row[0];
		$this->sample_changer = $row[1];
		$this->rheometer = $row[2];
		$this->shear_cell_boulder = $row[3];
		$this->shear_cell_12plane = $row[4];
		$this->shear_cell_plateplate = $row[5];
		$this->closed_cycle_refrigerator = $row[6];
		$this->electromagnet = $row[7];
		$this->superconducting_magnet = $row[8];
		$this->polarization = $row[9];
		$this->humidity_cell = $row[10];
		$this->user_equipment = $row[11];
		$this->other = $row[12];
	}
	public function __toString() {
		$string = "Sample environment equipment used by this publication:";
		$string .= $this->sample_changer == 1 ? " sample changer," : "";
		$string .= $this->rheometer == 1 ? " rheometer," : "";
		$string .= $this->shear_cell_boulder == 1 ? " boulder shear cell," : "";
		$string .= $this->shear_cell_12plane == 1 ? " 1,2-plane shaer cell," : "";
		$string .= $this->shear_cell_plateplate == 1 ? " plate-plate shear cell," : "";
		$string .= $this->closed_cycle_refrigerator == 1 ? " closed cycle refrigerator," : "";
		$string .= $this->electromagnet == 1 ? " electromagnet," : "";
		$string .= $this->superconducting_magnet == 1 ? " superconducting magnet," : "";
		$string .= $this->polarization == 1 ? " polarized beam analysis," : "";
		$string .= $this->humidity_cell == 1 ? " humidity chamber," : "";
		$string .= $this->user_equipment == 1 ? " user-supplied equipment," : "";
		$string .= $this->other == 1 ? " other or unknown" : "";
		$string = rtrim($string, ",");
		$string .= ".";
		return $string;
	}
}

?>