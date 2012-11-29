{include file='header.tpl'}

{if $cfg.debugmode}

	{* falls der Eintrag eingelesen werden sollte aber ein Fehler auftritt *}
	{if $add and !$added}
		{if count($error)>0}
			<p>Dein Eintrag konnte nicht eingelesen werden.</p>
			<p>Folgende Fehler sind aufgetreten:</p>
			<ul style="color: #DD2121;">
			{foreach from=$error item=msg}
				<li>{$msg}</li>
			{/foreach}
			</ul>
			
			<p><a href="javascript:history.back()">Zurück</a></p>
		{/if}
	{elseif $add and $added}
		{* wenn der Eintrag erfolgreich eingelesen wurde *}
		<p>Dein Eintrag wurde erfolgreich eingelesen.</p>
		<p>Du kannst ihn unter dem Link <a href="{$rootpath}{$added}" target="_self">{$serverpath}{$added}</a> jederzeit abrufen.</p>
	{else}
		{* wenn noch keine Daten übertragen wurden *}
		{include file='einleseformular.tpl'}
	{/if}
	
	{* DEBUG INFORMATIONEN *}
	{if count($debuginfo)>0}
		<p>Folgende Debuginformationen wurden generiert:</p>
		<ul style="color: #DD2121;">
		{foreach from=$debuginfo item=msg}
			<li>{$msg}</li>
		{/foreach}
		</ul>
	{/if}

{else}
Dieser Service ist momentan deaktiviert. Evtl. werden Wartungsarbeiten durchgeführt.
{/if}

{include file='footer.tpl'}