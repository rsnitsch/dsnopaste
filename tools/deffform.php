<?php
	define('INC_CHECK',true);
	$root_path='../';
	include($root_path.'include/config.inc.php');
	
	// Output-Control anlegen
	$output = new nopSmarty();

	// den Header inkludieren
	$output->assign('root_path',$root_path);
	$output->assign('title','Deffformular');
	
	// Inhalt
	if(!CFG_ENABLED)
	{
		$output->display('offline.tpl');
	}
	else
	{
            switch(isset($_GET['s']) ? $_GET['s'] : '')
            {
                case 'modern':
                    $output->assign('title','Deffformular (Welt 10+)');
                    $output->display('deffform_modern.tpl');
                    break;
                default:
                    $output->display('deffform.tpl');
                    break;
            }
	}
?>