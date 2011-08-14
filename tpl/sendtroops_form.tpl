{include file='header.tpl'}

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

<p>
ACHTUNG: Damit das Formular im Versammlungsplatz automatisch ausgefüllt werden kann musst du dieses Greasemonkey-Script installieren:
<a href="http://np.bmaker.net/misc/sendtroopshelper.user.js">sendtroopshelper.user.js</a>
</p>

</div>

</body>
</html>