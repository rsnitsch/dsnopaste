<?php
    define('INC_CHECK',true);
    $root_path='../';
    include($root_path.'include/config.inc.php');
    
    $smarty = new nopSmarty();
    
    $smarty->assign('title','Truppen schicken');
    $smarty->assign('root_path',$root_path);
    $smarty->assign('subid', 'sendtroops');
    //$smarty->assign('global_announcing', 'Eine neue Version des Einlese-Skriptes (unter anderem jetzt kompatibel zu DS 6.0, also insbesondere Welt 55/56) steht zum Testen bereit: <a href="http://forum.np.bmaker.net/viewtopic.php?id=98">http://forum.np.bmaker.net/viewtopic.php?id=98</a>');
    
    $errors = array();
    $debugs = array();
    
    // ist die Seite aktiviert
    if(!$cfg["enabled"]) {
        $smarty->display('offline.tpl');
        exit();
    }
    
    require($cfg["twdata_include"]);
    
    // Hilfsfunktionen
    function _displayErrors() {
        global $smarty, $errors, $debugs;
        displayErrors($smarty, $errors, $debugs);
    }
    
    // ###################################################
    // ###################################################
    // Beginn des eigentlichen Skriptes
    // ################################
    
    function main() {
        global $smarty, $errors, $debugs, $twd;
        
        // Daten validieren
        $world_id = $_GET['world'];
        
        if(!serverExists($world_id)) {
            $errors[] = "Ungültige Welt!";
            _displayErrors();
        }
        
        $server = Gameworld::forServerID($world_id);
        
        $troops = array();
        foreach($server->getUnitNames(true) as $unitname) {
            $troops[] = array('unitname' => $unitname,
                              'count'    => max(0, (!empty($_GET[$unitname]) ? intval($_GET[$unitname]) : 0)));
        }
        
        $from = intval($_GET['from']);
        $to   = intval($_GET['to']);
        
        $world_url = TWData::get_worldurl($world_id);
        if($world_url === false) {
            $errors[] = "Diese Welt ist momentan nicht verfügbar! Probiere es in 10 Minuten nochmal.";
            _displayErrors();
        }
        
        $to_village = $twd->query("SELECT x,y FROM `{$world_id}_village` WHERE id=".$twd->quote($to)." LIMIT 1")->fetch();
        if($to_village == false) {
            $errors[] = "Ungültige Target-ID.";
            _displayErrors();
        }
        
        $to_x = $to_village['x'];
        $to_y = $to_village['y'];
        
        $smarty->assign('world_url', $world_url);
        $smarty->assign('troops', $troops);
        
        $smarty->assign('from', $from);
        $smarty->assign('to_x', $to_x);
        $smarty->assign('to_y', $to_y);
        
        $smarty->display("sendtroops_form.tpl");
    }
    
    try {
        $twd = TWData::get_db_connection();
        main();
    }
    catch(Exception $exc) {
        $errors[] = "Interner Fehler!";
        $debugs[] = "Fehlermeldung: ".$exc->getMessage();
        _displayErrors();
    }
?>