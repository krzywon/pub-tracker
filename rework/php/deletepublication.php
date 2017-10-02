<?php

$pubtitle = $_GET['pubtitle'];

include 'publication_class.php';
$pub = publication::with_title($pubtitle);

$pub->remove_entry();

header('Location: ../modification.html');

?>