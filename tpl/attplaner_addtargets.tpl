{extends 'layout.tpl'}
{block 'content'}

{if !empty($w_hinweis)}<p style="color: #FF2121; font-weight: bold;">{$w_hinweis}</p>{/if}

<p>Wähle den Angriffsplan aus, zu dem du die Dörfer/Ziele hinzufügen willst:
{if count($plans) > 0}
</p>

<form action="attplaner_addtargets.php" method="POST">
	<input type="hidden" name="targets" value="{$targets}" />


	<ul>
		{foreach from=$plans item=plan}
		<li>
			<input type="radio" name="planid" value="{$plan.id}" />
			<a href="attplaner.php?id={$plan.id}&amp;key={$plan.key}" target="_self">attplaner.php?id={$plan.id}&amp;key={$plan.key}</a>
		</li>
		{/foreach}
	</ul>

	<input type="submit" value="Hinzufügen" />



</form>
{else}
<br /><i>Sorry, keine Angriffspläne von dir gefunden!</i></p>
{/if}

<p>Oder erstelle einen neuen Angriffsplan für die Dörfer/Ziele:<br />
<a href="attplaner_addtargets.php?targets={$targets}&amp;planid=-1" target="_self" rel="nofollow">Erstellen</a></p>

{/block}
