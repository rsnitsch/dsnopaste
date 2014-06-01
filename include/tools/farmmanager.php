<?php
    /**
     * Farmmanager
     * 
     * Ein Tool, das (Späh)berichte entgegennimmt und die darin angegriffenen
     * Dörfer als Farmen abspeichert. Bei jedem Aufruf des Tools werden
     * diese Farmen mit ihren aktuellen Ressourcen angezeigt. Das Tool berechnet
     * nämlich, welche Ressourcen seit dem Angriff hinzugekommen sind (anhand der
     * Minenstufen). Ebenfalls berücksichtigt werden Speicher und Versteck.
     * 
     * Setzt wenigstens das Mitsenden von Spähern voraus!
     * 
     * WICHTIG:
     * @todo: Auch Berichte ohne Späher einlesen können (Farmbericht/Spähbericht einlesen ist zweideutig)

     * NORMALE TODOS:
     * @todo: Wenn das Herkunftsdorf nicht dem Eigentümer des Farmmanagers gehört,
     *        dann soll das Herkunftsdorf nicht abgespeichert werden
     * @todo: Optimierung (Caching, ggf. Sortierungen komplett clientseitig umsetzen)
     *        + Im Falle von Einlesen per Skript nicht mehr die ganze Seite generieren
     *
     * NICE-TO-HAVE:
     * @todo: gesamtproduktion anzeigen
     * @todo: farmmanager cachen!
     * 
     * ERLEDIGT:
     * @todo: bessere Icons für das als gefarmt markieren, löschen etc.
     * @todo: Notizen verändern können auch ohne Einlesen
     * @todo: Auch nach prozentualem Füllstand des Speichers sortieren können
     * @todo: Auch nach speziellen Ressourcen sortieren können
     * @todo: Herkunftsdörfer der Farmen abspeichern.
     *        Einen Filter einbauen, sodass ggf. nur noch Farmen bestimmter
     *        Herkunftsdörfer angezeigt werden.
     * @todo: Dörfer nach Koordinaten sortieren
     * @todo: Sortierung nach Entfernung zum gerade gewählten Herkunftsdorf
     * @todo: Checkboxen für die unterschiedlichen Rohstoffboni hinzufügen
     * @todo: farmmanager regelmäßig mit cronjob aufräumen
     * @todo: Link zum Versammlungsplatz für direktes Truppen schicken (erfordert wohl Einbau von Weltdaten)
     * @todo: Ressourcen, die während der Laufzeit dazukommen, ebenfalls berechnen
     */
    define('INC_CHECK',true);
    define('INC_CHECK_DSBERICHT', true);
    include($root_path.'include/config.inc.php');
    require_once($root_path.'include/class.dsBericht.php');
    require_once($cfg["twdata_include"]);
    
    $smarty = new nopSmarty();
    
    $smarty->assign('title','Farmmanager');
    $smarty->assign('root_path',$root_path);
    $smarty->assign('subid', 'farmmgr');
    //$smarty->assign('global_announcing', 'Ich brauche erstmal keine weiteren Berichte mehr, danke!');
    
    $errors = array();
    $debugs = array();
    
    // ist die Seite aktiviert
    if(!$cfg["enabled"]) {
        $smarty->display('offline.tpl');
        exit();
    }
    
    // array der aktivierten Server
    $activated_servers = getServers();
    
    // array der möglichen sortierungen
    $avail_orders = array(  'lastreport',
                            'v_coords',
                            'c_sum',
                            'c_wood',
                            'c_loam',
                            'c_iron',
                            'fill_level',
                            'light',
                            'spear',
                            'storage',
                            'distance');
    
    $smarty->assign('activated_servers', $activated_servers);
    
    // kleine Hilfsfunktionen
    function _displayErrors() {
        global $smarty, $errors, $debugs;
        header('Status: 400 Bad Request');
        
        if(!_isAjaxRequest())
            displayErrors($smarty, $errors, $debugs);
        else {
            echo "Es ist ein Fehler aufgetreten:\n";
            foreach($errors as $error) {
                echo "  - ".htmlspecialchars($error)."\n";
            }
            exit();
        }
    }
    
    function _displaySQLError() {
        global $smarty;
        header('Status: 500 Internal Server Error');
        
        if(!_isAjaxRequest())
            displaySQLError($smarty);
        else
            die("SQL-Fehler!");
    }
    
    // holt die Farmen mit der ID $saveid aus der Datenbank
    function _getFarms($saveid) {
        global $mysql;
        global $source_village, $filter_source_village;
        global $oServer;
        
        $sql_filter_source_village = (!$source_village || !$filter_source_village) ? '' : "AND av_coords='".$mysql->escape($source_village)."'";
        
        $sql = 'SELECT *,'.
                'farmable*3 AS storage_max '.
                'FROM farms WHERE saveid="'.$mysql->escape($saveid).'" '.$sql_filter_source_village.
                ' ORDER BY farmed ASC,time ASC';
        $res = $mysql->sql_query($sql);
        
        if(!$res) {
            _displaySQLError();
        }
        
        $farms = array();
        while($row = $mysql->fetch($res)) {
            $farms[] = $row;
        }
        
        return $farms;
    }
    
    function _redirect($exit=true) {
        global $saveid;
        header("Location: farmmanager.php?id=".$saveid);
        if($exit)
            exit();
    }
    
    function _isAjaxRequest() {
        if (!isset($_POST['ajax'])) {
            return false;
        }

        $a = intval($_POST['ajax']);
        if ($a > 0) {
            return $a;
        }

        return true;
    }

    function calculateExpectedResources($farm, $time, Gameworld $server) {
        $hours_gone = ($time - $farm['time']) / 3600.0;
        $prod = $server->calcTotalMineProduction($farm['b_wood'], $farm['b_loam'], $farm['b_iron'], $farm['bonus'], $hours_gone);
        list($wood, $loam, $iron) = array_values($prod);
        return array(
            "wood" => round(min($farm['farmable'], $farm['wood'] + $wood)),
            "loam" => round(min($farm['farmable'], $farm['loam'] + $loam)),
            "iron" => round(min($farm['farmable'], $farm['iron'] + $iron))
        );
    }

    // neuen Farmmanager erstellen?
    if(isset($_GET['action']) and $_GET['action']=='create' and !empty($_POST['server']) and serverExists($_POST['server'])) {
        $saveid = generatePassword(10);
        $server = $_POST['server'];
        
        $is_activated_server = false;
        foreach($activated_servers as $act_server) {
            if($act_server['id'] == $server) {
                $is_activated_server = true;
                break;
            }
        }
        if(!$is_activated_server) {
            $errors[] = "Der Farmmanager ist für diese Welt nicht aktiviert!";
            _displayErrors();
        }
        
        enableMySQL(true);
        
        // darf der Besucher noch einen Farmmanger erstellen (1 pro Tag / Welt)?
        $limit=5; // 5 Einträge
        $timelimit=1800; // 30 Minuten
        $sql = "SELECT id FROM farmmanagers WHERE " .
                "ip='".$mysql->escape($_SERVER['REMOTE_ADDR'])."' AND " .
                "server='".$mysql->escape($server)."'" .
                "AND time>".(time()-$timelimit)." LIMIT 1";
        $res = $mysql->sql_query($sql);
        if(!$res)
            _displaySQLError();
        if($mysql->sql_num_rows($res)!=0) {
            $errors[] = "Du darfst pro Welt nur einen Farmmanager erstellen! Mit deiner IP-Addresse wurde aber bereits ein Farmmanager auf dieser Welt erstellt! Probiere es in spätestens 30 Minuten nochmal.";
            //$errors[] = 'Dein Farmmanager ist: <a href="farmmanager.php?id='.$mysql->sql_result($res,0,'id').'">farmmanager.php?id='.$mysql->sql_result($res,0,'id').'</a>';
            _displayErrors();
        }
        
        // Farmmanager erstellen und weiterleiten
        $sql = "INSERT INTO farmmanagers " .
                "(id, server, ip, time) VALUES" .
                "('$saveid', '".$mysql->escape($server)."', '".$mysql->escape($_SERVER['REMOTE_ADDR'])."', '".time()."')";
        if(!$mysql->sql_query($sql)) {
            _displaySQLError();
        }
        
        // weiterleiten ($saveid wurde ja bereits oben generiert)
        _redirect();
    }
    
    // startseite
    if(empty($_GET['id']) or !ctype_alnum($_GET['id']) or strlen($_GET['id']) != 10) {
        $smarty->display('farmmanager_start.tpl');
        exit();
    }
    

    /* ++++++++++++++++++++++ */
    // der Farmmanager selbst (nicht die Startseite)
    
    enableMySQL(true) or _displaySQLError();
    $saveid = $_GET['id']; // bereits validiert von obigem if-Block
    $smarty->assign('saveid', $saveid);
    
    $twd = null;
    try {
        $twd = TWData::get_db_connection();
    } catch(Exception $exc) {
        _displaySQLError();
    }
    
    // gibt es diesen Farmmanager überhaupt?
    $res = $mysql->sql_query("SELECT time,ip,server FROM farmmanagers WHERE id='".$mysql->escape($saveid)."' LIMIT 1");
    if($mysql->sql_num_rows($res) != 1) {
        $errors[] = "Farmmanager nicht gefunden. Möglicherweise wurde er wegen Nichtnutzung gelöscht.";
        $debugs[] = "Oder SQL-Fehler: ".$mysql->lasterror;
        _displayErrors();
    }
    
    $fm = $mysql->fetch($res);
    list($creation_time,$creation_ip,$server) = array_values($fm);
    
    // das sollte niemals passieren
    if(!isValidServerID($server)) {
        $errors[] = "Internal error: Invalid server id";
        _displayErrors();
    }
    
    $smarty->assign('world_id', $server);
    
    // Das Gameworld-Objekt
    $oServer = Gameworld::forServerID($server);
    $smarty->assign('server', $oServer);
    $smarty->assign('title', $smarty->getTemplateVars('title')." (".$oServer->name.")");
    $server_cfg = $oServer->getConfig();
    
    // Welche Bonusdörfer kann es auf diesem Server geben?
    $bonus_new = $oServer->bonusNew();
    $possible_boni=$oServer->bonusesPossible();
    $smarty->assign('bonus_new', $bonus_new);
    
    // Herkunftsdörfer auslesen
    $att_villages = array();
    $res = $mysql->sql_query("SELECT av_name, av_coords FROM farms WHERE saveid='".$mysql->escape($saveid)."' GROUP BY av_coords ORDER BY av_name");
    if(!$res)
        _displaySQLError();
    while($av = $mysql->fetch($res)) {
        if(!empty($av['av_name']) && !empty($av['av_coords']))
            $att_villages[] = $av;
    }
    $smarty->assign('att_villages', $att_villages);
    
    // Sortierung
    $order = (!empty($_COOKIE["order_$saveid"]) && in_array($_COOKIE["order_$saveid"], $avail_orders)) ? $_COOKIE["order_$saveid"] : 'lastreport';
    if(!empty($_GET["order"])) {
        $neworder = $_GET["order"];
        if(in_array($neworder, $avail_orders)) {
            // ordnung setzen
            $order = $neworder;
            
            _redirect(false);
            
            // cookie neu setzen
            setcookie("order_$saveid", $neworder, time()+86400*30, '', $_SERVER['HTTP_HOST']);
        }
    }
    
    // Herkunftsdorf-Auswahl und Filter
    $source_village = null;
    $filter_source_village = false;
    $filter_min_ress = 0;
    if(!empty($_COOKIE["filter_$saveid"])) {
        $tmp = $_COOKIE["filter_$saveid"];
        $parts = explode(",", $tmp);
        
        if(count($parts) == 3) {
            $source_village = inArrayColumn($parts[0], $att_villages, "av_coords") ? $parts[0] : null;
            $filter_source_village = intval($parts[1]);
            $filter_min_ress = intval($parts[2]);
        }
    }
    if(!empty($_POST["source_village"])) {
        $source_village = inArrayColumn($_POST["source_village"], $att_villages, "av_coords") ? $_POST["source_village"] : null;
        $filter_source_village = intval(!empty($_POST["filter_source_village"]) && $_POST["filter_source_village"] == "yes");
        $filter_min_ress = !empty($_POST["filter_min_ress"]) ? intval($_POST["filter_min_ress"]) : 0;
        
        _redirect(false);
        
        // cookie neu setzen
        setcookie("filter_$saveid", "$source_village,$filter_source_village,$filter_min_ress", time()+86400*30, '', $_SERVER['HTTP_HOST']);
        exit();
    }
    
    //var_dump($_COOKIE["filter_$saveid"]);
    
    $smarty->assign('source_village', $source_village);
    $smarty->assign('filter_source_village', $filter_source_village);
    $smarty->assign('filter_min_ress', $filter_min_ress);
    if($source_village) {
        list($av_x, $av_y) = explode("|", $source_village);
        $result = $twd->query("SELECT id FROM {$server}_village".
                              " WHERE x=".$twd->quote($av_x).
                              " AND y=".$twd->quote($av_y)." LIMIT 1")->fetch();
        
        if(!$result) {
            trigger_error("Invalid source village.");
        }
        else {
            $smarty->assign('source_village_id', $result['id']);
        }
    }
    
    // eine Farm einlesen?
    if(!empty($_POST) && !empty($_POST['parse'])) {
        if(empty($_POST['report'])) {
            $errors[] = 'Du musst einen Bericht angeben!';
            _displayErrors();
        }
        
        $report = $_POST['report'];
        if (_isAjaxRequest() >= 2) {
            // Since AJAX version 2 the report data is extra-encoded.
            $report = urldecode($report);
        }
        $matches = array();
        $data = array();
        
        if($cfg["debugmode"] && is_writable("/tmp/")) {
            $fh = fopen("/tmp/report.txt", "wb");
            fwrite($fh, $report);
            fclose($fh);
        }
        
        if(strlen($_POST['note']) > 100) {
            $errors[] = "Notizen dürfen höchstens 100 Zeichen lang sein!";
            _displayErrors();
        }
        
        // daten für die SQL abfrage zusammenstellen
        $data['saveid'] = $saveid;
        $data['farmed'] = 0; // die Farm nicht mehr als gefarmt markieren
        
        // Nur wenn eine Notiz angegeben wurde, soll diese gespeichert werden.
        // (Dadurch werden bestehende Notizen nicht gelöscht, wenn man das Feld frei
        // lässt.)
        if(!empty($_POST['note']))
            $data['note'] = $_POST['note'];
        
        // die Ressourcen, die gespäht wurden
        $spied_resources = array();
        $wood = (!empty($_POST['wood']) && $_POST['wood'] == 'yes');
        $loam = (!empty($_POST['loam']) && $_POST['loam'] == 'yes');
        $iron = (!empty($_POST['iron']) && $_POST['iron'] == 'yes');
        if ($wood) $spied_resources[] = 'wood';
        if ($loam) $spied_resources[] = 'loam';
        if ($iron) $spied_resources[] = 'iron';
        
        $units = array(
            "attacker" => $oServer->getUnitNames(true),
            "defender" => $oServer->getUnitNames(false)
        );
        $dsBericht = new dsBericht($units, $spied_resources);
		try {
			$dsBericht->parse($report);
		} catch(RuntimeException $exc) {
			$msg = $exc->getMessage();
			if (strpos($msg, "Number of") === 0) {
				$errors[] = "Die Anzahl der Einheitentypen in dem Bericht scheint nicht übereinzustimmen mit der Anzahl für die ausgewählte Welt (".htmlspecialchars($oServer->id).").";
			}
			$errors[] = "Fehler: ".htmlspecialchars($msg);
			_displayErrors();
		}
        $parsed = $dsBericht->getReport();
        
        // Dringend benötigte Teile des Berichts checken
        if ($parsed['time'] === false) {
            $errors[] = "Gesendet-Teil konnte nicht eingelesen werden (kopiere den gesamten Bericht!).";
        }
        if ($parsed['spied_resources'] === false) {
            $errors[] = "Erspähte Ressourcen konnten nicht eingelesen werden.";
        }
        if ($parsed['attacker'] === false) {
            $errors[] = "Angreifer-Informationen konnten nicht eingelesen werden.";
        }
        if ($parsed['defender'] === false) {
            $errors[] = "Verteidiger-Informationen konnten nicht eingelesen werden.";
        }
        if ($parsed['buildings'] === false) {
            $errors[] = "Die Gebäude-Informationen konnten nicht eingelesen werden.";
        }
        if ($parsed['buildings'] && $parsed['buildings']['storage'] == 0) {
            $errors[] = "Die Speicher-Stufe konnte nicht eingelesen werden.";
        }
        
        // Bei Fehlern abbrechen
        if (count($errors) > 0) {
            if(!_isAjaxRequest())
                array_unshift($errors, 'Ungültiger Bericht oder falscher Ausdruck (Details folgen). Denke daran, den Bericht komplett zu kopieren! Oder hast du vielleicht vergessen, einen Späher mitzuschicken?');
            _displayErrors();
        }
        
        // Geparste Daten übernehmen, soweit relevant
        $data['time'] = $parsed['time'];
        $data['wood'] = $parsed['spied_resources']['wood'];
        $data['loam'] = $parsed['spied_resources']['loam'];
        $data['iron'] = $parsed['spied_resources']['iron'];
        $data['av_name'] = $parsed['attacker']['village'];
        $data['av_coords'] = $parsed['attacker']['coords'];
        $data['v_name'] = $parsed['defender']['village'];
        $data['v_coords'] = $parsed['defender']['coords'];
        $data['b_wood'] = $parsed['buildings']['wood'];
        $data['b_loam'] = $parsed['buildings']['loam'];
        $data['b_iron'] = $parsed['buildings']['iron'];
        $data['b_wall'] = $parsed['buildings']['wall'];
        $data['b_storage'] = $parsed['buildings']['storage']; // TODO: Die Speicher-Stufe konnte nicht eingelesen werden!
        $data['b_hide'] = $parsed['buildings']['hide'];
        
        // Wurde ein Bonus angegeben?
        if(!empty($_POST['bonus'])) {
            if(in_array($_POST['bonus'], $possible_boni))
                $data['bonus'] = $_POST['bonus'];
        }
        
        // Alte Daten der Farm abrufen, wenn die Farm schon mal früher eingelesen wurde
        $res = $mysql->sql_query("SELECT * FROM farms WHERE v_coords='".$mysql->escape($data['v_coords'])."' AND saveid='$saveid'");
        if ($mysql->sql_num_rows($res) == 0) {
            $data['bonus'] = 'none';
            $farm_old = false;
        } else {
            $farm_old = $mysql->sql_fetch_assoc($res);
        }
        
        // Hat das Dorf den Speicher-Bonus?
        if(!empty($data['bonus']) && $data['bonus'] == 'storage') {
            // JA, weil explizit im Formular angegeben
            $storage_bonus = true;
        }
        elseif(empty($data['bonus'])) {
            $old_bonus = $farm_old ? $farm_old['bonus'] : '';
            
            // JA, weil dieser Bonus bereits eingetragen war und beibehalten werden soll
            $storage_bonus = ($old_bonus == 'storage');
        }
        else {
            // NEIN, keiner der obigen Fälle hat zugetroffen
            $storage_bonus = false;
        }
        
        if($storage_bonus) {
            $data['farmable'] = intval($oServer->calcStorageMax($data['b_storage'])*1.5 - $oServer->hideMax($data['b_hide']));
        }
        else {
            $data['farmable'] = intval($oServer->calcStorageMax($data['b_storage'])     - $oServer->hideMax($data['b_hide']));
        }
        
        // SQL bilden,
        // je nachdem ob die Farm bereits bekannt war (UPDATE) oder nicht (INSERT)
        if(!$farm_old) {
            // zuerst überprüfen, ob das limit überschritten wird
            $res = $mysql->sql_query("SELECT COUNT(*) AS count FROM farms WHERE saveid='".$mysql->escape($saveid)."'");
            if((!$res) or $mysql->sql_result($res, 0, 'count') >= 1000) {
                $errors[] = "Sorry, du kannst höchstens 1000 Farmen in einem Farmmanager verwalten!";
                _displayErrors();
            }
            
            // die ID des Dorfes hinzufügen
            list($v_x, $v_y) = explode("|", $data['v_coords']);
            $result = $twd->query("SELECT id FROM {$server}_village".
                                " WHERE x=".$twd->quote($v_x).
                                " AND y=".$twd->quote($v_y)." LIMIT 1")->fetch();
            if(!$result) {
                //$errors[] = "Ungültige Farm-Koordinaten!";
                //_displayErrors();
                $data['v_id'] = 0;
            }
            else {
                $data['v_id'] = $result['id'];
            }
            
            // SQL-INSERT bilden
            $sql = "INSERT INTO farms (".implode(',',array_keys($data)).") VALUES (";
            foreach($data as $value) {
                $sql .= "'".$mysql->escape($value)."',";
            }
            
            $sql = trim($sql, ',');
            $sql .= ")";
        }
        else {
            // Hat sich der Name des Angriffsdorfs geändert?
            if($farm_old['av_name'] != $data['av_name']) {
                // Namen des Angriffsdorfs in allen eingetragenen Farmen aktualisieren
                $res2 = $mysql->sql_query("UPDATE farms SET av_name='".$mysql->escape($data['av_name'])."' WHERE saveid='$saveid' AND av_coords='".$mysql->escape($data['av_coords'])."'");
                if(!$res2)
                    _displaySQLError();
            }
            
            if($farm_old['time'] > $data['time']) {
                $errors[] = "Für dieses Dorf gibt es bereits einen aktuelleren Bericht.";
                _displayErrors();
            }
            
            // die ID des Dorfes nachträglich hinzufügen
            // @TODO das sollte in einigen Wochen nicht mehr nötig sein
            if($farm_old['v_id'] == 0) {
                list($v_x, $v_y) = explode("|", $data['v_coords']);
                $result = $twd->query("SELECT id FROM {$server}_village".
                                    " WHERE x=".intval($v_x).
                                    " AND y=".intval($v_y)." LIMIT 1")->fetch();
                if(!$result) {
                    //$errors[] = "Ungültige Farm-Koordinaten!";
                    //_displayErrors();
                    $data['v_id'] = 0;
                }
                else {
                    $data['v_id'] = $result['id'];
                }
            }
            
            // Laufenden Durchschnitt der Performance aktualisieren
            if ($farm_old['time'] < $data['time']) {
                $expected_resources = array_sum(calculateExpectedResources($farm_old, $data['time'], $oServer));
                
                if ($expected_resources > 0) {
                    $actual_resources = array_sum($parsed['spied_resources']);
                    if ($parsed['booty']) {
                        $actual_resources += $parsed['booty']['all'];
                    }
                    
                    $performance_this_time = $actual_resources / floatval($expected_resources);
                    
                    if ($farm_old['performance'] === null) {
                        $data['performance'] = $performance_this_time;
                        $data['performance_updates'] = 1;
                    } else {
                        $alpha = max(0.2, 1.0 / ($farm_old['performance_updates'] + 1));
                        $data['performance'] = (1 - $alpha) * $farm_old['performance'] + $alpha * $performance_this_time;
                        $data['performance_updates'] = $farm_old['performance_updates'] + 1;
                    }
                }
            }
            
            // SQL-UPDATE bilden
            $sql = "UPDATE farms SET ";
            foreach($data as $key => $value) {
                $sql .= "`".$key."`='".$mysql->escape($value)."',";
            }
            
            $sql = trim($sql, ',');
            
            $sql .= " WHERE id='".intval($farm_old['id'])."' AND saveid='".$saveid."'";
        }
        
        $sql = trim($sql, ',');
        
        if(!$mysql->sql_query($sql)) {
            _displaySQLError();
        }
        
        if(_isAjaxRequest())
            die("Bericht erfolgreich eingelesen!");
        
        _redirect();
    }
    
    // eine Farm löschen?
    if(!empty($_GET['delete']) and is_number($_GET['delete'])) {
        $delete = intval($_GET['delete']);
        $sql = "DELETE FROM farms WHERE saveid='".$saveid."' AND id='$delete' LIMIT 1";
        if(!$mysql->sql_query($sql)) {
            _displaySQLError();
        }
        
        _redirect();
    }
    
    // eine Farm als gefarmt markieren?
    if(!empty($_GET['farmed']) and is_number($_GET['farmed'])) {
        $farmed = intval($_GET['farmed']);
        $sql = "UPDATE farms SET farmed=NOT farmed WHERE saveid='".$saveid."' AND id='$farmed' LIMIT 1";
        if(!$mysql->sql_query($sql)) {
            _displaySQLError();
        }
        
        _redirect();
    }
    
    // eine Farm bearbeiten?
    if(!empty($_GET['edit']) and is_number($_GET['edit'])) {
        $edit = intval($_GET['edit']);
        $sql = "SELECT id,note,bonus FROM farms WHERE saveid='".$saveid."' AND id='$edit' LIMIT 1";
        $res = $mysql->sql_query($sql);
        if(!$res)
            _displaySQLError();
        if($mysql->sql_num_rows($res) != 1) {
            $errors[] = "Ungültige Farm-ID!";
            _displayErrors();
        }
        
        $smarty->assign('edited_farm', $mysql->fetch($res));
    }
    // das Bearbeiten-Formular wurde bereits abgeschickt
    if(!empty($_POST) && !empty($_POST['edit']) && !empty($_POST['id']) && ctype_digit($_POST['id'])) {
        $id = intval($_POST['id']);
        
        $bonus = in_array($_POST['bonus'], $possible_boni) ? $_POST['bonus'] : '';
        
        if($bonus == '') {
            $errors[] = "Ungültige Bonus-Angabe!";
            _displayErrors();
        }
        
        $bonus = $mysql->escape($bonus);
        
        // Wenn auf den Storage-Bonus geändert wurde oder vom Storage-Bonus auf einen anderen Bonus,
        // dann muss der farmable-Wert aktualisiert werden
        // => Alten Bonus abrufen
        $sql = "SELECT bonus,b_storage,b_hide FROM farms WHERE saveid='".$saveid."' AND id='$id' LIMIT 1";
        $res = $mysql->sql_query($sql);
        if(!$res)
            _displaySQLError();
        if($mysql->sql_num_rows($res) != 1) {
            $errors[] = "Ungültige Farm-ID!";
            _displayErrors();
        }
        
        $new_farmable = false;
        $village_data = $mysql->fetch($res);
        $old_bonus = $village_data['bonus'];
        if($bonus == 'storage' && $old_bonus != 'storage') {
            $new_farmable = intval($oServer->calcStorageMax($village_data['b_storage'])*1.5 - $oServer->hideMax($village_data['b_hide']));
        }
        elseif($bonus != 'storage' && $old_bonus == 'storage') {
            $new_farmable = intval($oServer->calcStorageMax($village_data['b_storage'])     - $oServer->hideMax($village_data['b_hide']));
        }
        
        // => farmable-Wert ggf. aktualisieren
        if($new_farmable !== false) {
            $sql = "UPDATE farms SET farmable='".$new_farmable."' WHERE saveid='$saveid' AND id='$id'";
            if(!$mysql->sql_query($sql)) {
                _displaySQLError();
            }
        }
        
        $note = strlen($_POST['note']) <= 100 ? $_POST['note'] : substr($_POST['note'], 0, 100);
        $note = $mysql->escape($note);
        
        $sql = "UPDATE farms SET bonus='$bonus', note='$note' WHERE saveid='$saveid' AND id='$id'";
        
        if(!$mysql->sql_query($sql)) {
            _displaySQLError();
        }
        
        _redirect();
    }
    
    // seit dem letzten Bericht produzierte Ressourcen hinzufügen
    // und berechnen, wie viele lkav/speer benötigt werden zum transportieren
    $farms = _getFarms($saveid);
    
    // interessante Variablen, die die Summen der Werte der einzelnen Farmen enthalten
    $total_farms = 0;
    $count_farmed = 0; // Anzahl der gefarmten Farms (die grün markierten)
    $total_wood = 0;
    $total_loam = 0;
    $total_iron = 0;
    $total_sum = 0;
    $total_storage = 0;
    $total_spear = 0;
    $total_light = 0;
    
    $now = time();
    for($i=0; $i<count($farms); $i++) {
        // Erwartete Ressourcen ohne Berücksichtigung der Laufzeit
        list($farms[$i]['c_wood'], $farms[$i]['c_loam'], $farms[$i]['c_iron']) = array_values(calculateExpectedResources($farms[$i], $now, $oServer));
        
        // Gesamtsumme der Rohstoffe
        $farms[$i]['c_sum'] = $farms[$i]['c_wood'] + $farms[$i]['c_loam'] + $farms[$i]['c_iron'];
        
        // relativer Füllstand des Speichers
        $farms[$i]['fill_level'] =  ($farms[$i]['farmable'] > 0) ?
                                    (intval($farms[$i]['c_sum'] / ($farms[$i]['farmable'] * 3) * 100)) :
                                    0;
        
        // Entfernung zum Herkunftsdorf
        if($source_village) {
            $farms[$i]['distance'] = round(calcDistance($farms[$i]['v_coords'], $source_village), 1);
        }
        
        // Truppen, die zum Abtransport der Rohstoffe benötigt werden
        $units_tmp = array("spear" => 25.0, "light" => 80.0);
        foreach ($units_tmp as $unit => $carry) {
            if (!$source_village) {
                $farms[$i]["transport_$unit"] = $farms[$i]['c_sum'] / $carry;
            } elseif ($source_village) {
                // Erwartete Ressourcen mit Berücksichtigung der Laufzeit
                $runtime_in_seconds = $oServer->getTimePerField(array($unit => 1)) * $farms[$i]['distance'];
                $expected_resources_with_runtime = array_sum(calculateExpectedResources($farms[$i], $now + $runtime_in_seconds, $oServer));
                
                $farms[$i]["transport_$unit"] = ($expected_resources_with_runtime) / $carry;
            }
            
            $farms[$i]["transport_$unit"] = ceil($farms[$i]["transport_$unit"]);
        }
        
        // Performance
        if ($farms[$i]['performance'] !== null)
            $farms[$i]['performance_percentage'] = round(100 * $farms[$i]['performance']);
        
        // Filtern? (Also ausschließen?)
        $farms[$i]['filter'] = false;
        if ($farms[$i]['c_sum'] < $filter_min_ress) {
            $farms[$i]['filter'] = true;
        }
        
        // Anzahl Späher, die zu dieser Farm losgeschickt werden müssen (verlustfrei), bestimmen
        // => siehe: http://forum.die-staemme.de/showthread.php?69629-XML-Bedeutungen&p=3700438&viewfull=1#post3700438
        if ($server_cfg["game"]["spy"] != 3) {
            $farms[$i]["spy_count"] = 1;
        } else if ($farms[$i]["v_name"] == "Barbarendorf" || $farms[$i]["v_name"] == "Bonusdorf") {
            $farms[$i]["spy_count"] = 4;
        } else {
            $farms[$i]["spy_count"] = 5;
        }
        
        // die Summen...
        if (!$farms[$i]['filter']) {
            $total_farms++;
            if ($farms[$i]["farmed"]) $count_farmed++;
            $total_wood += $farms[$i]['c_wood'];            // Gesamt-Holz
            $total_loam += $farms[$i]['c_loam'];            // Gesamt-Lehm
            $total_iron += $farms[$i]['c_iron'];            // Gesamt-Eisen
            $total_sum += $farms[$i]['c_sum'];              // Gesamt-Ressourcen
            $total_storage += $farms[$i]['storage_max'];    // Gesamt-Speichervolumen
            $total_spear += $farms[$i]['transport_spear'];  // Gesamt-Speerträger-Bedarf
            $total_light += $farms[$i]['transport_light'];  // Gesamt-LKav-Bedarf
        }
    }
    
    // ggf. die Farmen sortieren (sind bereits nach dem letzten Bericht vorsortiert durch die SQL-Abfrage)
    if($order != 'lastreport' && !($order=='distance' && !$source_village)) {
        $cmp_key = '';
        $dir = 'desc';
        
        // Vergleichsfunktion für usort()
        function _cmpCallback($a,$b) {
            global $cmp_key, $dir;
            
            if($dir == 'desc') {
                $smaller =  1;
                $bigger  = -1;
            } elseif ($dir == 'asc') {
                $smaller = -1;
                $bigger  =  1;
            } else
                throw new InvalidArgumentException("Unknown sort direction.");
        
            if($a['farmed'] && !$b['farmed'])
                return 1;
            elseif(!$a['farmed'] && $b['farmed'])
                return -1;
                
            $a = $a[$cmp_key];
            $b = $b[$cmp_key];
            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? $smaller : $bigger;
        }
        
        // sortieren
        /** @todo: in SWITCH-statement umschreiben */
        if($order == 'v_coords') {
            $cmp_key = 'v_coords';
            $dir = 'asc';
        }
        elseif($order == 'c_sum') {
            $cmp_key = 'c_sum';
        }
        elseif($order == 'fill_level') {
            $cmp_key = 'fill_level';
        }
        elseif($order == 'distance') {
            $cmp_key = 'distance';
            $dir = 'asc';
        }
        elseif($order == 'c_wood') {
            $cmp_key = 'c_wood';
        }
        elseif($order == 'c_loam') {
            $cmp_key = 'c_loam';
        }
        elseif($order == 'c_iron') {
            $cmp_key = 'c_iron';
        }
        elseif($order == 'light') {
            $cmp_key = 'transport_light';
        }
        elseif($order == 'spear') {
            $cmp_key = 'transport_spear';
        }
        elseif($order == 'storage') {
            $cmp_key = 'storage_max';
        }
        
        usort($farms,"_cmpCallback");
    }
    
    $smarty->assign('total_farms', $total_farms);
    $smarty->assign('count_farmed', $count_farmed);
    $smarty->assign('total_wood', $total_wood);
    $smarty->assign('total_loam', $total_loam);
    $smarty->assign('total_iron', $total_iron);
    $smarty->assign('total_sum', $total_sum);
    $smarty->assign('total_storage', $total_storage);
    $smarty->assign('total_spear', $total_spear);
    $smarty->assign('total_light', $total_light);
    
    $smarty->assign('bonus_new', $bonus_new);
    $smarty->assign('bonus_res_all', ($oServer->bonusResAllFactor()-1)*100);
    $smarty->assign('bonus_res_one', ($oServer->bonusResOneFactor()-1)*100);
    
    $smarty->assign('farms', $farms);
    $smarty->display('farmmanager_main.tpl');
?>
