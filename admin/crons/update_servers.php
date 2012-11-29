<?php
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

$metafile_template = <<<META
<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<meta>
    <name>{name}</name>
</meta>
META;

if(isset($_GET['force_meta']))
    echo red('Meta file creation forced!')."<br />\n";
    
// array storing information, which gets stored in the servers.xml file later
$avail_servers = array('de' => array());

// download the server data for each server
foreach($servers as $key => $url) {
    $serverdir = $dir.'/'.$key;
    
    if(is_dir($serverdir)) {
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
        
        // check whether the meta file exists
        if(file_exists($serverdir.'/meta.xml') and !isset($_GET['force_meta']))
            $avail_servers['de'][$key] = simplexml_load_file($serverdir.'/meta.xml')->name;
        else {
            // no meta file available, create initial one
            echo "Creating initial meta file for $key...";
            
            // guess a good name for the world
            // if a number is in the world's ID, take the number, otherwise take the ID unchanged
            $name = 'Welt ';
            $matches = array();
            $number = 0;
            if(preg_match('/[^0-9]+([0-9]+)$/', $key, $matches)) {
                $number = $matches[1];
                $name .= $number;
            } else {
                $name .= $key;
            }
            
            // write the metafile
            $metafile = str_replace('{name}', $name, $metafile_template);
            
            $fh = @fopen($serverdir.'/meta.xml', 'w');
            if(!$fh) {
                failed();
            }
            else {
                fputs($fh, $metafile);
                fclose($fh);
                
                done();
            }
            
            // prepare the name to be stored in the servers.xml file
            $avail_servers['de'][$key] = simplexml_load_file($serverdir.'/meta.xml')->name;
        }
    }
}

// write the servers.xml file
$servers_xml = "<?xml version='1.0'?><servers>";
foreach($avail_servers as $language => $servers) {
	$servers_xml .= "<$language>";
    
    foreach($servers as $id => $name)
        $servers_xml .= "<$id>$name</$id>";
        
    $servers_xml .= "</$language>";
}
$servers_xml .= "</servers>";
$fh = fopen('servers.xml', 'w');
fwrite($fh, $servers_xml);
fclose($fh);


function red($txt) { return '<span style="color: #FF2121;">'.$txt.'</span>'; }
function done($br=true) { echo '<span style="color: #21FF21; font-weight: bold;">done</span>'; if($br) echo "<br />\n"; }
function failed($br=true) { echo '<span style="color: #FF2121; font-weight: bold;">failed</span>'; if($br) echo "<br />\n"; }
?>