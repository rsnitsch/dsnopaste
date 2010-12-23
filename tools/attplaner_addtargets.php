<?php
	/**
	 * Angriffsplaner - Schnittstelle
	 * 
	 * Schnittstelle zum Hinzufügen von Zielen/Dörfern zu Angriffsplänen
	 * z.B. für den Adelsplaner (adelsplaner.de)
	 * 
	 * Beispiel: ?targets=500|500,400|400
	 * 
	 * @todo: finish
	 */
	 
    define('INC_CHECK',true);
    $root_path='../';
    include($root_path.'include/config.inc.php');
    
    $smarty = new nopSmarty();
    
    $smarty->assign('title','Angriffsplaner');
    $smarty->assign('root_path',$root_path);
    
    $errors = array();
    $debugs = array();
    
    // kleine Hilfsfunktion
    function _displayErrors() {
    	global $smarty, $errors, $debugs;
    	displayErrors($smarty, $errors, $debugs);
    }
    
    // ist die Seite aktiviert
    if(!CFG_ENABLED) {
    	$smarty->display('offline.tpl');
    	exit();
    }
    
    // sind Ziele angegeben
    if(empty($_REQUEST['targets'])) {
    	$errors[] = "Keine Ziele angegeben!";
    	_displayErrors();
    }
    
    $targets = explode(',', $_REQUEST['targets']);
    
    // wie viele Ziele
    if(count($targets) > 10) {
    	$errors[] = "Maximal 10 Ziele können auf einmal hinzugefügt werden!";
    	_displayErrors();
    }
    
    // sind die Ziele gültig
    foreach($targets as $target) {
    	if(!validCoord($target)) {
    		$errors[] = "Ungültiges Ziel: ".htmlspecialchars($target);
    	}
    }
    if(count($errors)>0) {
    	_displayErrors();
    }
    
    // die Eingabedaten sind gültig!
    
    // formular anzeigen oder ist der Angriffsplan, zu dem die Ziele hinzugefügt
    // werden sollen, bereits ausgesucht?
    if(empty($_REQUEST['planid'])) {    
	    // jetzt muss herausgefunden werden, von welchen Angriffsplänen
	    // der Benutzer Administrator ist
	    $plans = array();
	    
	    foreach($_COOKIE as $name => $content) {
	    	if(strpos($name, 'admin_')===0) {
	    		enableMySQL(TRUE) or noSqlConnection($smarty);
	    		
	    		$res = $mysql->sql_query("SELECT id,key FROM attplans WHERE admin_key='".addslashes($content)."'");
	    		$plans[] = array($mysql->sql_result($res, 0, 'id'), $mysql->sql_result($res, 0, 'key'));
	    	}
	   	}
	   	
	   	$smarty->assign('plans', $plans);
	   	$smarty->assign('targets', urlencode($_REQUEST['targets']));
	   	$smarty->display('attplaner_addtargets.tpl');
    }
    else {
    	// die Dörfer/Ziele tatsächlich hinzufügen
    	if(!ctype_digit($_REQUEST['planid']) and $_REQUEST['planid'] != -1) {
    		$errors[] = "Ungültige AngriffsplanID.";
    		_displayErrors();
    	}
    	
    	enableMySQL(TRUE);
    	
    	$planid = $_REQUEST['planid'];
    	
    	// ist der Benutzer ein Admin von dem Angriffsplan?
    	$res = $mysql->sql_query("SELECT server,key,admin_key FROM attplans WHERE id='".addslashes($planid)."'");
    	
    	if($mysql->sql_num_rows($res)==0) {
    		$errors[] = "Dieser Plan scheint nicht zu existieren!";
	        $debugs[]='SQL-Abfrage: '.$mysql->lastquery;
	        $debugs[]='SQL-Fehlermeldung: '.$mysql->lasterror;
	        _displayErrors();
    	}
    	
    	$key = $mysql->sql_result($res, 0, 'key');
    	$admin_key = $mysql->sql_result($res, 0, 'admin_key');
    	$server = $mysql->sql_result($res, 0, 'admin_key');
    	
    	if(empty($_COOKIE['admin_'.$key]) or $_COOKIE['admin_'.$key] != $admin_key) {
    		$errors[] = "Du bist kein Ersteller/Admin dieses Angriffsplans!";
    		_displayErrors();
    	}
    	
    	// der Benutzer ist wirklich Admin von dem Plan, also
    	// müssen jetzt die Ziele hinzugefügt werden
    	/*
    	foreach($targets as $target) {
    		$mysql->sql_query("INSERT INTO attplans_actions " .
    				"(attplan_id, typ, from, to, runtime, senddate, arrive, note)")
    	}
    	*/
    }
?>