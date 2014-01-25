<?php
echo "Cron started.<br />";

$root_path='../../';
$dir = $root_path.'data/server/de';

$data = file_get_contents('http://www.die-staemme.de/backend/get_servers.php');

if(!$data) {
    die("file_get_contents (get_servers.php) failed!");
}

$servers = unserialize($data);

$files_to_download = array( 'config.xml' => '/interface.php?func=get_config',
                            'units.xml' => '/interface.php?func=get_unit_info',
                            'buildings.xml' => '/interface.php?func=get_building_info'  );

// array storing information, which gets stored in the servers.xml file later
$avail_servers = array('de' => array());

// download the server data for each server
foreach($servers as $key => $url) {
    $serverdir = $dir.'/'.$key;
    
    if(!is_dir($serverdir)) {
        if (preg_match("/^de[0-9]+$/", $key)) {
            if (@mkdir($serverdir)) {
                echo "Directory for server '$key' has been created automatically.<br />";
            } else {
                echo "Directory for server '$key' does not exist and could not be created.<br />";
                continue;
            }
        } else {
            echo "Directory for server '$key' does not exist. Server '$key' is not a normal world (i.e. classic or speed), so it is not automatically added.<br />";
            continue;
        }
    }
    
    // check whether $serverdir is writeable and attempt to chmod, if not
    if(!is_writeable($serverdir)) {
        if(!@chmod($serverdir, 0777)) {
            echo "Directory of server '$key' is not writeable. Additionally, chmod(0777) failed.<br />";
            continue;
        }
    }
    
    // actually download the files
    if(!isset($_GET['nodl'])) {
        foreach($files_to_download as $destinationFile => $path) {
            echo "Downloading '$url$path'... ";
            
            if(copy($url.$path, $serverdir.'/'.$destinationFile))
                done();
            else
                failed();
        }
    }

    // Delete obsolete meta.xml files
    if(file_exists($serverdir.'/meta.xml')) {
        echo "Server '$key': Removing obsolete meta.xml file<br />\n";
        unlink($serverdir.'/meta.xml');
    }

    $avail_servers['de'][$key] = "";
}

// Remove obsolete servers.xml file
if (is_file($root_path.'data/server/servers.xml')) {
    echo "Removing obsolete servers.xml file<br />\n";
    unlink($root_path.'data/server/servers.xml');
}

function red($txt) { return '<span style="color: #FF2121;">'.$txt.'</span>'; }
function done($br=true) { echo '<span style="color: #21FF21; font-weight: bold;">done</span>'; if($br) echo "<br />\n"; }
function failed($br=true) { echo '<span style="color: #FF2121; font-weight: bold;">failed</span>'; if($br) echo "<br />\n"; }
?>