<?php
    // stellt die Smartyklasse bereit
    
    require(CFG_SMARTYDIR.'/Smarty.class.php');
    
    class nopSmarty extends Smarty {
    
        function nopSmarty()
        {
            $this->template_dir = CFG_TPLDIR;
            $this->compile_dir = CFG_ROOTPATH.'data/cache/compiled';
            $this->cache_dir = CFG_ROOTPATH.'data/cache';
            
            $this->assign('global_announcing', CFG_GLOBAL_ANNOUNCING);
            $this->assign('debugmode', CFG_DEBUGMODE);
            
            $this->assign('server_url', trim(CFG_SERVERPATH, "/"));
        }
    
    }

?>