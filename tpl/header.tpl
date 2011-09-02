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
{if $debugmode}
<script language="javascript" type="text/javascript" src="{$root_path}js/jquery-1.3.2.js"></script>
{else}
<script language="javascript" type="text/javascript" src="{$root_path}js/jquery-1.3.2.min.js"></script>
{/if}
<script language="javascript" type="text/javascript" src="{$root_path}js/javascript.js"></script>
<!-- /Javascripts -->

{include file='werbung.tpl'}

{if strlen($global_announcing) > 0}
    {include file='announcing.tpl'}
{/if}

{if $dsg_eselsohr}
<div id="corner_small" style="background-image: url('http://dsg.gfx-dose.de/res/corner_small.png'); position: absolute; top: 0px; right: 0px; width: 80px; height: 80px; cursor: pointer; z-index: 99999" onclick="window.location.href = 'http://dsg.gfx-dose.de'" onmouseover="document.getElementById('corner_large').style.display = 'block';"></div>
<div id="corner_large" style="display: none;background-image: url('http://dsg.gfx-dose.de/res/corner_large.png'); position: absolute; top: 0px; right: 0px; width: 600px; height: 600px;cursor: pointer; z-index: 100001" onclick="window.location.href = 'http://dsg.gfx-dose.de'" onmouseout="this.style.display = 'none';"></div>
{/if}

<div id="dsstyle_body">

<h1><a href="{$root_path}index.php" target="_self">DS NoPaste</a> - {$title}</h1>

<noscript><p class="warnung">WICHTIG: Bitte aktiviere Javascript! Ansonsten kannst du einige Funktionen von NoPaste nicht nutzen!</p></noscript>
