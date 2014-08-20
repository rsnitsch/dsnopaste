<?php
	$csp_rules = "default-src 'none'; script-src 'self' anal.robertnitsch.de; style-src 'self'; img-src 'self'";

	foreach (array("X-WebKit-CSP", "X-Content-Security-Policy", "Content-Security-Policy") as $csp)
	{
		header($csp . ": " . $csp_rules);
	}
