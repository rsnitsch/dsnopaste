<?php
    if(!defined('INC_CHECK')) die("not that way");

    $cfg_defaults = array(
        "uploaded" => true,
        "enabled" => true,
        "serverpath" => "np.bmaker.de", // URL to this NP install, without the protocol part. Trailing forward slashes are automatically removed.
        "smartydir" => $root_path."smarty/libs",
        "tpldir" => $root_path."tpl",
        "incdir" => $root_path."include",
        "debugmode" => false,
        "twdata_include" => "/home/twdata/twdata.php",
        "language" => "de",
        "announce" => false,
        "announcing" => "",
        "mysql_host" => "localhost",
        "mysql_user" => "nopaste",
        "mysql_db" => "nopaste",
        "mysql_pass" => ""
    );

    // externe Authentifizierung
    define('AUTH_KEY', 'DSNoPaste');
    define('AUTH_SECRET', '1ab24a037c6155e112f684153f7335ad');

    // Lokale config inkludieren (kann Parameter überschreiben).
    if (is_readable($root_path.'/include/localconfig.inc.php')) {
        require($root_path.'/include/localconfig.inc.php');
    } else if (is_readable($root_path.'/include/config.local.inc.php')) {
        require($root_path.'/include/config.local.inc.php');
    } else {
        die("Invalid installation: No local config present.");
    }

    error_reporting($cfg["debugmode"] ? E_ALL : 0);

    // Includes etc...
    define('SSQL_INC_CHECK',TRUE);
    require_once($cfg["incdir"].'/class.simpleMySQL.php');
    require_once($cfg["incdir"].'/functions.inc.php');
    require_once($cfg["incdir"].'/functions.tribalwars.inc.php');
    require_once($cfg["incdir"].'/mysql.inc.php');
    require_once($cfg["incdir"].'/class.nopSmarty.php');
    require_once($cfg["incdir"].'/Session.class.php');
	require_once($cfg["incdir"].'/lang.inc.php');

    // MySQL
    $mysql=FALSE;
?>
