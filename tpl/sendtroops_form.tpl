{extends 'layout.tpl'}
{block 'content'}

<form action="{$world_url}/game.php" method="get" name="sendtroops" id="form_sendtroops" target="_self">
	<input type="hidden" name="village" value="{$from|intval}" />
	<input type="hidden" name="screen" value="place" />
	<input type="hidden" name="x" value="{$to_x|intval}" />
	<input type="hidden" name="y" value="{$to_y|intval}" />
	{foreach from=$troops item=unit}
	<input type="hidden" name="{$unit.unitname|urlencode}" value="{$unit.count|intval}" />
	{/foreach}
	<input type="hidden" name="attack" value="Angreifen" />
	<input type="submit" value="Versammlungplatz öffnen" />
</form>


<h2>Wichtig: Userscripts installieren</h2>

<p>
	Für dieses Feature müssen die folgenden Greasemonkey-Userscripts installiert werden:
</p>

<table>
	<tr>
		<th>Name</th>
		<th>Beschreibung</th>
		<th>Installations-Link</th>
	</tr>
	<tr>
		<td>Farmmanager - Erweiterung II (SendTroopsHelper)</td>
		<td>Die Truppen im Versammlungsplatz werden automatisch ausgefüllt.</td>
		<td><a href="http://scripts.die-staemme.de/download/50.user.js">Installieren</a></td>
	</tr>
	<tr>
		<td>Farmmanager Popup Überspringer</td>
		<td>Der Button 'Versammlungsplatz öffnen' (siehe oben) wird automatisch geklickt.</td>
		<td><a href="https://bitbucket.org/rsnitsch/dsnopaste-userscripts/raw/f4262c052a469101d40fec475e912707954dfe86/farmmanager-popup-ueberspringer.user.js">Installieren</a></td>
	</tr>
</table>

{/block}

{block 'footer'}{/block}
