<?php
	/**
	 * Externe Authentifizierung
	 * 
	 * Diese Datei wird aufgerufen, wenn die ext. Authentifizierung von DieStämme
	 * genutzt wurde. Als Parameter werden von DS übergeben:
	 * 
	 * sid = von NoPaste generierte ID
	 * username = der Accountname des Benutzers bei DieStämme
	 * hash = ein MD5-Hash aus sid, username und dem geheimen Key
	 * 
	 * 
	 * Die Authentifizierung bei NoPaste sieht vor, dass
	 * sich Besucher als Besitzer/Eigentümer mehrerer Accounts authentifizieren können
	 * (durch mehrmaliges Nutzen der ext. Auth.).
	 * Wird dann von NoPaste bei einer "Aktion" ein DS-Account gefordert, so können die
	 * Benutzer einen Account auswählen, der für diese Aktion genutzt werden soll
	 * (z.B. beim nutzen des Angriffsplaners).
	 * 
	 * Weiterhin ist geplant, dass die Nutzer jedem Account eine "Primärwelt" zuweisen
	 * können. Bei allen Aktionen, die mit einer bestimmten Spielwelt verknüpft sind,
	 * wird dann dieser Account vorausgewählt.
	 */
	 
	define('INC_CHECK',true);
	$root_path='../';
    include($root_path.'include/config.inc.php');
    $smarty = new nopSmarty();
    
	$session = Session::getInstance();
	
	// Ausloggen?
	if(isset($_GET['logout']))
		$session->destroy();
	$smarty->display('logout.tpl');
	exit();
	// -----------------------------------
	
	// Authentifizieren
	$username = paramGET('username', false);
	$sid = paramGET('sid', false);
	$hash = paramGET('hash', false);
	
	// Wenn alle Parameter übergeben wurden
	if($username && $sid && $hash) {
		// Überprüfen, ob die Quelle mit DieStämme übereinstimmt
		$md5 = md5($sid.$username.AUTH_SECRET);
		if($hash == $md5) {
			// den Account der Session hinzufügen
			$accounts = $session->get('accounts', array());
			$accounts[] = $username;
			$session->set('accounts', $accounts);
			
			$smarty->assign('new_account', $username);
			$smarty->display('added_account.tpl');
			exit();
		} else {
			die("Zugriff verweigert.");
		}
	}
?>
