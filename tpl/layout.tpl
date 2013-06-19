<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" href="http://www.die-staemme.de/favicon.ico" />
	<title>DS NoPaste - {$title}</title>
	<meta name="author" content="Robert 'bmaker' Nitsch" />
	<meta name="keywords" content="browsergame, angriffsplaner, planer, farmmanager, manager, tools, stämme, staemme, diestämme, diestaemme, tribal wars" />
	<link rel="stylesheet" type="text/css" href="{$root_path}styles.css" />
</head>

<body>

<!-- Javascripts -->
{if $cfg.debugmode}
<script language="javascript" type="text/javascript" src="{$root_path}js/jquery-2.0.1.js"></script>
{else}
<script language="javascript" type="text/javascript" src="{$root_path}js/jquery-2.0.1.min.js"></script>
{/if}
<script language="javascript" type="text/javascript" src="{$root_path}js/javascript.js"></script>
<!-- /Javascripts -->

{if strlen($global_announcing) > 0}
    {include file='announcing.tpl'}
{/if}

<div id="dsstyle_body">

<h1><a href="{$root_path}index.php" target="_self">DS NoPaste</a> - {$title}</h1>

<noscript><p class="warnung">WICHTIG: Bitte aktiviere Javascript! Ansonsten kannst du einige Funktionen von NoPaste nicht nutzen!</p></noscript>

{block 'content'}---Platzhalter---{/block}

{block 'footer'}
	<br />
	<hr style="margin-top: 20px;" />
	
	<table align="center" border="0" style="vertical-align: bottom; width: 100%;">
	 <tr>
	  <td align="left" width="200">
	   <p>
		<a href="{$root_path}spenden.php">
		 <img src="https://www.paypal.com/de_DE/DE/i/btn/btn_donateCC_LG.gif" border="0" alt="Spenden" />
		</a>
	   </p>
	  </td>
	  <td align="center">
	   <p>
	   <!-- Host Europe GmbH - Partnerprogramm - Beginn -->
	   <a href="http://affiliate.hosteurope.de/click.php/A4pg0anFsjrLViT5e6npwgs4L2OiQ9Fb9vE4FHofakY," title="powered by Host Europe" target="_blank">powered by Host Europe</a><img src="http://affiliate.hosteurope.de/view.php/A4pg0anFsjrLViT5e6npwgs4L2OiQ9Fb9vE4FHofakY," width="0" height="0" alt="" border="0" />
	   <!-- Host Europe GmbH - Partnerprogramm - Ende -->
	   </p>
	   <p>
	   <!-- Host Europe GmbH - Partnerprogramm - Beginn -->
	   <a href="http://affiliate.hosteurope.de/click.php/k1E4wPXWQAfkpIihQEyAkcu-Fs3bg_H3v36iigEWbZQ," target="_blank"><img src="http://affiliate.hosteurope.de/view.php/k1E4wPXWQAfkpIihQEyAkcu-Fs3bg_H3v36iigEWbZQ," width="468" height="60" alt="Full Banner" border="0" /></a>
	   <!-- Host Europe GmbH - Partnerprogramm - Ende -->
	   </p>
	  </td>
	  <td align="right" width="300">
		<p id="copyright">
			&copy; copyright by <a href="http://www.robertnitsch.de">Robert Nitsch</a>, 2006-2012.<br />
			<a href="http://feedback.np.bmaker.net/">Feedback</a> oder Mail an<br /> <i>battlemaker ät web punkt de</i><br />
			<a href="{$root_path}anb.php" target="_self">ANBs/Impressum</a><br />
	{if $cfg.uploaded && !$cfg.debugmode}			
			{literal}
			<!-- Piwik --> 
			<script type="text/javascript">
			var pkBaseURL = (("https:" == document.location.protocol) ? "https://anal.robertnitsch.de/piwik/" : "http://anal.robertnitsch.de/piwik/");
			document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
			</script><script type="text/javascript">
			try {
			var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", 1);
			piwikTracker.trackPageView();
			piwikTracker.enableLinkTracking();
			} catch( err ) {}
			</script><noscript><p><img src="http://anal.robertnitsch.de/piwik/piwik.php?idsite=1" style="border:0" alt="" /></p></noscript>
			<!-- End Piwik Tracking Code -->
			{/literal}
	{/if}
		</p>
	  </td>
	 </tr>
	</table>
{/block}

</div>

</body>
</html>
