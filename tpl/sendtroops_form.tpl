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


<h2>Wichtig: Farmmanager - Erweiterung II (sendtroopshelper) installieren</h2>

<p>
	Für dieses Feature muss das Userscript <strong>Farmmanager - Erweiterung II (sendtroopshelper)</strong> installiert
	werden.
</p>

<p>
	Jetzt <a href="http://scripts.die-staemme.de/download/50.user.js">Farmmanager - Erweiterung II installieren</a>
	(aus der <a href="http://scripts.die-staemme.de/">Script-DB</a>).
<p>

<h3>Was macht das Script?</h3>

<p>Das Script erfüllt die folgenden Funktionen:</p>

<ul>
	<li>Die Truppen im Versammlungsplatz werden automatisch ausgefüllt.</li>
	<li>
		Dieses Popup wird automatisch übersprungen; es öffnet sich also direkt
		der Versammlungsplatz, ohne dass man auf "Versammlungsplatz öffnen" klicken muss.
	</li>
</ul>
{/block}

{block 'footer'}{/block}
