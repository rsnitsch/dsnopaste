{include file='header.tpl'}

<p>Dies ist der Farmmanager. Hier kannst du bis zu 1000 Farmen verwalten und überwachen.</p>

<p>Eine genauere Beschreibung findest du hier:
<a href="http://forum.die-staemme.de/showthread.php?t=83974">http://forum.die-staemme.de/showthread.php?t=83974</a></p>

<h2>Farmmanager erstellen</h2>

<form action="farmmanager.php?action=create" method="post">
    Für...
    
	<select name="server">
	{foreach from=$activated_servers item=server}
       	<option value="{$server.id}">{$server.name}</option>
   	{/foreach}
	</select>
	
	<input type="submit" value="Erstellen" />
</form>

<p style="font-weight: bold;"><span style="color: #DD2121;">Wichtig: </span>Ungenutzte (=leere) Farmmanager werden spätestens nach 24 Stunden gelöscht!</p>


{include file='footer.tpl'}