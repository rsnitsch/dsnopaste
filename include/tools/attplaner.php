<?php
	/**
	 * Angriffsplaner
	 * 
	 * Zur Planung von Angriffen. Mehrere Start- und Zieldörfer sind möglich.
	 * Jeder Angriffsplaner kann a) nur zum Lesen oder b) zum Lesen und Bearbeiten
	 * per Link weitergegeben werden.
	 * 
	 * Angriffspläne werden nach 30 Tagen gelöscht (siehe cronjob in include-
	 * Verzeichnis). Angriffspläne ohne Aktionen werden jede Nacht gelöscht.
	 * 
	 * Unterstützt Massenbearbeitung (Verschiebung, Löschung von einzelnen
	 * Aktionen).
	 * 
	 * @todo: attplaner_addtargets.php
	 */
    define('INC_CHECK',true);
    include($root_path.'include/config.inc.php');
    
    // Output-Control anlegen
    $output = new nopSmarty();

    // den Header inkludieren
    $output->assign('title','Angriffsplaner');
    $output->assign('root_path',$root_path);
    $output->assign('subid', 'attplan');
    
    $output->assign('timestamp',time());
    
    // hinweis, da der attplaner auf alle server umgestellt wurde
    //$output->assign('w_hinweis', 'Wichtig: Der Angriffsplaner wurde vor kurzem (am 10.12.) stark erweitert! Wenn seitdem Fehler auftreten (zum Beispiel wegen falscher Laufzeiten) <a href="http://forum.die-staemme.de/showthread.php?t=33178" target="_blank">gebt mir bitte Bescheid</a>.');
    
    $errors=array();
    $debuginfo=array();
    
    // Keys zum Erstellen von Angriffsplanern per URL
    //  Es muss dann ein key-Parameter mitübergeben werden, der mit
    //  md5("<key>".strval(time() / 3600)) berechnet wird.
    $time_key = strval(floor(time() / 3600));
    $url_create_keys = array("twforums" =>
							 md5("FdxlWf0NO6dXjAaHQObyDtSxpcgdqTli8msqFuBcBmrIvEruCXPADx8kvBjjdb3".$time_key));
    
	error_reporting(E_ALL);
	
    // Inhalt
    if(!$cfg["enabled"])
    {
        $output->display('offline.tpl');
        exit;
    }
    else
    {
        $attid=-1;
        
        if(!empty($_GET['id']) and is_number($_GET['id']))
        {
            $attid=$_GET['id'];
        }
        else
        {
            // soll evtl. ein neuer Angriffsplan erstellt werden?
            if(!empty($_REQUEST['create']) and ($_POST['create']==1 or in_array($_GET['create'], $url_create_keys) !== false)
                and !empty($_REQUEST['server']) and serverExists($_REQUEST['server']))
            {
                enableMySQL(TRUE) or noSqlConnection(&$output);
                
                // verhindern, dass innerhalb von X Minuten mehr als Y Einträge erstellt werden
                $limit=5; // 5 Einträge
                $timelimit=3600; // 1 Stunde
                $currenttime=time();
                $sql_cmd='SELECT id FROM attplans WHERE ip="'.$mysql->escape($_SERVER['REMOTE_ADDR']).'" AND time>'.($currenttime-$timelimit).' LIMIT 5';
                $erg=$mysql->sql_query($sql_cmd);
                
                if(!($mysql->sql_num_rows($erg)>=$limit)) // wenn das Limit NICHT überschritten wird
                {
                    // den Angriffsplan in der Datenbank anlegen
                    $maxid=$mysql->sql_query('SELECT MAX(id) AS newid FROM attplans');
                    $attid=$mysql->sql_result($maxid,0,'newid') + 1;
                    $key=generatePassword(6);
                    $adminkey=generatePassword(6);
                    $server = $_REQUEST['server'];
                    
                    $sql_cmd='INSERT INTO attplans (id, `key`, adminkey, time, ip, server) VALUES ('.$attid.', "'.$key.'", "'.$adminkey.'", '.time().', "'.$_SERVER['REMOTE_ADDR'].'", "'.$mysql->escape($server).'")';
                    $erg=$mysql->sql_query($sql_cmd);
                    
                    if($erg)
                    {
                        // den Benutzer als Ersteller dieses Plans kennzeichnen (mit einem Cookie)
                        setcookie('admin_'.$key, $adminkey, time()+2592000);
                        
                        // den Benutzer weiterleiten zu dem erstellen Angriffsplan
                        header('Location: attplaner.php?id='.$attid.'&key='.$key.'&new=1');
                        
                        // dieses Skript kann jetzt abgebrochen werden, da der Benutzer sowieso weitergeleitet wird
                        exit;
                    }
                    else
                    {
                        $errors[]='Es ist ein Fehler aufgetreten (SQL-Fehler). Es konnte kein neuer Angriffsplan erstellt werden.';
                        if($cfg["debugmode"])
                        {
                            $debuginfo[]='SQL-Abfrage: '.$sql_cmd;
                            $debuginfo[]='SQL-Fehlermeldung: '.$mysql->lasterror;
                        }
                        
                        $output->assign('error',$errors);
                        $output->assign('debuginfo',$debuginfo);
                        $output->display('display_errors.tpl');
                        exit;
                    }
                }
                else
                {
                    $errors[]='<span style="color: #FF2121;">Sorry, aber es können nur 5 Angriffspläne pro Stunde erstellt werden!</span>';
                    $output->assign('error',$errors);
                    $output->display('display_errors.tpl');
                    exit;
                    
                    
                    //$attid=-1; (veraltet)
                }

            }
        }
        
        if($attid==-1)
        {
            $output->assign('servers', getServers());
            $output->display('attplaner_start.tpl');
        }
        else
        {
            // Angriff kann ausgegeben werden
            enableMySQL(TRUE) or noSqlConnection(&$output);
            $plandata    = $mysql->sql_query("SELECT * FROM attplans WHERE id='$attid'");

            // nur wenn der Angriff gefunden wird...
            if($mysql->sql_num_rows($plandata) == 1)
            {
                $key            = $mysql->sql_result($plandata, 0, 'key');
                $adminkey       = $mysql->sql_result($plandata, 0, 'adminkey');
                $notes          = $mysql->sql_result($plandata, 0, 'notes');
                $created        = $mysql->sql_result($plandata, 0, 'time');
                $serverid       = $mysql->sql_result($plandata, 0, 'server');
                $server         = new Gameworld($mysql->sql_result($plandata, 0, 'server'));
                
                $unitnames = $server->unitnames;
                
                // AngriffsID übergeben
                $output->assign('attid', $attid);
                $output->assign('attkey', $key);
				
				// wurde der Angriffsplan gerade erst erstellt?
				$output->assign('new_plan', isset($_GET['new']) && (intval($_GET['new']) == 1));
                
                // server
                $output->assign('server', $server);
                
                // ist der angegebene key korrekt?
                if(isset($_GET['key']) and $_GET['key']==$key)
                {
                    // einen Link zu diesem Angriffsplan anzeigen
                    $output->assign('link', 'http://np.bmaker.net/tools/attplaner.php?id='.$attid.'&amp;key='.$key);
                    
                    // ist der User der Ersteller bzw. zur Bearbeitung berechtigt?
                    $creator=(isset($_COOKIE['admin_'.$key]) and $_COOKIE['admin_'.$key]==$adminkey);
                    $output->assign('creator', $creator);

                    // einen Link anzeigen, mit dem man jemand anderes zum Admin machen kann
                    if($creator)
                        $output->assign('admin_link', 'http://np.bmaker.net/tools/attplaner.php?id='.$attid.'&amp;key='.$key.'&amp;admin_key='.$adminkey);
                    else
                        $output->assign('admin_link', '');
                        
                    // soll der Benutzer zum Mitersteller gemacht werden?
                    if(isset($_GET['admin_key']))
                    {
                        if($_GET['admin_key'] == $adminkey)
                        {
                            setcookie('admin_'.$key, $adminkey, time()+(2592000));
                            header('Location: attplaner.php?id='.$attid.'&key='.$key);
                            exit;
                        }
                    }
                    
                    // jetzt ausgeben wann der Angriffsplan gelöscht wird...
                    $output->assign('delete', '<br />Dieser Angriffsplan wird am '.date('j. M',($created+2592000)).' gelöscht.');
                    
                    // sollen irgendwelche Änderungen vorgenommen werden?
                    if((!empty($_POST['action']) and $_POST['action']=='save') or isset($_GET['deleteatt']))
                    {
                        checkEdit();
                        
                        // ist der Benutzer berechtigt, Änderungen durchzuführen?
                        if(isset($_COOKIE['admin_'.$key]) and $_COOKIE['admin_'.$key]==$adminkey)
                        {
                            // Angriff hinzufügen oder bearbeiten?
                            if(isset($_POST['add_attack']) or isset($_POST['edit_attack']))
                            {
                                $typ=null;
                                $to=null;
                                $from=null;
                                $arrive=null;
                                $note=null;
                                $runtime=null;
                                $senddate=null;
                                
                                if(check_AttEditOrSave()) // Alle Angaben sind gültig.
                                {
                                    // die Laufzeit und das Abschickdatum berechnen (damit sie später nicht bei jedem Aufruf neu berechnet werden muss)
                                    
                                    // veraltet
                                    /*$units=array(
                                        'spear' => $_POST['add_spear'],
                                        'sword' => $_POST['add_sword'],
                                        'axe' => $_POST['add_axe'],
                                        'spy' => $_POST['add_spy'],
                                        'light' => $_POST['add_light'],
                                        'heavy' => $_POST['add_heavy'],
                                        'ram' => $_POST['add_ram'],
                                        'catapult' => $_POST['add_catapult'],
                                        'snob' => $_POST['add_snob']
                                    );*/
                                    
                                    
                                    $units = false;
                                    foreach($unitnames as $unitname)
                                    {
                                        $units[$unitname] = $_POST['add_'.$unitname];
                                    }
                                    $timeperfield=getTimePerField($units, $server);
                                    
                                    // der paladin senkt die zeit/feld bei unterstützungsaufträgen auf 10 minuten
                                    if($typ == 2)
                                    {
                                        $paladin = isset($units['knight']) ? $mysql->escape($units['knight']) : 0;
                                        if($paladin > 0)
                                            $timeperfield = 600;
                                    }
                                    
                                    $runtime=calcRuntime($from, $to, $timeperfield);
                                    $senddate=$arrive-$runtime;
                                    
                                    // jetzt muss je nach dem ob bearbeitet oder hinzugefügt werden soll, eine SQL abfrage gebaut werden
                                    if(isset($_POST['add_attack']))
                                    {
                                        // Angriff/Unterstützung hinzufügen
                                        $sql_cmd='INSERT INTO attplans_actions (attplan_id, typ, `from`, `to`, runtime, senddate, arrive, note, spear, sword, axe, archer, spy, light, marcher, heavy, ram, catapult, knight, priest, snob)'
                                            .' VALUES ('.$attid.', '.$typ.', "'.$from.'", "'.$to.'", '.$runtime.', '.$senddate.', '.$arrive.', "'.$mysql->escape($_POST['note']).'", '
                                            .(isset($units['spear'])    ? $mysql->escape($units['spear'])    : 0).', '
                                            .(isset($units['sword'])    ? $mysql->escape($units['sword'])    : 0).', '
                                            .(isset($units['axe'])        ? $mysql->escape($units['axe'])        : 0).', '
                                            .(isset($units['archer'])    ? $mysql->escape($units['archer'])    : 0).', '
                                            .(isset($units['spy'])        ? $mysql->escape($units['spy'])        : 0).', '
                                            .(isset($units['light'])    ? $mysql->escape($units['light'])    : 0).', '
                                            .(isset($units['marcher'])    ? $mysql->escape($units['marcher'])    : 0).', '
                                            .(isset($units['heavy'])    ? $mysql->escape($units['heavy'])    : 0).', '
                                            .(isset($units['ram'])        ? $mysql->escape($units['ram'])        : 0).', '
                                            .(isset($units['catapult'])    ? $mysql->escape($units['catapult']): 0).', '
                                            .(isset($units['knight'])    ? $mysql->escape($units['knight'])    : 0).', '
                                            .(isset($units['priest'])    ? $mysql->escape($units['priest'])    : 0).', '
                                            .(isset($units['snob'])        ? $mysql->escape($units['snob'])    : 0).')';
                                    }
                                    else
                                    {
                                        // bearbeiten
                                        $sql_cmd='UPDATE attplans_actions SET typ='.$typ.', `from`="'.$from.'", `to`="'.$to.'", runtime='.$runtime.', senddate='.$senddate.', arrive='.$arrive.', note="'.$mysql->escape($_POST['note'])
                                        .'", spear='.(        isset($units['spear'])        ? $mysql->escape($units['spear'])        : 0)
                                        .', sword='.(        isset($units['sword'])        ? $mysql->escape($units['sword'])        : 0)
                                        .', axe='.(            isset($units['axe'])        ? $mysql->escape($units['axe'])            : 0)
                                        .', archer='.(        isset($units['archer'])        ? $mysql->escape($units['archer'])    : 0)
                                        .', spy='.(            isset($units['spy'])        ? $mysql->escape($units['spy'])            : 0)
                                        .', light='.(        isset($units['light'])        ? $mysql->escape($units['light'])        : 0)
                                        .', marcher='.(        isset($units['marcher'])    ? $mysql->escape($units['marcher'])        : 0)
                                        .', heavy='.(        isset($units['heavy'])        ? $mysql->escape($units['heavy'])        : 0)
                                        .', ram='.(            isset($units['ram'])        ? $mysql->escape($units['ram'])            : 0)
                                        .', catapult='.(    isset($units['catapult'])    ? $mysql->escape($units['catapult'])    : 0)
                                        .', knight='.(        isset($units['knight'])        ? $mysql->escape($units['knight'])        : 0)
                                        .', priest='.(        isset($units['priest'])        ? $mysql->escape($units['priest'])        : 0)
                                        .', snob='.(        isset($units['snob'])        ? $mysql->escape($units['snob'])        : 0)
                                        .' WHERE attplan_id='.$attid.' AND id='.$mysql->escape($_POST['oldid']);
                                    }
                                    
                                    // SQL Abfrage ausführen
                                    if(!$mysql->sql_query($sql_cmd))
                                    {
                                        $errors[]='Der Angriff bzw. die Untersttzung konnte nicht hinzugefgt/bearbeitet werden (SQL-Fehler).';
                                        if($cfg["debugmode"])
                                        {
                                            $debuginfo[]='SQL-Fehlermeldung: '.$mysql->lasterror;
                                        }
                                    }
                                    else
                                    {
                                        // alles hat geklappt und der Benutzer soll auf die Seite zurückgeleitet werden
                                        header('Location: attplaner.php?id='.$attid.'&key='.$key);
                                        exit;
                                    }
                                }
                                else
                                {
                                    $output->assign('error', $errors);
                                    $output->display('display_errors.tpl');
                                    exit;
                                }
                                
                                // sind Fehler aufgetreten?
                                if(count($errors) > 0)
                                {
                                    $output->assign('error', $errors);
                                    $output->assign('debuginfo', $debuginfo);
                                    $output->display('display_errors.tpl');
                                    exit;
                                }
                            }
                            
                            // einen Angriff löschen?
                            if(!empty($_GET['deleteatt']) and is_number($_GET['deleteatt']))
                            {
                                $sql_cmd='DELETE FROM attplans_actions WHERE id='.$mysql->escape($_GET['deleteatt']).' AND attplan_id='.$attid;
                                $mysql->sql_query($sql_cmd);
                                
                                // umleiten, damit der deleteatt-Parameter wieder verlorengeht
                                header('Location: attplaner.php?id='.$attid.'&key='.$key);
                                exit;
                            }
                            
                            // Notizen ändern?
                            if(isset($_POST['save_notes']))
                            {
                                if(strlen($_POST['notes']) < 25000)
                                {
                                    $notes=$_POST['notes'];
                                    
                                    $sql_cmd='UPDATE attplans SET notes="'.$mysql->escape($notes).'" WHERE id='.$attid;
                                    $mysql->sql_query($sql_cmd);
                                }
                                else
                                {
                                    $errors[]='Die Notizen konnten nicht abgespeichert werden, da sie zu lang sind (max. 25000 Zeichen).';
                                    $output->assign('error', $errors);
                                    $output->display('display_errors.tpl');
                                    exit;
                                }
                            }
                            
                            // massenbearbeitung von angriffen?
                            if(isset($_POST['mass_edit']) and !empty($_POST['mass_action']) and !empty($_POST['mass_edit_select']))
                            {
	                            // alle ausgewählten Aktionen ermitteln
	                            $matches = array();
	                            $selected = array();
	                            foreach($_POST as $name => $value)
	                            {
	                                if($value == '1')
	                                {
	                                    if(preg_match('/^select_([0-9]+)$/',$name,$matches))
	                                    {
	                                        $selected[] = intval($matches[1]);
	                                    }
	                                }
	                            }
	                            $output->assign('selected', $selected);
                            	
                                // ermitteln, was mit den ausgewählten berichten geschehen soll
                                $mass_action = $_POST['mass_action'];
                                $mass_select = $_POST['mass_edit_select'];
                                
                                // wenn keine gültige Auswahl-Option gewählt wurde standardmäßig all_marked erzwingen
                                if($mass_select != 'all' and $mass_select != 'all_marked' and $mass_select != 'all_notmarked')
                                    $mass_select = 'all_marked';
                                
                                if(count($selected) > 0 or $mass_select=='all')
                                {
                                    if($mass_action == 'addtime')
                                    {
                                        if((isset($_POST['secondsplus']) and is_numeric($_POST['secondsplus']))
                                            and (isset($_POST['minutesplus']) and is_numeric($_POST['minutesplus']))
                                            and (isset($_POST['hoursplus']) and is_numeric($_POST['hoursplus']))
                                            and (isset($_POST['daysplus']) and is_numeric($_POST['daysplus'])))
                                        {
                                            $time_to_add = intval($_POST['daysplus']*86400 + $_POST['hoursplus']*3600 + $_POST['minutesplus']*60 + $_POST['secondsplus']);
                                            
                                            $sql_cmd='';
                                            switch($mass_select)
                                            {
                                                case 'all_marked':
                                                    $sql_cmd='UPDATE attplans_actions SET arrive=(arrive + '.($time_to_add).'), senddate = (senddate + '.($time_to_add).') WHERE id IN ('.implode(',',$selected).') AND attplan_id='.$attid;
                                                    break;
                                                case 'all':
                                                    $sql_cmd='UPDATE attplans_actions SET arrive=(arrive + '.($time_to_add).'), senddate = (senddate + '.($time_to_add).') WHERE attplan_id='.$attid;
                                                    break;
                                                case 'all_notmarked':
                                                    $sql_cmd='UPDATE attplans_actions SET arrive=(arrive + '.($time_to_add).'), senddate = (senddate + '.($time_to_add).') WHERE id NOT IN ('.implode(',',$selected).') AND attplan_id='.$attid;
                                                    break;
                                            }
                                           	
                                            if(!$mysql->sql_query($sql_cmd))
                                            {
                                                $errors[]=('Die Angriffe konnten nicht verschoben werden (SQL-Fehler).');
                                                if($cfg["debugmode"])
                                                    $debuginfo[]='SQL-Fehlermeldung: '.$mysql->lasterror;
                                            }
                                        }
                                        else
                                        {
                                            $errors[]='Sorry, aber die Angriffe konnten nicht verschoben werden. Du musst einen numerischen Wert angeben!';
                                        }
                                    }
                                    elseif($mass_action == 'delete')
                                    {
                                        if(isset($_POST['mass_action_delete_sure']) and $_POST['mass_action_delete_sure']=='1')
                                        {
                                            $sql_cmd='';
                                            switch($mass_select)
                                            {
                                                case 'all_marked':
                                                    $sql_cmd='DELETE FROM attplans_actions WHERE id IN ('.implode(',',$selected).') AND attplan_id='.$attid;
                                                    break;
                                                case 'all':
                                                    $sql_cmd='DELETE FROM attplans_actions WHERE attplan_id='.$attid;
                                                    break;
                                                case 'all_notmarked':
                                                    $sql_cmd='DELETE FROM attplans_actions WHERE id NOT IN ('.implode(',',$selected).') AND attplan_id='.$attid;
                                                    break;
                                            }
                                            
                                            $mysql->sql_query($sql_cmd);
                                            
                                            // umleiten, damit der deleteatt-Parameter wieder verlorengeht
                                            header('Location: attplaner.php?id='.$attid.'&key='.$key);
                                            exit;
                                        }
                                        else
                                            $errors[] = 'Um mehrere Berichte auf einmal löschen zu können, musst du die "sicher!"-Checkbox aktivieren!';
                                    }
                                }
                                else
                                {
                                    $errors[] = 'Du hast keine Aktionen ausgewählt, die bearbeitet werden sollen!';
                                }
                            }
                            else
                            {
                            	$output->assign('selected', array());
                            }
                            
                            if(count($errors) > 0)
                            {
                                $output->assign('error', $errors);
                                $output->assign('debuginfo', $debuginfo);
                                $output->display('display_errors.tpl');
                                exit;
                            }
                            
                            /*
                            // alle Angriffe um X Minuten verschieben?
                            if(isset($_POST['add_time']))
                            {
                                if((isset($_POST['minutesplus']) and is_numeric($_POST['minutesplus']))
                                    and (isset($_POST['hoursplus']) and is_numeric($_POST['hoursplus']))
                                    and (isset($_POST['daysplus']) and is_numeric($_POST['daysplus'])))
                                {
                                    $time_to_add = $_POST['daysplus']*86400 + $_POST['hoursplus']*3600 + $_POST['minutesplus']*60;
                                    $sql_cmd='UPDATE attplans_actions SET arrive=(arrive + '.($time_to_add).'), senddate = (senddate + '.($time_to_add).') WHERE attplan_id='.$attid;
                                    if(!$mysql->sql_query($sql_cmd))
                                    {
                                        $errors[]=('Die Angriffe konnten nicht verschoben werden (SQL-Fehler).');
                                        if($cfg["debugmode"])
                                            $debuginfo[]='SQL-Fehlermeldung: '.$mysql->lasterror;
                                    }
                                }
                                else
                                {
                                    $errors[]='Sorry, aber die Angriffe konnten nicht verschoben werden. Du musst einen numerischen Wert größer 0 angeben!';
                                }
                                
                                if(count($errors) > 0)
                                {
                                    $output->assign('error', $errors);
                                    $output->display('display_errors.tpl');
                                    exit;
                                }
                            }
                            */
                        }
                        else
                        {
                            $output->assign('noadmin', TRUE); // zeigt eine Warnung an
                        }
                    } // Ende des "Änderungen"-Blocks (hier werden Änderungen an dem Angriffsplan vorgenommen)
                    
                    
                    // alle bisherigen Angriffe/Unterstützungen ausgeben
                    // Sortierung der Angriffe/Unterstützungen beachten
                    $order='';
                    $allowed_orders=array('from', 'to', 'typ', 'arrive', 'senddate');
                    if(isset($_GET['order']))
                    {
                        if(array_search($_GET['order'], $allowed_orders))
                        {
                            setcookie('order_'.$key, $_GET['order'], time()+86400);
                            
                            switch($_GET['order'])
                            {
                                case 'from':
                                    $order='`from` DESC';
                                    break;
                                case 'to':
                                    $order='`to` DESC';
                                    break;
                                default:
                                    // für alle anderen möglichen Sortierungen ist keine spezielle Formatierung notwendig
                                    $order=$_GET['order'];
                            }
                        }
                    }
                    elseif(isset($_COOKIE['order_'.$key]))
                    {
                        $cookie_order = $_COOKIE['order_'.$key];
                        
                        // aus dem order-cookie auslesen
                        if(array_search($cookie_order, $allowed_orders))
                        {
                            switch($cookie_order)
                            {
                                case 'from':
                                    $order='`from` DESC';
                                    break;
                                case 'to':
                                    $order='`to` DESC';
                                    break;
                                default:
                                    // fr alle anderen mï¿½lichen Sortierungen ist keine spezielle Formatierung notwendig
                                    $order=$cookie_order;
                            }
                        }
                    }

                    // wenn $order noch nicht gesetzt wurde, dann muss der standard verwendet werden
                    if($order == '')
                        $order='arrive';
                        
                    $actions=$mysql->sql_query('SELECT * FROM attplans_actions WHERE attplan_id='.$attid.' ORDER BY '.$order);
                    if(!$actions)
                    {
                        if($cfg["debugmode"])
                            $debuginfo[]='Es ist ein SQL-Fehler aufgetreten. SQL-Fehlermeldung: '.$mysql->lasterror;
                    }
                    
                    // Alle Aktionen ausgeben
                    $actions_array=array();
                    while($row=$mysql->sql_fetch_assoc($actions))
                    {
                        $row['arrive_pure']=date('d.m.Y H:i:s',$row['arrive']);
                        $row['arrive']=date('j. M H:i:s',$row['arrive']);
                        
                        $tleft=$row['senddate']-time();
                        $timeleft='';
                        if($tleft > 0)
                        {
                            $h_left = intval($tleft / 3600);
                            $m_left = intval(($tleft - ($h_left*3600)) / 60);
                            $timeleft = "{$h_left}h / {$m_left}m";
                        }

                        $actions_array[]=array_merge($row, array('send'=>date('j. M H:i:s',$row['senddate']), 'timeleft' => $timeleft));
                    }
                    
                    // das generierte Array mit allen Aktionen an Smarty weitergeben
                    $output->assign('actions', $actions_array);
                    
                    // das Formular zum Hinzufügen neuer Aktionen
                    if($attid>=0)
                    {
                        $output->assign('currentdate',date('d.m.Y H:i:s', time()+3600));
                        
                        // das Formular zum editieren oder hinzufügen? (normalerweise hinzufügen)
                        $planform_std=FALSE; // diese Variable gibt an, ob das Standardformular zum HINZUFÜGEN oder das Formular zum EDITIEREN eines Angriffs/Unterstützung angezeigt wird
                        
                        // das Units Array
                        $units = false;
                        foreach($unitnames as $unitname)
                        {
                            $units[$unitname]=0;
                        }
                        
                        if(isset($_GET['edit']) and is_number($_GET['edit']))
                        {
                            // EIN ANGRIFF bzw. EINE UNTERSTÜTZUNG SOLLEN EDITIERT WERDEN!!!
                            
                            $sql_cmd='SELECT * FROM attplans_actions WHERE attplan_id='.$attid.' AND id='.$mysql->escape($_GET['edit']);
                            $attdata=$mysql->sql_query($sql_cmd);
                            
                            if($mysql->sql_num_rows($attdata)==1)
                            {
                                // DATEN DES ANGRIFFS BZW. DER UNTERSTÜTZUNG
                                $output->assign('arrival',date('d.m.Y H:i:s', $mysql->sql_result($attdata, 0, 'arrive')));
                                $output->assign('from',        $mysql->sql_result($attdata, 0, 'from'));
                                $output->assign('to',        $mysql->sql_result($attdata, 0, 'to'));
                                $output->assign('note',        $mysql->sql_result($attdata, 0, 'note'));
                                // welcher Typ ausgewählt ist
                                $typ=$mysql->sql_result($attdata, 0, 'typ');
                                $output->assign('typ', $typ);
                                // alle Einheiten
                                // die Funktion getNumber() erzwingt die Ausgabe einer Zahl! (würde man sie nicht verwenden, würde $mysql->sql_result leer, wenn die Einheit nur 0x vorkommt!!!)
                                foreach($unitnames as $unitname)
                                {
                                    $units[$unitname] = getNumber($mysql->sql_result($attdata, 0, $unitname));
                                }
                                $output->assign('units', $units);
                                // - ENDE DATEN...
                                
                                // die Standardaktion des Formulars:
                                $output->assign('action_attack','edit_attack');
                                $output->assign('add_or_edit', 'bearbeiten');
                                
                                // die alte ID der AKTION
                                $output->assign('oldid', $_GET['edit']);
                            }
                            else
                            {
                                $planform_std=TRUE;
                            }
                        }
                        else
                        {
                            $planform_std=TRUE;
                        }
                        
                        if($planform_std)
                        {
                            // ein Angriff bzw. eine Unterstützung sollen hinzugefügt werden (standard)
                            
                            // Herausfinden, welche Daten die letzte hinzugefügte Aktion aufweist
                            $query = $mysql->sql_query('SELECT arrive, `from`, `to` FROM attplans_actions WHERE attplan_id='.$attid.' ORDER BY id DESC LIMIT 1');
                            $last_action = ($mysql->sql_num_rows($query)!=0) ? $mysql->sql_fetch_assoc($query) : null;
                            
                            
                            // DATEN DES ANGRIFFS BZW. DER UNTERSTÜTZUNG
                            $output->assign('note', '');
                            
                            // welches koordinaten system?
                            if($last_action == null) {
                            	$output->assign('arrival',date('d.m.Y H:i', time()+3600).':00');
                            	
                                if($server->coordSystem() == 'modern')
                                {
                                    $output->assign('from','500|500');
                                    $output->assign('to','501|501');
                                }
                                else
                                {
                                    $output->assign('from','0:0:0');
                                    $output->assign('to','0:0:1');
                                }
                            } else {
                            	$output->assign('arrival',date('d.m.Y H:i:s', $last_action['arrive']));
                            	
                                $output->assign('from', $last_action['from']);
                                $output->assign('to', $last_action['to']);
                            }
                            
                            $output->assign('typ1',''); // welcher Typ ausgewählt ist
                            $output->assign('typ2',''); // welcher Typ ausgewählt ist
                            // alle Einheiten
                            $output->assign('units', $units);

                            // - ENDE DATEN...
                            
                            // die Standardaktion des Formulars:
                            $output->assign('action_attack','add_attack');
                            $output->assign('add_or_edit', 'hinzufügen');
                        }
                        
                        // Notizen
                        $output->assign('notes', $notes);
                        
                        
                        // den Planer darstellen
                        $output->assign('unitnames', $unitnames);
                        $output->display('attplaner_plan.tpl');
                    }
                }
                else
                {
                    $errors[]='Zugriff verweigert! Falscher Key!';
                    $output->assign('error', $errors);
                    $output->display('display_errors.tpl');
                    exit;
                }
            }
            else
            {
                $errors[]='Dieser Angriffsplan ist nicht (mehr) vorhanden.';
                $output->assign('error', $errors);
                $output->display('display_errors.tpl');
                exit;
            }
        }
    }
    

    // berprft generelle Daten
    function checkEdit()
    {
        global $creator, $errors, $debuginfo;
        
        if(!$creator)
        {
            $errors[]='Du hast diesen Angriffsplan nicht erstellt. Du bist nicht berechtigt Ã„nderungen durchzufhren.';
            return FALSE;
        }
    }
    
    // prÃ¼ft alle Daten, die zum Bearbeiten oder Hinzufgen eines Angriffes nÃ¶tig sind
    function check_AttEditOrSave()
    {
        global $errors;
        global $unitnames;
        global $typ, $from, $to, $arrive;
        
        // Fehlende Unit-Angaben ggf. ergänzen
		foreach($unitnames as $unitname) {
	        $key_name = 'add_'.$unitname;
	
			if(empty($_POST[$key_name]))
				$_POST[$key_name] = "0";
		}
        
        // Überprfen ob alle Parameter gesetzt wurden
        if(!empty($_POST['typ']) and !empty($_POST['from']) and !empty($_POST['to']) and !empty($_POST['arrival']))
        {
            $typ=0;
            switch ($_POST['typ'])
            {
                case 'att':
                    $typ=1;
                    break;
                case 'deff':
                    $typ=2;
                    break;
                case 'fake':
                    $typ=3;
                    break;
                case 'snob':
                    $typ=4;
                    break;
                default:
                    $typ=1;
                    $errors[]='Ungültiger Typ!';
            }
            
            // Absendekoordinate
            if(validCoord($_POST['from']))
                $from = cleanCoord($_POST['from']);
            else
                $errors[]='Ungültige Absendekoordinate!';
            
            // Zielkoordinate
            if(validCoord($_POST['to']))
                $to = cleanCoord($_POST['to']);
            else
                $errors[]='Ungültige Zielkoordinate!';
                
            // Absendekoordinate darf nicht gleich der Zielkoordinate sein!
            if($from==$to)
                $errors[]='Absendekoordinate und Zielkoordinate dürfen nicht identisch sein!';
            
            // Ankunftsdatum
            $arrive=0;
            if(preg_match('/([0-9]{2})\.([0-9]{2})\.([0-9]{4})\s+([0-9]{2}):([0-9]{2}):([0-9]{2})/',$_POST['arrival'],$arrive_matches))
            {
                // int mktime ( [int Stunde [, int Minute [, int Sekunde [, int Monat [, int Tag [, int Jahr [, int is_dst]]]]]]] )
                $arrive=mktime($arrive_matches[4],$arrive_matches[5],$arrive_matches[6],$arrive_matches[2],$arrive_matches[1],$arrive_matches[3]);
            }
            else
            {
                $errors[]="Ungültiges Ankunftsdatum!<br />Beachte, dass du die Ankunftszeit in genau diesem Format angeben musst: tt.mm.jjjj hh:mm:ss<br />Auch die Uhrzeit muss aus zweistelligen Zahlen bestehen!";
            }
            
            // Notiz
            if(isset($_POST['note']))
            {
                $len = strlen($_POST['note']);
                if($len > 50)
                    $errors[]='Notizen für einzelne Aktionen dürfen nicht länger als 50 Zeichen sein.';
            }
            
            
            // alle Einheiten zusammen müssen eine Anzahl größer 0 ergeben
            $all_units = 0;
            $key_name = '';
            foreach($unitnames as $unitname)
            {
                $key_name = 'add_'.$unitname;
                
                if(is_number($_POST[$key_name]))
                {
                    if($_POST[$key_name] >= 0)
                    {
                        $all_units += $_POST[$key_name];
                    }
                    else
                    {
                        $errors[]='Die Einheitenanzahlen dürfen nicht kleiner 0 sein!';
                        break;
                    }
                }
                else
                {
                    $errors[]='Einheitenanzahlen müssen numerisch angegeben werden!';
                    break;
                }
            }
            if($all_units <= 0)
                $errors[]='Die Einheitensumme muss größer 0 sein!';
            
            
            if(count($errors)>0)
                return FALSE;
        }
        else
        {
            $errors[]='Nicht alle Parameter wurden übertragen!';
            return FALSE;
        }
        
        // wenn die Funktion bis hierhin kommt, ist alles gut gelaufen
        return TRUE;
    }
    
    

    // ###################################################
    // ###################################################
    // ################### FUNKTIONEN ####################
    // ###################################################
    // ###################################################

    // dies ist nur eine Hilfsfunktion
    function getNumber($number)
    {
        $number=(string) $number;
        if(strlen($number)==0)
            $number='0';
        return $number;
    }
?>