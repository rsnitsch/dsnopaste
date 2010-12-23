<?php
    if(!defined('INC_CHECK')) die("not that way");
    
    // Konfigurationskonstanten
    $uploaded = !preg_match('%/home/robert.*%', __FILE__);
    define('CFG_UPLOADED',$uploaded);
    define('CFG_ENABLED',TRUE);
    
    // Server-spezifische Einstellungen
    switch($_SERVER['SERVER_ADDR']) {
        case '178.77.99.165':
            define('CFG_SERVERPATH','http://np.bmaker.net');
            define('CFG_SMARTYDIR',$root_path.'smarty/Smarty-2.6.26/libs');
            break;
        case '127.0.0.1':
            define('CFG_SERVERPATH','http://localhost/~robert/nopaste');
            define('CFG_SMARTYDIR','/home/robert/coding/obst/obst-ab/include/libs/smarty');
            break;
        default:
            throw new Exception("SERVER_ADDR unknown.");
    }
    
    define('CFG_TPLDIR',$root_path.'tpl');
    define('CFG_INCDIR',$root_path.'include');
    
    define('CFG_DEBUGMODE',true || !$uploaded ||
           $_SERVER['REMOTE_ADDR'] == '127.0.0.1' ||
           $_SERVER['REMOTE_ADDR'] == $_SERVER['SERVER_ADDR']);
    
    // externe Authentifizierung
    define('AUTH_KEY', 'DSNoPaste');
    define('AUTH_SECRET', '1ab24a037c6155e112f684153f7335ad');
    
    // TWDATA
    define('TWDATA_INCLUDE', '/home/twdata/twdata.php');
    
    $language = 'de';
    
    // ankündigung?
    $announce = false;
    $announcing = "Willkommen auf dem neuen Server. ;)";
    define('CFG_GLOBAL_ANNOUNCING', $announce ? $announcing : '');
    
    define('CFG_ROOTPATH', $root_path);
    
    if(CFG_DEBUGMODE)
    {
        error_reporting(E_ALL);
    }
    else
    {
        error_reporting(0);
    }
    
    // Includes etc...
    define('SSQL_INC_CHECK',TRUE);
    require(CFG_INCDIR.'/class.simpleMySQL.php');
    require(CFG_INCDIR.'/functions.inc.php');
    include(CFG_INCDIR.'/mysql.inc.php');
    require(CFG_INCDIR.'/class.outputControl.php');
    require(CFG_INCDIR.'/class.nopSmarty.php');
    require(CFG_INCDIR.'/Session.class.php');
    
    // MySQL
    $mysql=FALSE;
?>
