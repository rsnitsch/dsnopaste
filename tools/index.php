<?php
    define('INC_CHECK', true);
    
    $root_path='../';
    require($root_path.'include/config.inc.php');
    header('Location: '.$cfg["serverpath"]);
    exit();
?>