<?php
	define('INC_CHECK',true);
	$root_path='./';
	include($root_path.'include/config.inc.php');
	
	// Output-Control anlegen
	$output = new nopSmarty();

	$output->assign('title', 'Impressum');
	$output->assign('root_path', $root_path);
	$output->display('legal.tpl');
?>