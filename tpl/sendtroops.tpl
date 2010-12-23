<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" href="http://www.die-staemme.de/favicon.ico" />
	<title>DS NoPaste - {$title}</title>
	<meta name="author" content="Robert 'bmaker' Nitsch" />
	<meta name="keywords" content="robert nitsch, bmaker, nopaste, tool, paste, die stämme, diestämme, die staemme, diestaemme, tribalwars, tribal wars, online" />
</head>

<frameset cols="*, 0" framespacing="0" frameborder="0" border="0">
	<frame frameborder="0" marginheight="5" marginwidth="5" border="0" src="sendtroops.php?form=1&amp;world={$world|urlencode}&amp;from={$from|intval}&amp;to={$to|intval}{foreach from=$troops item=unit}&amp;{$unit.unitname|urlencode}={$unit.count|intval}{/foreach}" name="main" />
	<frame marginwidth="7" marginheight="0" frameborder="0" scrolling="no" src="http://www.example.com" name="dummy" />
</frameset>

</html>