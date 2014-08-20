<?php
	$csp_rules = "default-src 'none'; script-src 'self' anal.robertnitsch.de; style-src 'self'; img-src 'self'; frame-src anal.robertnitsch.de; child-src anal.robertnitsch.de";

	foreach (array("X-WebKit-CSP", "X-Content-Security-Policy", "Content-Security-Policy") as $csp)
	{
		header($csp . ": " . $csp_rules);
	}
