{extends 'layout.tpl'}
{block 'head'}
{$smarty.block.parent}
	<script language="javascript" type="text/javascript" src="{$root_path}js/farmmanager.js"></script>
{/block}

{block 'content'}
<div id="farmmanager">

<div class="tiny">
	<p>
		Link zu diesem Farmmanager (bitte abspeichern):
		<a class="simple" href="{$server_url}/tools/farmmanager.php?id={$saveid}">
			{$server_url}/tools/farmmanager.php?id={$saveid}
		</a>
	</p>
</div>

{if !isset($edited_farm)}
<h3><a id="toggle_form" href="">Farmbericht/Spähbericht einlesen</a></h3>

<div id="form">
	<form action="farmmanager.php?id={$saveid}&amp;mode={$mode}" method="post">
	<input type="hidden" name="parse" value="1" />
	<table>
		<tr>
			<th colspan="3">Farmbericht einfügen (ab einschließlich Betreff):</th>
		</tr>
		<tr>
			<td colspan="3"><textarea id="report" name="report" rows="3"></textarea></td>
		</tr>
		<tr>
			<th>Notiz zu dieser Farm:</th>
			<th>Erspähte Ressourcen:</th>
			<th>Bonusdorf?</th>
		</tr>
		<tr>
			<td><input type="text" size="30" name="note" value="" /></td>
			<td>
				<input type="checkbox" name="wood" value="yes" checked="checked" /><img src="{$root_path}images/holz.png" alt="Holz" />
				<input type="checkbox" name="loam" value="yes" checked="checked" /><img src="{$root_path}images/lehm.png" alt="Lehm" />
				<input type="checkbox" name="iron" value="yes" checked="checked" /><img src="{$root_path}images/eisen.png" alt="Eisen" />
			</td>
			<td>
				<select id="bonus_select" name="bonus">
					<option value="" selected="selected">- keine Angabe (alter Wert wird beibehalten) -</option>
					<option value="none">keiner dieser Boni</option>
					<option id="bonus_select_all" value="all">+{$bonus_res_all}% erhöhte Rohstoffproduktion</option>
					<option id="bonus_select_wood" value="wood">+{$bonus_res_one}% erhöhte Holzproduktion</option>
					<option id="bonus_select_loam" value="loam">+{$bonus_res_one}% erhöhte Lehmproduktion</option>
					<option id="bonus_select_iron" value="iron">+{$bonus_res_one}% erhöhte Eisenproduktion</option>
					{if $bonus_new}<option id="bonus_select_storage" value="storage">+50% Speicher-Volumen</option>{/if}
				</select>
			</td>
		</tr>
	</table>
	<input id="parse_report_button" type="submit" value="OK" />
	</form>
</div>
{else}
<h3><a id="toggle_form" href="">Farm bearbeiten</a></h3>

<div id="form">
	<form action="farmmanager.php?id={$saveid}&amp;mode={$mode}" method="post">
	<input type="hidden" name="edit" value="1" />
	<input type="hidden" name="id" value="{$edited_farm.id}" />
	<table>
		<tr>
			<th>Notiz zu dieser Farm:</th>
			<th>Bonusdorf?</th>
		</tr>
		<tr>
			<td><input type="text" size="30" name="note" value="{$edited_farm.note|escape}" /></td>
			<td>
				<select id="bonus_select" name="bonus">
					<option value="none" {if $edited_farm.bonus=='none'} selected="selected"{/if}>keiner dieser Boni</option>
					<option id="bonus_select_all" value="all" {if $edited_farm.bonus=='all'} selected="selected"{/if}>+{$bonus_res_all}% erhöhte Rohstoffproduktion</option>
					<option id="bonus_select_wood" value="wood" {if $edited_farm.bonus=='wood'} selected="selected"{/if}>+{$bonus_res_one}% erhöhte Holzproduktion</option>
					<option id="bonus_select_loam" value="loam" {if $edited_farm.bonus=='loam'} selected="selected"{/if}>+{$bonus_res_one}% erhöhte Lehmproduktion</option>
					<option id="bonus_select_iron" value="iron" {if $edited_farm.bonus=='iron'} selected="selected"{/if}>+{$bonus_res_one}% erhöhte Eisenproduktion</option>
					{if $bonus_new}<option id="bonus_select_storage" value="storage" {if $edited_farm.bonus=='storage'} selected="selected"{/if}>+50% Speicher-Volumen</option>{/if}
				</select>
			</td>
		</tr>
	</table>
	<input id="parse_report_button" type="submit" value="Speichern" />
	</form>
</div>
{/if}

<h3>Farmenübersicht</h3>

	<form id="update_settings" name="update_settings" action="" method="POST">
		<input type="hidden" name="id" value="{$saveid}" />
		
		<p>
		Herkunftsdorf auswählen:
		<select name="source_village">
			<option value="all"{if !$source_village} selected="selected"{/if}>- ALLE -</option>
			{if count($att_villages)>0}
			{foreach from=$att_villages item=av}
			<option value="{$av.av_coords}"{if $source_village==$av.av_coords} selected="selected"{/if}>{$av.av_coords}, {$av.av_name}</option>
			{/foreach}
			{/if}
		</select>
		</p>
		
		<span class="bold">Farmen filtern:</span><br />
		
		<table>
			<tr>
				<td><label for="filter_source_village">Nach Herkunftsdorf filtern</label></td>
				<td><input type="checkbox" id="filter_source_village" name="filter_source_village" value="yes" {if $filter_source_village}checked="checked" {/if}/></td>
			</tr>
			<tr>
				<td>Mindest-Ressourcen</td>
				<td><input type="text" name="filter_min_ress" value="{$filter_min_ress}" /></td>
			</tr>
		</table>
		<input type="submit" value="Aktualisieren" />
	</form>
	
	<p>
		{if $mode != 'default'}<a href="farmmanager.php?id={$saveid}&amp;mode=default">Zeige Ressourcen</a>{else}<span class="bold">Zeige Ressourcen</span>{/if}
		|
		{if $mode != 'buildings'}<a href="farmmanager.php?id={$saveid}&amp;mode=buildings">Zeige Gebäude</a>{else}<span class="bold">Zeige Gebäude</span>{/if}
	</p>
	
	{if count($farms) > 0}
	<table cellspacing="0" cellpadding="3" id="farmmanager_farms">
		<tr>
			<th><a href="farmmanager.php?id={$saveid}&amp;order=v_coords&amp;mode={$mode}">XXX|YYY</a></th>
			<th>Dorf</th>
			<th>Bonus</th>
			{if $source_village}<th><a href="farmmanager.php?id={$saveid}&amp;order=distance&amp;mode={$mode}">Entfernung</a></th>{/if}
			{if $mode == 'default'}
			<th class="align_right">
				<a href="farmmanager.php?id={$saveid}&amp;order=c_wood&amp;mode={$mode}" title="Nach Holz sortieren"><img src="{$root_path}images/holz.png" alt="Holz" /></a>
			</th>
			<th class="align_right">
				<a href="farmmanager.php?id={$saveid}&amp;order=c_loam&amp;mode={$mode}" title="Nach Lehm sortieren"><img src="{$root_path}images/lehm.png" alt="Lehm" /></a>
			</th>
			<th class="align_right">
				<a href="farmmanager.php?id={$saveid}&amp;order=c_iron&amp;mode={$mode}" title="Nach Eisen sortieren"><img src="{$root_path}images/eisen.png" alt="Eisen" /></a>
			</th>
			<th class="c_sum"><a href="farmmanager.php?id={$saveid}&amp;order=c_sum&amp;mode={$mode}"><abbr title="Ressourcen">Ress.</abbr></a></th>
			<th> / </th>
			<th><a href="farmmanager.php?id={$saveid}&amp;order=storage&amp;mode={$mode}">Speicher</a> (<a href="farmmanager.php?id={$saveid}&amp;order=fill_level" title="relativer Füllstand des Speichers">XX%</a>)</th>
			<th>Wall</th>
			<th>Performance</th>
			{elseif $mode == 'buildings'}
				{foreach from=$buildings item=building}
					<th><img src="{$root_path}images/buildings/{$building}1.png" title="{t}{$building}{/t}" /></th>
				{/foreach}
			{/if}
			<th><a href="farmmanager.php?id={$saveid}&amp;order=lastreport&amp;mode={$mode}">Letzter Bericht</a></th>
			<th>Notiz</th>
			<th class="align_center">Markierung</th>
			<th class="align_center">Truppen schicken</th>
			<th class="align_center">Bearbeiten</th>
		</tr>
	{foreach from=$farms item=farm}{if !$farm.filter}
		<tr class="{cycle values="background_light,background_dark"}{if $farm.farmed} green{/if}">
			<td>{$farm.v_coords}</td>
			<td>{$farm.v_name}</td>
			<td>
				{if $farm.bonus != 'none'}
					{if $farm.bonus == 'all'}<img src="{$root_path}images/storage.png" title="+3% erhöhte Rohstoffproduktion" alt="see title" />{/if}
					{if $farm.bonus == 'storage'}<img src="{$root_path}images/storage.png" title="+50% Speicher-Volumen" alt="see title" />{/if}
					{if $farm.bonus == 'wood'}<img src="{$root_path}images/holz.png" title="+10% erhöhte Holzproduktion" alt="see title" />{/if}
					{if $farm.bonus == 'loam'}<img src="{$root_path}images/lehm.png" title="+10% erhöhte Lehmproduktion" alt="see title" />{/if}
					{if $farm.bonus == 'iron'}<img src="{$root_path}images/eisen.png" title="+10% erhöhte Eisenproduktion" alt="see title" />{/if}
				{else}
					-
				{/if}
			</td>
			{if $source_village}<td>{$farm.distance}</td>{/if}
			{if $mode == 'default'}
			<td class="align_right">
				{$farm.c_wood} <img src="{$root_path}images/holz.png" alt="Holz" />
			</td>
			<td class="align_right">
				{$farm.c_loam} <img src="{$root_path}images/lehm.png" alt="Lehm" />
			</td>
			<td class="align_right">
				{$farm.c_iron} <img src="{$root_path}images/eisen.png" alt="Eisen" />
			</td>
			<td class="c_sum align_left{if $farm.c_sum>=$farm.storage_max} red{/if}">{$farm.c_sum}</td>
			<td> / </td>
			<td class="align_left{if $farm.c_sum>=$farm.storage_max} red{/if}">{$farm.storage_max} ({$farm.fill_level}%)</td>
			<td>{if $farm.b_wall >= 5}<span class="warnung building_warn">St. {$farm.b_wall}</span>{else}St. {$farm.b_wall}{/if}</td>
			<td>{if $farm.performance === null}-{else}{$farm.performance_percentage}%{/if}</td>
			{elseif $mode == 'buildings'}
				{foreach from=$buildings item=building}
					{if is_null($farm["b_$building"]) || !isset($buildings_max_levels[$building]) || $farm["b_$building"] <= $buildings_max_levels[$building]}
					<td class="align_center">{if is_null($farm["b_$building"])} ? {else} {$farm["b_$building"]} {/if}</td>
					{else}
						{if $building == 'barracks' || $building == 'wall'}
							<td class="align_center"><span class="warnung building_warn">{$farm["b_$building"]}</span></td>
						{else}
							<td class="align_center bold red">{$farm["b_$building"]}</td>
						{/if}
					{/if}
				{/foreach}
			{/if}
			<td>{$farm.time|date_format:"%d.%m. %H:%M Uhr"}</td>
			<td>{$farm.note|escape}</td>
			<td class="align_center">
				<!-- Als gefarmt markieren bzw. die Umkehrung -->
				<a class="image_link" href="farmmanager.php?id={$saveid}&amp;farmed={$farm.id}&amp;mode={$mode}">
					<img src="{if $farm.farmed}{$root_path}images/unit_axe_arrowup.png{else}{$root_path}images/units/axe.png{/if}" title="Als gefarmt markieren bzw. Markierung aufheben" alt="Axt" />
				</a>
			</td>
			<td class="align_center">
				{if isset($source_village_id)}
					{if !$farm.farmed}
						{if $farm.v_id != 0}
							<!-- Späher schicken (semi-automatisch) und als gefarmt markieren -->
							<a class="image_link sendtroops" href="farmmanager.php?id={$saveid}&amp;farmed={$farm.id}&amp;mode={$mode}" data-world_id="{$world_id}" data-from="{$source_village_id}" data-to="{$farm.v_id}" data-units="spy={$farm.spy_count}">
								<img src="{$root_path}images/units/spy.png" title="{$farm.spy_count}" alt="Späher" />
							</a>
							&nbsp;
							
							{foreach from=$farm.sendtroop_actions item=action}
							<a class="image_link sendtroops" href="farmmanager.php?id={$saveid}&amp;farmed={$farm.id}&amp;mode={$mode}" data-world_id="{$world_id}" data-from="{$source_village_id}" data-to="{$farm.v_id}" data-units="spy={$action.spy_count}&{$action.unit}={$action.unit_count}">
								<img src="{$root_path}images/units/{$action.unit}.png" title="{$action.unit_count}" />
							</a>
							&nbsp;
							{/foreach}
						{else}
						<em>Dorf-ID unbekannt.</em>
						{/if}
					{else}
					<em>Schon unterwegs!</em>
					{/if}
				{else}
				<em>Kein Herkunftsdorf ausgewählt!</em>
				{/if}
			</td>
			<td class="align_center">
				<!-- Bearbeiten Farm -->
				<a class="image_link" href="farmmanager.php?id={$saveid}&amp;edit={$farm.id}&amp;mode={$mode}">
					<img src="{$root_path}images/icon_text.gif" border="0" title="Bearbeiten" alt="Text-Icon" width="20" height="20" />
				</a>
				&nbsp;
				<!-- Löschen der Farm -->
				<a class="image_link delete_farm" href="farmmanager.php?id={$saveid}&amp;delete={$farm.id}&amp;mode={$mode}">
					<img src="{$root_path}images/delete.png" title="Löschen" alt="Kreuz-Icon" />
				</a>
			</td>
		</tr>
	{/if}{/foreach}
		<tr class="bold brown_bg">
			<td>Summe</td>
			<td>-</td>
			<td>-</td>
			{if $source_village}<td>-</td>{/if}
			{if $mode == 'default'}
			<td class="tiny align_right">
				{$total_wood} <img src="{$root_path}images/holz.png" width="11" height="11" alt="Holz" />
			</td>
			<td class="tiny align_right">
				{$total_loam} <img src="{$root_path}images/lehm.png" width="11" height="11" alt="Lehm" />
			</td>
			<td class="tiny align_right">
				{$total_iron} <img src="{$root_path}images/eisen.png" width="11" height="11" alt="Eisen" />
			</td>
			<td class="tiny" class="c_sum">{$total_sum}</td>
			<td> / </td>
			<td class="tiny">{$total_storage}</td>
			<td>-</td>
			<td>-</td>
			{elseif $mode == 'buildings'}
				{foreach from=$buildings item=building}
					<td>-</td>
				{/foreach}
			{/if}
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
		</tr>
	</table>
	{else}
	<p class="italic">Noch keine Farmen erfasst!</p>
	{/if}

<p>Gefarmt: <span class="green">{$count_farmed}</span>/{$total_farms}</p>

<h3>Truppenbuttons konfigurieren</h3>

<p>Wähle die Einheiten aus, die in der Spalte <em>"Truppen schicken"</em> aufgeführt werden sollen:</p>

<form action="farmmanager.php?id={$saveid}&amp;mode={$mode}" method="post">
	{foreach from=$units_with_carry item=unit}
	<label for="sendtroops_{$unit}"><img src="{$root_path}images/units/{$unit}.png" /></label>
	<input type="checkbox" id="sendtroops_{$unit}" name="sendtroops_{$unit}" value="yes" {if in_array($unit, $sendtroops_units)}checked="checked"{/if}/>
	&nbsp;
	{/foreach}
	<input type="submit" name="set_sendtroops_units" value="Speichern" />
</form>

</div><!-- //<div id="farmmanager"> -->

{/block}
