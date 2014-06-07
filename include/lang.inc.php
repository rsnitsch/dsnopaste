<?php
	// Translate
	function t($str) {
		global $lang;
		
		if (isset($lang[$str]))
			return $lang[$str];
		else
			return $str;
	}
	
	// Plurals
	function tp($str1, $str2, $n) {
		return t($str1); // TODO
	}
	
	// Smarty adapters
	function t_smarty($params, $content, $smarty, &$repeat) {
		if (isset($content)) {
			return t($content);
		}
	}
	
	function tp_smarty($params, $content, $smarty, &$repeat) {
		return t_smarty($params, $content, $smarty, $repeat); // TODO
	}
	
	require_once($root_path.'/include/lang/de.php');
	