<?php
	define('INC_CHECK',true);
	$root_path='./';
	include($root_path.'include/config.inc.php');
	
	require_once "XML/RSS.php";
	
	// Smarty starten
	$output = new nopSmarty();

	// den Header inkludieren
	$output->assign('root_path', $root_path);
	$output->assign('title','Start');
	$output->assign('subid', 'start');
	
	// News abrufen
	$cache_file = $root_path.'cache/news_items.cache';
	if(CFG_DEBUGMODE || (time() - filemtime($cache_file)) > 3600) {
		// CACHE MISS
		$rss =& new XML_RSS("http://forum.np.bmaker.net/extern.php?action=feed&fid=9&type=rss");
		$rss->parse();
		$items = $rss->getItems();
		$items = array_slice($items, 0, min(count($items), 3));
		
		function filter($item) {
			return $item['title'] != "Archiv";
		}
		
		$items = array_filter($items, "filter");
		
		$len = count($items);
		
		for($i=0; $i < $len; $i++) {
			if(($timestamp = strtotime($items[$i]['pubdate'])) !== false) {
				$items[$i]['date'] = date("d.m.Y", $timestamp);
			}
			else {
				$match = array();
				
				if(preg_match("/(\d+ [a-zA-Z]+ \d+)/",
							  $items[$i]['pubdate'],
							  $match)) {
					$items[$i]['date'] = $match[1];
				}
				else {
					$items[$i]['date'] = 'Datum';
				}
			}
			
			$items[$i]['intro'] = substr($items[$i]['description'],
										 0,
										 strpos($items[$i]['description'], "</p>")+4);
			
			$items[$i]['link'] = str_replace("&action=new", "",      $items[$i]['link']);
			$items[$i]['link'] = str_replace("&",           "&amp;", $items[$i]['link']);
		}
		
		//print_r($items);
		
		file_put_contents($cache_file, serialize($items));
	}
	else {
		// CACHE HIT
		$items = unserialize(file_get_contents($cache_file));
	}
	
	$output->assign('news_items', $items);
	
	// seite darstellen
	$output->display('start.tpl');
?>