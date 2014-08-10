<?php
    define('INC_CHECK', true);
    
    $root_path='../';
    require($root_path.'include/config.inc.php');
    header('Location: '.server_url());
    exit();
?>