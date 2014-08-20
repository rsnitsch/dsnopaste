{extends 'layout.tpl'}
{block 'content'}

{if !empty($w_hinweis)}<p class="warnung">{$w_hinweis}</p>{/if}

<p>Dies ist der Angriffsplaner von NoPaste. Du kannst hier einen genauen Angriffsplan mit mehreren Dörfern erstellen und
ihn dann über einen Link deinem Stamm zeigen. Angriffspläne werden i.d.R. einen Monat lang gespeichert.</p>


<h3>Angriffsplan erstellen</h3>
<p>Du wirst mit einem Cookie als Ersteller gekennzeichnet und kannst als einziger den Angriffsplan editieren!</p>
<p>Je nachdem, welchen Server du auswählst werden andere Laufzeiten für die Berechnungen verwendet.</p>

<form action="attplaner.php" method="post">
	<input type="hidden" name="create" value="1" />
	Für...
	<select name="server">
{foreach from=$servers item=server}
		<option value="{$server.id}">{$server.name}</option>
{/foreach}
	</select>

	<input type="submit" value="Erstellen" />
</form>

<p class="bold"><span class="red">Wichtig: </span>Angriffspläne, die keine Aktionen haben, werden spätestens nach 24 Stunden gelöscht!</p>

{/block}
