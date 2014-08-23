<?php
	$csp_rules = "default-src 'none'; script-src 'self' 'unsafe-eval' anal.robertnitsch.de; style-src 'self'; img-src 'self' anal.robertnitsch.de; frame-src anal.robertnitsch.de; child-src anal.robertnitsch.de; report-uri ".server_url()."/csp_report.php";

	foreach (array("X-WebKit-CSP", "X-Content-Security-Policy", "Content-Security-Policy") as $csp)
	{
		header($csp . ": " . $csp_rules);
	}
