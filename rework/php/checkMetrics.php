<?php

// TODO: Remove this entirely

$pdf = (@$_POST['pdf'] == TRUE ? 1 : 0);
$usage['nistauthor'] = (@$_POST['nistauthor'] == TRUE ? 1 : 0);
$usage['ng1sans'] = (@$_POST['ng1sans'] == TRUE ? 1 : 0);
$usage['ngb10msans'] = (@$_POST['ngb10msans'] == TRUE ? 1 : 0);
$usage['ngb30msans'] = (@$_POST['ngb30msans'] == TRUE ? 1 : 0);
$usage['ng3sans'] = (@$_POST['ng3sans'] == TRUE ? 1 : 0);
$usage['ng7sans'] = (@$_POST['ng7sans'] == TRUE ? 1 : 0);
$usage['bt5sans'] = (@$_POST['bt5usans'] == TRUE ? 1 : 0);
$usage['igor'] = (@$_POST['igor'] == TRUE ? 1 : 0);
$se['sample_changer'] = (@$_POST['changer'] == TRUE ? 1 : 0);
$se['rheometer'] = (@$_POST['rheometer'] == TRUE ? 1 : 0);
$se['sc_boulder'] = (@$_POST['bsc'] == TRUE ? 1 : 0);
$se['sc_12'] = (@$_POST['12sc'] == TRUE ? 1 : 0);
$se['sc_pp'] = (@$_POST['ppsc'] == TRUE ? 1 : 0);
$se['ccr'] = (@$_POST['ccr'] == TRUE ? 1 : 0);
$se['em'] = (@$_POST['em'] == TRUE ? 1 : 0);
$se['scm'] = (@$_POST['scm'] == TRUE ? 1 : 0);
$se['pol'] = (@$_POST['pa'] == TRUE ? 1 : 0);
$se['humidity'] = (@$_POST['humidity'] == TRUE ? 1 : 0);
$se['userequip'] = (@$_POST['userequip'] == TRUE ? 1 : 0);
$se['other'] = (@$_POST['otherequip'] == TRUE ? 1 : 0);

?>