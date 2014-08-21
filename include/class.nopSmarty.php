<?php
    // stellt die Smartyklasse bereit
    
    require($cfg["smartydir"].'/Smarty.class.php');
    
    class nopSmarty extends Smarty {
        function __construct()
        {
            parent::__construct();
            
            global $root_path, $cfg;
            
            $this->template_dir = $cfg["tpldir"];
            $this->compile_dir = $root_path.'data/cache/compiled';
            $this->cache_dir = $root_path.'data/cache';
            
            $this->assign('cfg', $cfg);
            
            $this->assign('global_announcing', $cfg["announcing"]);
            $this->assign('debugmode', $cfg["debugmode"]);
            
			$this->assign('request_protocol', request_protocol());
            $this->assign('server_url', server_url());
			
			// Translation functions
			$this->registerPlugin('block', 't', 't_smarty');
			$this->registerPlugin('block', 'tp', 'tp_smarty');
        }
    
    }

?>