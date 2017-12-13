<!DOCTYPE html>
<html>
<head>
{block 'head'}
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" href="{$root_path}images/favicon.ico" />
	<title>DS NoPaste - {$title}</title>
	<meta name="author" content="Robert 'bmaker' Nitsch" />
	<meta name="keywords" content="browsergame, angriffsplaner, planer, farmmanager, manager, tools, stämme, staemme, diestämme, diestaemme, tribal wars" />
	<link rel="stylesheet" type="text/css" href="{$root_path}css/styles.css" />
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

	<a href="https://bitbucket.org/rsnitsch/dsnopaste">DS NoPaste wird auf Bitbucket.org entwickelt</a>
</div>
{/block}

</div>

</body>
</html>
