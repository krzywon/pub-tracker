<?php

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'publications_rework';
$tables = array(
								'publication' => array('id', 'title', 'journal_id', 'volume', 'issue', 'firstpage', 'year', 'number_of_authors', 'pdf', 'doi'), 
								'author' => array('id', 'first_name', 'middle_name', 'last_name'), 
								'publication_author' => array('author_id', 'publication_id', 'author_number'), 
								'journal' => array('id', 'name', 'abbreviation'), 
								'sampleenvironmentmetrics' => array('publication_id', 'sample_changer', 'rheometer', 'shear_cell_boulder', 'shear_cell_12plane', 'shear_cell_plateplate', 'closed_cycle_refrigerator', 'electromagnet', 'superconducting_magnet', 'polarization', 'humidity_cell', 'user_equipment', 'other'),
								'usagemetrics' => array('publication_id', 'nist_author', 'ng3_30m_sans', 'ng7_30m_sans', 'ng1_8m_sans', 'ngb_10m_sans', 'ngb_30m_sans', 'bt5_usans', 'igor_macros')
								);

?>