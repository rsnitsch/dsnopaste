<?php
	if(isset($_GET['key']) and isset($_GET['adminkey']))
	{
		$key = $_GET['key'];
		$akey = $_GET['adminkey'];
		setcookie("admin_$key", $akey);
	}
	else
	{
		echo "Parameter key oder adminkey nicht übertragen.";
	}
