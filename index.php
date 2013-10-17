<?php
	define('INC_CHECK',true);
	$root_path='./';
	include($root_path.'include/config.inc.php');

	// Smarty starten
	$output = new nopSmarty();

	// den Header inkludieren
	$output->assign('root_path', $root_path);
	$output->assign('title','Start');
	$output->assign('subid', 'start');

	// seite darstellen
	$output->display('start.tpl');
?>
