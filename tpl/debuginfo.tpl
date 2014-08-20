{* gibt alle Debugmeldungen aus *}
{if count($debuginfo)>0}
<div id="debug_info">
	<p>Debuginformationen:</p>
	<ul>
	{foreach from=$debuginfo item=msg}
		<li>{$msg}</li>
	{/foreach}
	</ul>
</div>
{/if}