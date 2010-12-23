<?php
	define('INC_CHECK',true);
	$root_path='./';
	include($root_path.'include/config.inc.php');
	
	// Output-Control anlegen
	$output = new nopSmarty();

	$output->assign('title', 'ANB / Impressum');
	$output->assign('rootpath', $root_path);
	
	
	// die ANBs anzeigen
	$output->display('anb.tpl');
?>