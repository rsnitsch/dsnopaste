<?php
	$c = file_get_contents("php://input");

	if (!$c)
		exit;
	   
	$c = json_decode($c, true);
	$c = print_r($c, true);

	file_put_contents("data/log/csp_errors.log", "Date: ".date("Y-m-d H:i:s")."\nReport:\n".$c."\n", FILE_APPEND);
