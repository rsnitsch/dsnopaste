<?php
    define('INC_CHECK',true);
    $root_path='../';
    include($root_path.'include/config.inc.php');
    
    // Output-Control anlegen
    $output = new nopSmarty();
    $output->registerPlugin('function', 'paginate', 'smarty_paginate');

    // Allgemein Daten
    $output->assign('root_path', '../');
    $output->assign('title', 'Administration');
    $output->assign('cfg_enabled', boolYesNo($cfg["enabled"]));
    $output->assign('cfg_uploaded', boolYesNo($cfg["uploaded"]));
    $output->assign('cfg_debugmode', boolYesNo($cfg["debugmode"]));
    
    // nur wenn SQL aktiviert werden kann...
    if(enableMySQL(true))
    {
        // angriffspläne abfragen
        $total_count = $mysql->sql_result($mysql->sql_query('SELECT COUNT(*) AS total FROM attplans'), 0, 'total');
        $output->assign('count', $total_count);
        
        $server = addslashes(paramGET('server', ''));
        
        $limit = 50;
        $page = paramGET('page', 1);
        if(!is_numeric($page) or $page < 1)
            $page = 1;
        
        $output->assign('page', $page);
        
        $offset = ($page-1) * 50;
        $limit = "LIMIT $offset,50";
        
        $plans = array();
        $query = $mysql->sql_query("SELECT * FROM attplans ".(!empty($server) ? "WHERE server='$server'" : "")." $limit");
        if(!$query)
            die($mysql->lastquery.'<br />'.$mysql->lasterror);
            
        while($plan = $mysql->sql_fetch_assoc($query))
            $plans[] = $plan;
            
        $output->assign('plans', $plans);
    }
    else
    {
        die('Die SQL-Verbindung konnte nicht aufgebaut werden!');
    }
    
    // den Footer inkludieren
    $output->display('admin.tpl');
    
    // gibt zu einem Boolean-Wert jeweils "Ja" (TRUE) oder "Nein" (FALSE) zurück
    function boolYesNo($bool)
    {
        if($bool) return 'Ja';
        return 'Nein';
    }
    
    function smarty_paginate ($aParam) {
        // {paginate count=30 curr=1 max=10 url=http://here.com/page-::PAGE::}
        
        $nPageCnt  =  $aParam['count'];
        $nCurrPage  =   $aParam['curr'];
        $nMaxPage  =   $aParam['max'];
        $sUrl    =   $aParam['url'];
        $sOut    =   "";
        
        $bDrewDots = false;
        
        if ($nPageCnt > $nMaxPage) {
        
            if (1 > ($nCurrPage - ($nMaxPage /2))) {
            $nStart = 1;
            $nEnd  = $nMaxPage;
            } elseif ($nPageCnt < ($nCurrPage + ($nMaxPage /2))) {
            $nStart = $nPageCnt - $nMaxPage;
            $nEnd  = $nPageCnt;
            } else {
            $nStart = $nCurrPage - ($nMaxPage / 2);
            $nEnd  = $nCurrPage + ($nMaxPage / 2);
            }//if
            
        } else {
            $nStart = 1;
            $nEnd  = $nPageCnt;
        }//if
        
        for ($a = $nStart; $a <= $nEnd; $a++) {
            
            if ($a == $nCurrPage)
                $sOut .= "<span class='current'>${a}</span>";
            else
                $sOut .= "<a href='" . str_replace('::PAGE::', $a, $sUrl) . "' title='Go to page ${a}'>${a}</a>";
        
        }//for
        
        if ($nStart > 3) {
        
            $sOut = "
            <a href='" . str_replace('::PAGE::', 1, $sUrl) . "' title='Go to page 1>1</a>
            <a href='" . str_replace('::PAGE::', 2, $sUrl) . "' title='Go to page 2>2</a>
            <span>&#8230;</span>" . $sOut;
        
        }//if
        
        if ($nEnd < ($nPageCnt - 3)) {
        
            $sOut .= "
            <span>&#8230;</span>
            <a href='" . str_replace('::PAGE::', $nPageCnt - 1, $sUrl) . "' title='Go to page " . ($nPageCnt - 1) . "'>" . ($nPageCnt-1) . "</a>
            <a href='" . str_replace('::PAGE::', $nPageCnt, $sUrl) . "' title='Go to page " .$nPageCnt . "'>" .$nPageCnt . "</a>
            " ;
        
        // die($sOut);
        }//if
        
        if ($nCurrPage == 1)
            $sOut = '<span class="nextprev">&#171; Previous</span>' . $sOut;
            else 
            $sOut = '<a href="' . str_replace('::PAGE::', $nCurrPage - 1, $sUrl) . '" class="nextprev" title="Go to Previous Page">&#171; Previous</a>' . $sOut;
            
        
        if ($nCurrPage >= $nPageCnt)
            $sOut .= '<span class="nextprev">Next &#187;</span>';
            else 
            $sOut .= '<a href="' . str_replace('::PAGE::', $nCurrPage + 1, $sUrl) . '" class="nextprev" title="Go to Next Page">Next &#187;</a>';
        
        return $sOut;
            
    } //smarty_pagenate
