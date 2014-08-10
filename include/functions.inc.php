<?php
    if(!defined('INC_CHECK')) die("hacking attempt");
    
    function is_number($txt) {
        return ctype_digit($txt);
    }
    
    function paramGET($key, $std=null)
    {
        if(isset($_GET[$key]))
            return $_GET[$key];
        
        return $std;
    }
    
    function noSqlConnection($output)
    {
        global $errors;
        $errors[]='Interner Fehler: Es kann keine Verbindung zur Datenbank aufgebaut werden.';
        displayErrors($output, $errors);
    }
    
    
    // Funktion short()
    // kürzt einen String falls er länger als $max Zeichen ist
    function short($string, $max, $points=FALSE)
    {
        if(strlen($string)>$max)
        {
            $string=substr($string,0,$max);
            if($points) $string.='...';
        }
        
        return $string;
    }
    
    // generiert ein Passwort
    function generatePassword($len)
    {
        $pass_word='';
        
        // Im Pool der möglichen Zeichen kommen übrigens absichtlich kein j, i, l, I, o, O, Q, 1 und keine O vor, da im Praxisbetrieb die User immer wieder Schwierigkeiten hatten,
        // die Zeichen richtig zu lesen und dann richtig einzugeben.
        $pool = "qwertzupasdfghkyxcvbnm";
        $pool .= "23456789";
        $pool .= "WERTZUPLKJHGFDSAYXCVBNM";

        srand((double)microtime()*1000000);
        for($index = 0; $index < $len; $index++)
        {
            $pass_word .= substr($pool,(rand()%(strlen ($pool))), 1);
        }
        
        return $pass_word;
    }
    
    /**
     * Überprüft, ob NoPaste einen Server unterstützt.
     */
    function serverExists($server)
    {
        global $root_path, $cfg;
        return isValidServerID($server) and is_dir($root_path."data/server/{$cfg['language']}/$server");
    }
    
    /**
     * Gibt ein Array zurück mit den IDs und Namen
     * aller von Nopaste unterstützten Server.
     */
    function getServers()
    {
        global $root_path;

        $servers = array();
        $path = $root_path.'data/server/de';

        if (!is_dir($path)) {
            throw new Exception("Directory 'data/server/de' does not exist!");
        }

        $items = scandir($path);
        foreach ($items as $item) {
            if ($item != "." && $item != ".." && is_dir($path."/".$item)) {
                $servers[] = array('id' => $item, 'name' => Gameworld::nameForID($item));
            }
        }

        usort($servers, "serverCmp");
        return $servers;
    }
    
    function serverCmp($a, $b)
    {
        return -strnatcmp($a["name"], $b["name"]);
    }
    
    /**
     * Zeigt eine NoPaste-Fehlerseite an.
     * 
     * Die einzelnen Fehler werden per $errors übergeben.
     * 
     * Zusätzlich können Debug-Informationen per $debugs angegeben werden.
     * Diese werden automatisch angezeigt, wenn der DEBUG-Modus aktiviert ist.
     */
    function displayErrors(Smarty $smarty, $errors, $debugs=null) {
        global $cfg;
        
        if($debugs == null or !$cfg["debugmode"])
            $debugs = array();
            
        $smarty->assign('error', $errors);
        $smarty->assign('debuginfo', $debugs);
        $smarty->display('display_errors.tpl');
        exit();
    }
    
    /**
     * Zeigt einen SQL-Fehler an.
     * 
     * SQL-Abfrage und SQL-Fehlermeldung werden dem Benutzer nur bei aktiviertem
     * DEBUG-Modus gezeigt.
     */
    function displaySQLError(Smarty $smarty) {
        global $mysql;
        
        $errors=array();
        $debugs=array();
        $errors[] = "SQL-Fehler!";
        $debugs[] = "SQL-Abfrage: ".$mysql->lastquery;
        $debugs[] = "SQL-Fehler: ".$mysql->lasterror;
        $debugs[] = "mysql_error(): ".mysql_error();
        displayErrors($smarty, $errors, $debugs);
    }
    
    /**
     * Durchsucht ein Array der Form
     *  array(
     *      array("spalte1" => "wert11", "spalte2" => "wert12"),
     *      array("spalte1" => "wert21", "spalte2" => "wert22")
     *      );
     *  Es wird nur die Spalte $column durchsucht.
     */
    function inArrayColumn($needle, $array, $column) {
        foreach($array as $e) {
            if($e[$column] == $needle)
                return true;
        }
        return false;
    }

    function server_url() {
        global $cfg;
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https://' : 'http://');
        return $protocol.trim($cfg["serverpath"], "/");
    }
