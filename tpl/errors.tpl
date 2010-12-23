{* gibt alle Fehlermeldungen aus *}
<p>Folgende Fehler sind aufgetreten:</p>
{if count($error)>0}
	<ul style="color: #DD2121;">
	{foreach from=$error item=msg}
		<li>{$msg}</li>
	{/foreach}
	</ul>
{/if}