$(document).ready(function() {
	var pkBaseURL = (("https:" == document.location.protocol) ? "https://anal.robertnitsch.de/piwik/" : "http://anal.robertnitsch.de/piwik/");

	try {
		var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", 1);
		piwikTracker.trackPageView();
		piwikTracker.enableLinkTracking();
	} catch( err ) {
		if (window.console) console.log(err);
	}
});
