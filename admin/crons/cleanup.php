<pre>
<?php
    /**
     * Dieses Skript wird täglich aufgerufen
     * 
     * Es erledigt folgende Aufgaben:
     * - alle Angriffspläne ohne Aktionen werden gelöscht
     * - alle Angriffspläne älter als 30 Tage und ihre Aktionen werden gelöscht
     * - Farmen älter als 30 Tage löschen
     * - leere Farmmanager löschen
     */
    
    define('INC_CHECK',true);
    $root_path='../../';
    
    error_reporting(E_ALL);
    
    $log=fopen($root_path.'data/log/cronjob.cleanup.log','a');
    
    // mysql
    require($root_path."include/mysql.inc.php");
    enableMySQL(false) or die("Couldnt connect to database.");
    
    header("Content-Type: text/html; charset=utf-8");
    echoLog("Verbindung zu Datenbank steht. Beginn des Cronjobs...");
    
    // Angriffspläne älter als 1 Monat löschen
    $sql_cmd='SELECT id FROM attplans WHERE time<'.(time()-2592000);
    $result=mysql_query($sql_cmd, $mysql_conn);
    if($result and mysql_num_rows($result)>0)
    {
        echoLog("Löschen von ".mysql_num_rows($result)." ausgelaufenen (30+ Tage) Angriffsplänen und ihre Aktionen...");
        
        while($row=mysql_fetch_assoc($result))
        {
            $sql_cmd='DELETE FROM attplans WHERE id='.$row['id'];
            if(!mysql_query($sql_cmd, $mysql_conn))
                echoLog('Angriffsplan Nr. '.$row['id'].' nicht gelöscht, SQL-Fehler: '.mysql_error($mysql_conn));
            
            $sql_cmd='DELETE FROM attplans_actions WHERE attplan_id='.$row['id'];
            if(!mysql_query($sql_cmd, $mysql_conn))
                echoLog('Angriffsplan-Aktionen nicht gelöscht, SQL-Fehler: '.mysql_error($mysql_conn));
        }
    }
    elseif(!$result)
    {
        echoLog("Ein SQL-Fehler trat auf: ".mysql_error($mysql_conn));
        echoLog("SQL-Query: "+$sql_cmd);
    }
    else
    {
        echoLog("Es gibt keine Angriffspläne älter als 30 Tage.");
    }
    
    // Angriffspläne ohne Aktionen löschen
    $sql_cmd='DELETE attplans, attplans_actions FROM attplans LEFT JOIN attplans_actions ON attplans.id = attplans_actions.attplan_id WHERE attplans_actions.attplan_id IS NULL';
    $result=mysql_query($sql_cmd, $mysql_conn);
    if($result === TRUE)
    {
        echoLog('Es wurden '.mysql_affected_rows($mysql_conn).' Angriffspläne ohne Aktionen gelöscht...');
    }
    else
    {
        echoLog('FEHLER: Angriffspläne ohne Aktionen konnten nicht gelöscht werden. SQL-Fehler: '.mysql_error($mysql_conn));
    }
    
    // Farmen älter als 30 Tage löschen
    $sql_cmd = 'DELETE FROM `farms` WHERE time < '.(time()-2592000);
    $result = mysql_query($sql_cmd, $mysql_conn);
    if($result === true)
    {
        echoLog('Es wurden '.mysql_affected_rows($mysql_conn).' Farmen älter als 30 Tage gelöscht...');
    }
    else
    {
        echoLog('FEHLER: Es wurden keine Farmen älter als 30 Tage gelöscht... SQL-Fehler: '.mysql_error($mysql_conn));
    }
    
    // Leere Farmmanager löschen
    $sql_cmd = 'DELETE farmmanagers, farms FROM farmmanagers LEFT JOIN farms ON farmmanagers.id = farms.saveid WHERE farms.id IS NULL';
    $result = mysql_query($sql_cmd, $mysql_conn);
    if($result === true)
    {
        echoLog('Es wurden '.mysql_affected_rows($mysql_conn).' leere Farmmanager gelöscht...');
    }
    else
    {
        echoLog('FEHLER: Es wurden keine leeren Farmmanager gelöscht... SQL-Fehler: '.mysql_error($mysql_conn));
    }
    
    // LOGDATEI SCHLIESSEN
    fclose($log);
    
    // echo $text and write $text to a logfile, too
    function echoLog($text)
    {
        global $log;
        
        $text=date("d.m.Y, H:i:s").' | '.$text;
        
        echo $text."\n";
        fputs($log, $text."\n");
    }
?>
</pre>