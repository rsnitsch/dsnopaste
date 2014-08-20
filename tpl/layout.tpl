<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
{block 'head'}
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" href="{$root_path}images/favicon.ico" />
	<title>DS NoPaste - {$title}</title>
	<meta name="author" content="Robert 'bmaker' Nitsch" />
	<meta name="keywords" content="browsergame, angriffsplaner, planer, farmmanager, manager, tools, stämme, staemme, diestämme, diestaemme, tribal wars" />
	<link rel="stylesheet" type="text/css" href="{$root_path}styles.css" />
{if $cfg.debugmode}
	<script language="javascript" type="text/javascript" src="{$root_path}js/jquery-2.0.1.js"></script>
{else}
	<script language="javascript" type="text/javascript" src="{$root_path}js/jquery-2.0.1.min.js"></script>
{/if}
	<script language="javascript" type="text/javascript" src="{$root_path}js/general.js"></script>
{/block}
</head>

<body>

{if strlen($global_announcing) > 0}
	{include file='announcing.tpl'}
{/if}

<div id="dsstyle_body">

<h1><a href="{$root_path}index.php" target="_self">DS NoPaste</a> - {$title}</h1>

<noscript><p class="warnung">WICHTIG: Bitte aktiviere Javascript! Ansonsten kannst du einige Funktionen von NoPaste nicht nutzen!</p></noscript>

{block 'content'}---Platzhalter---{/block}

{block 'footer'}
<div id="footer">
	<br />
	<hr />

	<table align="center" border="0">
		<tr>
			<td align="left" width="200">
				<p>
					<a href="{$root_path}spenden.php">
						<img src="{$root_path}/images/paypal_spenden.gif" border="0" alt="Spenden" />
					</a>
				</p>
			</td>
			<td align="center">
			</td>
			<td align="right" width="300">
				<p id="copyright">
					&copy; copyright by <a href="http://www.robertnitsch.de">Robert Nitsch</a>, 2006-2014.<br />
					<a href="http://feedback.np.bmaker.de/">Feedback</a> oder Mail an<br /> <i>battlemaker ät web punkt de</i><br />
					<a href="{$root_path}legal.php" target="_self">Impressum</a><br />
{if $cfg.uploaded && !$cfg.debugmode}
{literal}
					<!-- Piwik -->
					<script type="text/javascript" href="{$root_path}js/analytics.js"></script><noscript><p><img src="http://anal.robertnitsch.de/piwik/piwik.php?idsite=1" border="0" alt="" /></p></noscript>
					<!-- End Piwik Tracking Code -->
{/literal}
{/if}
				</p>
			</td>
		</tr>
	</table>
</div>
{/block}

</div>

</body>
</html>
