<!DOCTYPE html>
<html>
	<head>
		<title>SANS Publication Class Test Results</title>
		<meta charset="UTF-8">
		<link rel="stylesheet" href="http://www.ncnr.nist.gov/programs/sans/scripts/style.css" type="text/css">
		<link rel="stylesheet" href="style.css" type="text/css">
	</head>
	
	<body>
	
		<?php
		
		//TODO: Tests Needed
		
		//TODO: publication class requirements
		// (3) Any bugs found after testing
		
		include 'publication_class.php';
		
		/*
		/ Helper methods used to check and print out the test results
		*/
		function check_for_errors($list) {
			$errors = "";
			foreach($list as $value => $test) {
				if ($test) {
					$errors .= "<li class=\"warning\">The ".$value." did not pass the ".$test." test.</li>\n";
				}
			}
			return $errors;
		}
		function print_results($errors, $success_msg) {
			if ($errors) {
				print $errors;
			} else {
				print "<li class=\"success\">$success_msg</li>\r";
			}
		}
		
		/*
		/ Tests on the Publication() class
		*/
		function test_get_known_pub()
		{
			$refer = array(
				"id" => "1000",
				"title" => "Swelling the Hydrophobic Core of Surfactant-Suspended Single Wall Carbon Nanotubes: A SANS Study",
				"year" => "2011",
				"volume" => "27",
				"issue" => "18",
				"firstpage" => "11372",
				"journal.id" => "1",
				"doi" => "/10.1021/la202117p"
				);
			$authors = array(
				"author1" => "2279",
				"author2" => "2280"
				);
			$se = array();
			$usage = array("ng3sans" => 1);
			$pub = new publication($refer, $authors, $se, $usage);
			$author1 = $pub->get_author_by_author_number(1)[0];
			$author2 = $pub->get_author_by_author_number(2)[0];
			
			$checks = array("full_pub_id" => $pub->reference->get_id() != '1000',
											"full_pub_year" => $pub->reference->year != 2011,
											"full_pub_volume" => $pub->reference->volume != 27,
											"full_pub_issue" => $pub->reference->issue != 18,
											"full_pub_firstpage" => $pub->reference->firstpage != 11372,
											"full_pub_ng3sans" => !$pub->usage_metrics->ng3sans,
											"full_pub_sample_changer" => $pub->se_metrics->sample_changer,
											"full_pub_rheometer" => $pub->se_metrics->rheometer,
											"full_pub_author1" => $author1->get_id() != 2279,
											"full_pub_author2" => $author2->get_id() != 2280,
											"full_pub_author_number" => $pub->get_number_of_authors() != 2,
											);
			$errors = check_for_errors($checks);
			print_results($errors, "The full publication was loaded from the DB properly.");
		}
		function test_create_pub_in_press()
		{
			$refer = array(
				"title" => "ALKJfdlkasdfimvmiioq38imlkv oisadflksdfoiwane ",
				"year" => "5605853",
				"volume" => "Press",
				"issue" => "",
				"firstpage" => "",
				"journal.id" => "99",
				"doi" => "http://dx.doi.org/10.1021/adfasdfasdfasd"
				);
			$usage = array("ng3sans" => 1);
			$se =	array("sample_changer" => "TRUE");
			$authors = array(
				"author1" => "James K. Polk",
				"author2" => "David F R Mildner"
			);
			$pub = new publication($refer, $authors, $se, $usage);
			$pub->add_author_at_end("Allen Thompson");
			$errors = "";
			
			$auth_fortests = $pub->get_author_by_author_number(1)[0];
			$pub_link = $auth_fortests->get_by_publink();
			$auth_id_fortests = $auth_fortests->get_id();
			
			$checks = array("press_pub_authors_number" => $pub->get_number_of_authors() != 3,
											"press_pub_year" => $pub->reference->year != 5605853,
											"press_pub_volume" => $pub->reference->volume != "Press",
											"press_pub_issue" => $pub->reference->issue != 0,
											"press_pub_firstpage" => $pub->reference->firstpage != 0
											);
			$errors = check_for_errors($checks);
			print_results($errors, "The new publication in press was saved to the DB properly.");
			
			$pub->remove_entry();
			$pub_link_redux = $auth_fortests->get_by_publink();
			$checks = array("press_pub_del" => $pub->reference->get_by_title("ALKJfdlkasdfimvmiioq38imlkv oisadflksdfoiwane "),
											"press_pub_auth_link_del" => $pub_link_redux,
											);
			$errors = check_for_errors($checks);
			print_results($errors, "The new publication in press was removed from the DB properly.");
		}
		function test_create_pub_submitted()
		{
			$refer = array(
				"title" => "ALKJfdlkasdfimvmiioq38imlkv oisadflksdfoiwane ",
				"year" => "5605853",
				"volume" => "Submitted",
				"issue" => "",
				"firstpage" => "",
				"journal.id" => "99",
				"doi" => "http://dx.doi.org/10.1021/adfasdfasdfasd"
				);
			$usage = array("ng3sans" => 1);
			$se =	array("sample_changer" => "TRUE");
			$authors = array(
				"author1" => "James K. Polk",
				"author2" => "David F R Mildner"
			);
			$pub = new publication($refer, $authors, $se, $usage);
			$pub->add_author_at_end("Allen Thompson");
			$errors = "";
			
			$auth_fortests = $pub->get_author_by_author_number(1)[0];
			$pub_link = $auth_fortests->get_by_publink();
			$auth_id_fortests = $auth_fortests->get_id();
			
			$checks = array("press_pub_authors_number" => $pub->get_number_of_authors() != 3,
											"press_pub_year" => $pub->reference->year != 5605853,
											"press_pub_volume" => $pub->reference->volume != "Submitted",
											"press_pub_issue" => $pub->reference->issue != 0,
											"press_pub_firstpage" => $pub->reference->firstpage != 0
											);
			$errors = check_for_errors($checks);
			print_results($errors, "The new publication submitted was saved to the DB properly.");
			
			$pub->remove_entry();
			$pub_link_redux = $auth_fortests->get_by_publink();
			$checks = array("press_pub_del" => $pub->reference->get_by_title("ALKJfdlkasdfimvmiioq38imlkv oisadflksdfoiwane "),
											"press_pub_auth_link_del" => $pub_link_redux,
											);
			$errors = check_for_errors($checks);
			print_results($errors, "The new publication submitted was removed from the DB properly.");
		}
		function test_get_pub_in_press()
		{
			$refer = array(
				"id" => "1281"
				);
			$usage = array();
			$se =	array();
			$authors = array();
			$pub = new publication($refer, $authors, $se, $usage);
			$errors = "";
			
			$checks = array("press_pub_authors_number" => $pub->get_number_of_authors() != 3,
											"press_pub_year" => $pub->reference->year != 2014,
											"press_pub_volume" => $pub->reference->volume != "Press",
											"press_pub_issue" => $pub->reference->issue != 0,
											"press_pub_firstpage" => $pub->reference->firstpage != 0
											);
			$errors = check_for_errors($checks);
			print_results($errors, "The known publication in press was loaded from the DB properly.");
		}
		
		/*
		/ Tests on the Citation Generation Functions
		*/
		function test_cvs_citation()
		{
			$refer = array("id" => 1168);
			$pub = new publication($refer);
			$checks = array("cvs_citation" => $pub->cvs_citation != "Unique Structural:;:;: Dynamical:;:;: and Functional Properties of K11-Linked Polyubiquitin Chains,Structure,21,7,2013,http://dx.doi.org/10.1016/j.str.2013.04.029,0,0,0,1,0,0,0,C.A. Castaneda,T.R. Kashyap,M.A. Nakasone,S. Krueger,D. Fushman",
											"cvs_print" => $pub->cvs_printable != "<tr><td>Unique Structural, Dynamical, and Functional Properties of K11-Linked Polyubiquitin Chains</td><td>Structure</td><td>21</td><td>7</td><td>2013</td><td>http://dx.doi.org/10.1016/j.str.2013.04.029</td><td>C.A. Castaneda</td><td>T.R. Kashyap</td><td>M.A. Nakasone</td><td>S. Krueger</td><td>D. Fushman</td></tr>"
											);
			$errors = check_for_errors($checks);
			print_results($errors, "The cvs citation generation was succesful.");
		}
		function test_chrns_citation()
		{
			$refer = array("id" => 1168);
			$pub = new publication($refer);
			$checks = array("chrns_citation" => $pub->chrns_citation != "C.A. Castaneda, T.R. Kashyap, M.A. Nakasone, S. Krueger, D. Fushman, \"Unique Structural, Dynamical, and Functional Properties of K11-Linked Polyubiquitin Chains\", Structure, 21, 1168 (2013)\n",
											);
			$errors = check_for_errors($checks);
			print_results($errors, "The chrns citation generation was succesful.");
		}
		function test_mla_citation()
		{
			$refer = array("id" => 1168);
			$pub = new publication($refer);
			$checks = array("citation" => $pub->citation != "Castaneda, Carlos A. Kashyap, Tanuja R. Nakasone, Mark A. Krueger, Susan. Fushman, David. \"Unique Structural, Dynamical, and Functional Properties of K11-Linked Polyubiquitin Chains.\" <i>Structure</i> 21.7 (2013): 1168. Print.\n",
											);
			$errors = check_for_errors($checks);
			print_results($errors, "The citation generation was succesful.");
		}
		
		/*
		/ Tests on the Journal() class
		*/
		function test_load_known_journal()
		{
			$id = 1;
			$name = "Langmuir";
			$abbrev = "Langmuir";
			$journal = new Journal($id);
			$journal_by_name = new Journal(0, $name);
			
			$checks = array("id" => $journal->get_id() != 1,
											"name" => $journal->name != "Langmuir",
											"abbrev" => $journal->abbreviation != "Langmuir",
											"name_id" => $journal_by_name->get_id() != 1,
											"name_name" => $journal_by_name->name != "Langmuir",
											"name_abbrev" => $journal_by_name->abbreviation != "Langmuir",
											);
			
			$errors = check_for_errors($checks);
			print_results($errors, "The journal with ID 1 was loaded from the DB properly.");
		}
		function test_create_new_journal()
		{
			$name = "TEST JOURNAL";
			$abbrev = "TEST J.";
			$journal = new Journal($id = 0, $name = $name, $abbrev = $abbrev);
			$id = $journal->get_id();
			
			$journal->remove_entry();
			$result = $journal->get_by_id($id);
			
			$checks = array("id" => $id == 0,
											"name_abbrev" => $journal->name != "TEST JOURNAL",
											"name_abbrev" => $journal->abbreviation != "TEST J.",
											"removal" => $result);
			
			$errors = check_for_errors($checks);
			print_results($errors, "The journal creation and destruction were successful.");
		}
		function test_create_empty_journal()
		{
			$journal = new Journal();
			$errors = "";
			
			$checks = array("id" => $journal->get_id(), "name" => $journal->name,"abbrev" => $journal->abbreviation);
			
			$errors = check_for_errors($checks);
			print_results($errors, "The empty journal test was successful.");
		}
		
		/*
		/ Tests on the Reference() class
		*/
		function test_load_known_reference()
		{
			$id = 1252;
			$ref = new Reference($id);
			
			$checks = array("id" => $ref->get_id() != 1252, 
											"doi" => $ref->doi != "http://dx.doi.org/10.1021/la501071s",
											);
			
			$errors = check_for_errors($checks);
			print_results($errors, "Loading a known reference test was successful.");
		}
		function test_create_new_reference()
		{
			$id = 0;
			$title = "TEST ARTICLE II - DELETE WHEN FINISHED";
			$volume = 30;
			$issue = 1;
			$firstpage = 98765;
			$year = 2025;
			$doi = "http://dx.doi.org/10.1021/bm501062d";
			$journal = 11;
			
			$ref = new Reference($id, $title, $volume, $issue, $firstpage, $journal, $year, $doi);
			$id = $ref->get_id();
			
			$checks = array("doi" => $doi !="http://dx.doi.org/10.1021/bm501062d",
											"year" => $year != 2025,
											"title" => $title != "TEST ARTICLE II - DELETE WHEN FINISHED"
											);
			
			$ref->remove_entry();
			$still_exists = $ref->get_by_id($id);
			$checks["deleted"] = $still_exists;
			
			$errors = check_for_errors($checks);
			print_results($errors, "The reference creation and deletion test was successful.");
		}
		function test_create_empty_reference()
		{
			$ref = new Reference();
			$checks = array("id" => $ref->get_id(), 
											"issue" => $ref->issue,
											"volume" => $ref->volume
											);
			
			$errors = check_for_errors($checks);
			print_results($errors, "The empty reference test was successful.");
		}
		
		/*
		/ Tests on the Author() class
		*/
		function test_load_known_author_no_pub()
		{
			$author = new Author($id = 2661, $first = "Jeffery", $middle = "R", $last = "Krzywon", $pub_id = 0, $number = 0);
			
			$checks = array("id" => $author->get_id() != 2661, 
											"firstname" => $author->firstname != "Jeffery",
											"pub_id" => $author->get_publication_id(),
											"mla_name" => $author->get_mla_name() != "Krzywon, Jeffery R."
											);
			
			$errors = check_for_errors($checks);
			print_results($errors, "The known author with no publication test was successful.");
		}
		function test_load_known_author_pub()
		{
			$pub_id = 1281;
			$author_id = 2786;
			$first = "Katrina";
			$middle = "A";
			$last = "Jolliffe";
			$number = 0;
			
			$author = new Author($author_id, "", "", "", $pub_id, $number);
			$author_by_name = new Author(0, $first, $middle, $last, $pub_id, $number);
			
			$checks = array("firstname" => $author->firstname != 'Katrina', 
											"printed_name" => $author->get_printed_name() != 'K.A. Jolliffe',
											"id" => $author->get_id() != 2786,
											"pub_id" => $author->get_publication_id() != 1281,
											"author_number" => $author->author_number != 2,
											"name_id" => $author_by_name->get_id() != 2786,
											"name_firstname" => $author_by_name->firstname != 'Katrina', 
											"name_pub_id" => $author_by_name->get_publication_id() != 1281,
											"name_author_number" => $author_by_name->author_number != 2,
											"name_printed_name" => $author_by_name->get_printed_name() != 'K.A. Jolliffe'
											);
			
			$errors = check_for_errors($checks);
			print_results($errors, "The known author with linked publication test was successful.");
		}
		function test_create_new_author_no_pub()
		{
			$first = "Khalil-Ahmed";
			$middle = "Aziz";
			$last = "Ansari";
			
			$author = new Author(0, $first, $middle, $last, 0, 0);
			$id = $author->get_id();
			
			$checks = array("firstname" => $author->firstname != 'Khalil-Ahmed', "printed_name" => $author->get_printed_name() != 'K.-A.A. Ansari');
			
			$author->remove_entry();
			$still_exists = $author->get_by_id($id);
			$checks["deleted"] = $still_exists;
			
			$errors = check_for_errors($checks);
			print_results($errors, "The new author test was successful.");
		}
		function test_create_new_author_pub()
		{
			$first = "Khalil-Ahmed";
			$middle = "Aziz";
			$last = "Ansari";
			$pub_id = 1281;
			$author_number = 99;
			
			$author = new Author(0, $first, $middle, $last, $pub_id, $author_number);
			$result = $author->get_by_publink();
			$add_2nd_publink = $author->add_auth_pub_link();
			$result_2_publinks = $author->get_by_publink();
			
			$checks = array("firstname" => $author->firstname != 'Khalil-Ahmed', 
											"printed_name" => $author->get_printed_name() != 'K.-A.A. Ansari',
											"publink created" => !$result,
											"author number" => $author->author_number != 99,
											"two publinks" => sizeof($result_2_publinks) != 2
											);
					
			$id = $author->get_id();						
			$author->remove_entry();
			$auth_exists = $author->get_by_id($id);
			$author->remove_auth_pub_link();
			$result_after_delete = $author->get_by_publink();
			
			$checks["Author deleted"] = $auth_exists;
			$checks["one publink"] = sizeof($result_after_delete) != 1;
			$auth_pub_deleted = $author->remove_auth_pub_link();
			
			$errors = check_for_errors($checks);
			print_results($errors, "The new author with publink test was successful.");
		}
		function test_create_empty_author()
		{
			$auth = new Author();
			
			$errors = "";
			$empty_array = array("id" =>$auth->get_id(), "pub_id" => $auth->get_publication_id(), "auth_number" => $auth->author_number, "first" => $auth->firstname, "middle" => $auth->middlename, "last" => $auth->lastname);
			
			$errors = check_for_errors($empty_array);
			print_results($errors, "The empty author test was successful.");
		}
		
		/*
		/ Tests on the UsageMetrics class
		*/
		function test_load_known_usage()
		{
			$id = 1281;
			$usage = new UsageMetrics($id);
			
			$checks = array($usage->nistauthor, $usage->bt5usans, !$usage->ng3sans);
			
			$errors = check_for_errors($checks);
			print_results($errors, "The load known usage metrics test was successful.");
		}
		function test_create_new_usage()
		{
			$pub_id = 2500;
			$metrics = array('nistauthor' => TRUE, 'ng3sans' => FALSE, 'ng7sans' => FALSE, 'ng1sans' => TRUE, 'bt5usans' => TRUE);
			
			$usage = new UsageMetrics($pub_id, $metrics);
			$checks = array($usage->get_id() != 2500, $usage->igor, !$usage->nistauthor, !$usage->bt5usans, $usage->ngb10msans);
			
			$usage->remove_entry();
			$result = $usage->get_by_id($pub_id);
			
			$checks["Deleted"] = $result;
			
			$errors = check_for_errors($checks);
			print_results($errors, "The create new usage metrics test was successful.");
		}
		function test_create_empty_usage()
		{
			$usage = new UsageMetrics();
			
			$checks = array($usage->nistauthor, $usage->bt5usans, $usage->ng3sans);
			
			$errors = check_for_errors($checks);
			print_results($errors, "The load empty usage metrics test was successful.");
		}
		
		/*
		/ Tests on the SampleEnvironmentMetrics class
		*/
		function test_load_known_se()
		{
			$id = 1262;
			$usage = new SampleEnvironmentMetrics($id);
			
			$checks = array($usage->sample_changer, $usage->rheometer, !$usage->electromagnet, !$usage->polarization);
			
			$errors = check_for_errors($checks);
			print_results($errors, "The load known sample environment metrics test was successful.");
		}
		function test_create_new_se()
		{
			$pub_id = 2500;
			$metrics = array('sample_changer' => TRUE, 'sc_12' => FALSE, 'scm' => TRUE, 'humidity' => TRUE, 'other' => TRUE);
			
			$usage = new SampleEnvironmentMetrics($pub_id, $metrics);
			$checks = array($usage->get_id() != 2500, !$usage->sample_changer, !$usage->humidity_cell, !$usage->superconducting_magnet, $usage->shear_cell_12plane, $usage->polarization);
			
			$usage->remove_entry();
			$result = $usage->get_by_id($pub_id);
			
			$checks["Deleted"] = $result;
			
			$errors = check_for_errors($checks);
			print_results($errors, "The create new sample environmnet metrics test was successful.");
		}
		function test_create_empty_se()
		{
			$usage = new SampleEnvironmentMetrics();
			
			$checks = array($usage->sample_changer, $usage->rheometer, $usage->electromagnet, $usage->polarization);
			
			$errors = check_for_errors($checks);
			print_results($errors, "The load empty sample environment metrics test was successful.");
		}
		
		// Testing the Publication() class
		
		print "<ul>\r";
		$connect = new connection('publications_rework', $tables);
		$connect->reset_indices(1282, 2877, 209);
		
		test_get_known_pub();
		test_create_pub_in_press();
		test_create_pub_submitted();
		test_get_pub_in_press();
		test_load_known_journal();
		test_create_new_journal();
		test_create_empty_journal();
		test_load_known_reference();
		test_create_new_reference();
		test_create_empty_reference();
		test_load_known_author_no_pub();
		test_load_known_author_pub();
		test_create_empty_author();
		test_create_new_author_no_pub();
		test_create_new_author_pub();
		test_load_known_usage();
		test_create_empty_usage();
		test_create_new_usage();
		test_load_known_se();
		test_create_empty_se();
		test_create_new_se();
		test_cvs_citation();
		test_chrns_citation();
		test_mla_citation();
		
		$connect->reset_indices(1282, 2877, 209);
		print "</ul>\r";
		
		// Testing the PublicationDB() class
		
		?>
	</body>

</html>