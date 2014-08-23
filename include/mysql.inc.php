<?php
    if(!defined('INC_CHECK')) die('denied!');
    
    $mysql_conn=NULL;
    
    // baut die MySQL-Verbindung auf
    function enableMySQL($useclass=FALSE)
    {
        global $cfg;
        global $debuginfo;
        
        if(!$useclass)
        {
            // alte Methode
            global $mysql_conn;
            
            if($mysql_conn = mysql_connect($cfg["mysql_host"], $cfg["mysql_user"], $cfg["mysql_pass"]))
            {
                if(!mysql_select_db($cfg["mysql_db"], $mysql_conn))
                {
                    $debuginfo[] = "Konnte die Datenbank nicht auswählen!";
                    return FALSE;
                }
                
                return TRUE;
            }
            else
            {
                $debuginfo[] = "mysql_connect ist fehlgeschlagen!";
                $debuginfo[] = mysql_error();
                return FALSE;
            }
        }
        else
        {
            // neue Methode (es soll auf eine SQL-Klasse umgestiegen werden. Noch verwenden aber viele Dateien direkt die SQL-Funktionen!)
            global $mysql;
            $mysql=new simpleMySQL($cfg["mysql_user"], $cfg["mysql_pass"], $cfg["mysql_db"], $cfg["mysql_host"]);
            
            if(!$mysql->connected())
            {
                 $debuginfo[] = "Fehlermeldung der SQL-Klasse: ".$mysql->lasterror;
                 return FALSE;
            }
            return TRUE;
        }
    }
