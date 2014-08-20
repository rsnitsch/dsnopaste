{extends 'layout.tpl'}
{block 'head'}
{$smarty.block.parent}
	<script language="javascript" type="text/javascript" src="{$root_path}js/attplaner.js"></script>
{/block}

{block 'content'}
<script type="text/javascript">
{literal}// <![CDATA[
$(document).ready(function() {
	smartifyCoordInputs($("input[name=from_x]"), $("input[name=from_y]"));
	smartifyCoordInputs($("input[name=to_x]"), $("input[name=to_y]"));
	autoSwitchFocus($("input[name=from_y]"), $("input[name=to_x]"), 3);
});
// ]]>{/literal}
</script>

<div id="attplan_meta" class="tiny">
{if !empty($w_hinweis)}
	<p class="warnung">{$w_hinweis}</p>
{/if}

	<table>
		<tr>
			<th>
				Einfacher Link zu diesem Plan (<u>ohne</u> Bearbeitungsrechte):
			</th>
			<td>
				<a class="simple" href="{$link}">{$link}</a>
				(<a href="javascript:ap_normalLinkDescription()">Beschreibung</a>)
			</td>
		</tr>
		<tr>
			<th>
				Admin-Link zu diesem Plan (<u>mit</u> Bearbeitungsrechten):
			</th>
			<td>
{if !empty($admin_link)}
					<a class="simple" href="{$admin_link}">{$admin_link}</a>
{else}
					- diesen Link darfst du nur sehen, wenn du bereits Bearbeitungsrechte hast -
{/if}
				(<a href="javascript:ap_adminLinkDescription()">Beschreibung</a>)
			</td>
		</tr>
	</table>

{if $new_plan && $creator}
<div id="attplan_creator_notice">
	<p class="warnung">
		EMPFEHLUNG:
		Du solltest den Admin-Link zu deinem Angriffsplan jetzt zu deinen Lesezeichen hinzufügen!
		<a href="javascript:ap_adminLinkDescription()">(Warum?)</a>
		<br />
		<a href="{$link}">Diesen Hinweis ausblenden.</a>
	</p>
</div>
{/if}

	<p>
{if $creator}
		<span class="hinweis">Du darfst diesen Angriffsplan bearbeiten.</span>
{else}
		<span class="warnung">Du darfst diesen Angriffsplan NICHT bearbeiten.</span>
{/if}

{$delete}
	</p>

{if isset($noadmin) && $noadmin}
<div id="no_admin_notice">
	<script language="javascript" type="text/javascript">
		setTimeout("alert('Du hast diesen Angriffsplan nicht erstellt. Du bist nicht berechtigt Änderungen durchzuführen.');", 1000);
	</script>
	<noscript>
		<p class="warnung">Du hast diesen Angriffsplan nicht erstellt. Du bist nicht berechtigt Änderungen durchzuführen.</p>
	</noscript>
</div>
{/if}
</div>


<h3>Bisherige Angriffe/Unterstützungen</h3>


<form action="" method="post" name="attplan_form">
<input type="hidden" name="action" value="save" />

<table class="attplan" cellspacing="0">
	<tr>
		<th><a href="{$attplan_baseurl}&amp;order=from" target="_self">Startdorf</a></th>
		<th><a href="{$attplan_baseurl}&amp;order=to" target="_self">Zieldorf</a></th>
		<th><a href="{$attplan_baseurl}&amp;order=typ" target="_self">Typ</a></th>
		<th><a href="{$attplan_baseurl}&amp;order=senddate" target="_self">Abschickzeit</a></th>
		<th><a href="{$attplan_baseurl}&amp;order=arrive" target="_self">Ankunft</a></th>
		<th width="50">Notiz</th>
		{foreach from=$unitnames item=unitname}
		<th width="35"><img src="{$root_path}images/units/{$unitname}.png" alt="" /></th>
		{/foreach}
		<th>Aktionen</th>
		<th>Auswahl</th>
	</tr>
{foreach from=$actions item=action}
	<tr class="{cycle values="background_light,background_dark"}">
		<td><a class="setValue" href="javascript:ap_setCoord('from', {$action.from.x}, {$action.from.y})">{$action.from.orig}</a></td>
		<td><a class="setValue" href="javascript:ap_setCoord('to', {$action.to.x}, {$action.to.y})">{$action.to.orig}</a></td>
		<td>{if $action.typ==1}
				<img src="{$root_path}images/units/axe.png" alt="Axtkämpfer" title="Angriff" />
			{elseif $action.typ==2}
				<img src="{$root_path}images/units/sword.png" alt="Schwertkämpfer" title="Unterstützung" />
			{elseif $action.typ==3}
				<img src="{$root_path}images/blue.png" alt="Fake (Bild: blauer Kreis)" title="Fake" />
			{elseif $action.typ==4}
				<img src="{$root_path}images/units/snob.png" alt="Adelsgeschlecht" title="Adelsgeschlecht" />
			{/if}
		</td>
		<td{if $action.senddate < $timestamp} class="red"{/if}{if !empty($action.timeleft)} title="Noch: {$action.timeleft}"{/if}>{$action.send}</td>
		<td><a class="setValue" href="javascript:ap_setValue('arrival', '{$action.arrive_pure}')">{$action.arrive}</a></td>
		<td><textarea cols="10" rows="1">{$action.note|escape}</textarea></td>
{foreach from=$unitnames item=unitname}
			<td>{$action.$unitname}</td>
{/foreach}

		<td><a href="{$attplan_baseurl}&amp;edit={$action.id|escape:'url'}">Editieren</a> <a class="delete" href="{$attplan_baseurl}&amp;deleteatt={$action.id}" onclick="return ap_deleteConfirm()"><span>Löschen</span></a></td>

		<td><input type="checkbox" name="select_{$action.id}" value="1" {if count($selected) > 0 and array_search($action.id, $selected) !== false}checked="checked" {/if}/></td>
	</tr>
{/foreach}
</table>

<table border="0">
	<tr>
		<td>
			<select name="mass_edit_select">
				<option value="all">Alle Aktionen</option>
				<option value="all_marked" selected="selected">Alle markierten Aktionen</option>
				<option value="all_notmarked">Alle nicht markierten Aktionen</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>
			<input type="radio" id="mass_action_move" name="mass_action" value="addtime" checked="checked" />
			<label for="mass_action_move">verschieben</label> um
			<input type="text" size="1" name="daysplus" value="0" /> Tage
			<input type="text" size="1" name="hoursplus" value="0" /> Stunden
			<input type="text" size="1" name="minutesplus" value="0" /> Minuten
			<input type="text" size="1" name="secondsplus" value="0" /> Sekunden <span class="tiny">(auch negative Werte möglich)</span>
		</td>
	</tr>
	<tr>
		<td>
			<input type="radio" id="mass_action_delete" name="mass_action" value="delete" />
			<label for="mass_action_delete">löschen</label>
			<input type="checkbox" name="mass_action_delete_sure" value="1" />
			<span class="small">sicher!</span>
		</td>
	</tr>
	<tr>
		<td><input type="submit" name="mass_edit" value="OK" /></td>
	</tr>
</table>

<table border="0" class="valign_top">
	<tr>
		<td>
			<h3>Angriff/Unterstützung {$add_or_edit}</h3>
			<p class="tiny">WICHTIG: <a class="setValue">Unterstrichene</a> Werte bisheriger Aktionen können durch Anklicken ins Formular übernommen werden!</p>

			<table class="dsstyle">
				<tr>
					<th>Typ:</th>
					<td>
						<select id="attplaner_aktion_typ" name="typ">
							<option id="attplaner_aktion_typ_att" value="att"{if isset($typ) && $typ==1} selected="selected"{/if}>Angriff</option>
							<option id="attplaner_aktion_typ_snob" value="snob"{if isset($typ) && $typ==4} selected="selected"{/if}>Adelsgeschlecht</option>
							<option id="attplaner_aktion_typ_deff" value="deff"{if isset($typ) && $typ==2} selected="selected"{/if}>Unterstützung</option>
							<option id="attplaner_aktion_typ_fake" value="fake"{if isset($typ) && $typ==3} selected="selected"{/if}>Fake</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>Absendekoordinaten:</th>
					<td>x: <input type="text" name="from_x" value="{$from.x}" size="5" /> y: <input type="text" name="from_y" value="{$from.y}" size="5" /></td>
				</tr>
				<tr>
					<th>Zielkoordinaten:</th>
					<td>x: <input type="text" name="to_x" value="{$to.x}" size="5" /> y: <input type="text" name="to_y" value="{$to.y}" size="5" /></td>
				</tr>
				<tr>
					<th>Ankunft:</th>
					<td><input type="text" name="arrival" value="{$arrival}" /> (tt.mm.jjjj hh:mm:ss)</td>
				</tr>
				<tr>
					<th>Notiz:</th>
					<td><textarea cols="15" rows="2" name="note" onkeyup="this.value=this.value.substr(0,50);">{$note|escape}</textarea></td>
				</tr>
				<tr>
					<th>Einheiten:</th>
					<td>
						<table class="attplan">
							<tr>
								{foreach from=$unitnames item=unitname}
								<th><img src="{$root_path}images/units/{$unitname}.png" alt="" /></th>
								{/foreach}
							</tr>
							<tr>
								{foreach from=$unitnames item=unitname}
								<td><input type="text" name="add_{$unitname}"		value="{$units.$unitname}" size="2" /></td>
								{/foreach}
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
		<td>
			<h3>Notizen</h3>
			<textarea name="notes" cols="30" rows="6">{$notes|escape}</textarea><br />
			<input type="submit" name="save_notes" value="Speichern" />
		</td>
	</tr>
</table>

<br />
{if isset($oldid)}<input type="hidden" name="oldid" value="{$oldid}" />{/if}
<input type="submit" name="{$action_attack}" value="Speichern" />
</form>

{/block}
