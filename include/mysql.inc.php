<?php
    if(!defined('INC_CHECK')) die('denied!');
    
    $mysql_conn=NULL;
    
    $mysql_pass='';
    if(!$cfg["uploaded"])
    {
        define('MYSQL_HOST','localhost');
        define('MYSQL_USER','robert');
        $mysql_pass='robtretrerob';
        define('MYSQL_DB','nopaste');
    }
    else
    {
    	switch($_SERVER['SERVER_ADDR']) {
			case '178.77.99.165':
				define('MYSQL_HOST','localhost');
				define('MYSQL_USER','nopaste');
				$mysql_pass='tErp6w6QxpMx8WxR';
				define('MYSQL_DB','nopaste');
				break;
		    default:
		    	throw new Exception("No database credentials available.");
    	}
    }
    
    // baut die MySQL-Verbindung auf
    function enableMySQL($useclass=FALSE)
    {
        global $mysql_pass;
        global $debuginfo;
        
        if(!$useclass)
        {
            // alte Methode
            global $mysql_conn;
            
            if($mysql_conn = mysql_connect(MYSQL_HOST, MYSQL_USER, $mysql_pass))
            {
				unset($mysql_pass);
				
                if(!mysql_select_db(MYSQL_DB, $mysql_conn))
                {
                    $debuginfo[] = "Konnte die Datenbank nicht auswählen!";
                    return FALSE;
                }
                
                return TRUE;
            }
            else
            {
                unset($mysql_pass);
                $debuginfo[] = "mysql_connect ist fehlgeschlagen!";
                $debuginfo[] = mysql_error();
                return FALSE;
            }
        }
        else
        {
            // neue Methode (es soll auf eine SQL-Klasse umgestiegen werden. Noch verwenden aber viele Dateien direkt die SQL-Funktionen!)
            global $mysql;
            $mysql=new simpleMySQL(MYSQL_USER, $mysql_pass, MYSQL_DB, MYSQL_HOST);
            
            unset($mysql_pass);
            
            if(!$mysql->connected())
            {
                 $debuginfo[] = "Fehlermeldung der SQL-Klasse: ".$mysql->lasterror;
                 return FALSE;
            }
            return TRUE;
        }
    }
?>
