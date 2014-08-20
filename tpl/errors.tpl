{* gibt alle Fehlermeldungen aus *}
<div id="errors">
<p>Folgende Fehler sind aufgetreten:</p>
{if count($error)>0}
	<ul>
	{foreach from=$error item=msg}
		<li>{$msg}</li>
	{/foreach}
	</ul>
</div>
{/if}