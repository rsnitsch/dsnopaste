{extends 'layout.tpl'}
{block 'head'}
{$smarty.block.parent}
	<script language="javascript" type="text/javascript" src="{$root_path}js/farmmanager.js"></script>
{/block}

{block 'content'}
<div id="farmmanager">

{literal}
<script language="javascript" type="text/javascript">
$(document).ready(
	function() {
		fm_updateFormVisible();

		$('#update_settings select[name=source_village]').change(function() {
			document.forms.namedItem('update_settings').submit();
		});
	}
);
</script>
{/literal}

<div class="tiny">
	<p>
		Link zu diesem Farmmanager (bitte abspeichern):
		<a class="simple" href="{$server_url}/tools/farmmanager.php?id={$saveid}">
			{$server_url}/tools/farmmanager.php?id={$saveid}
		</a>
	</p>
</div>

{if !isset($edited_farm)}
<h3><a href="javascript:fm_toggleForm();">Farmbericht/Spähbericht einlesen</a></h3>

<div id="form">
	<form action="farmmanager.php?id={$saveid}" method="post">
	<input type="hidden" name="parse" value="1" />
	<table>
		<tr>
			<th colspan="3">Farmbericht einfügen (ab einschließlich Betreff):</th>
		</tr>
		<tr>
			<td colspan="3"><textarea name="report" cols="60" rows="3"></textarea></td>
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
				<select name="bonus" style="width: 140px;">
					<option value="" style="padding-left:20px;" selected="selected">- keine Angabe (alter Wert wird beibehalten) -</option>
					<option value="none" style="padding-left:20px;">keiner dieser Boni</option>
					<option value="all" style="background-image: url({$root_path}images/storage.png);background-repeat: no-repeat;padding-left: 20px;">+{$bonus_res_all}% erhöhte Rohstoffproduktion</option>
					<option value="wood" style="background-image: url({$root_path}images/holz.png);background-repeat: no-repeat;padding-left: 20px;">+{$bonus_res_one}% erhöhte Holzproduktion</option>
					<option value="loam" style="background-image: url({$root_path}images/lehm.png);background-repeat: no-repeat;padding-left: 20px;">+{$bonus_res_one}% erhöhte Lehmproduktion</option>
					<option value="iron" style="background-image: url({$root_path}images/eisen.png);background-repeat: no-repeat;padding-left: 20px;">+{$bonus_res_one}% erhöhte Eisenproduktion</option>
					{if $bonus_new}<option value="storage" style="background-image: url({$root_path}images/storage.png);background-repeat: no-repeat;padding-left: 20px;">+50% Speicher-Volumen</option>{/if}
				</select>
			</td>
		</tr>
	</table>
	<input type="submit" style="font-weight: bold;width:500px;height:35px;" value="OK" />
	</form>
</div>
{else}
<h3><a href="javascript:fm_toggleForm();">Farm bearbeiten</a></h3>

<div id="form">
	<form action="farmmanager.php?id={$saveid}" method="post">
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
				<select name="bonus" style="width: 140px;">
					<option value="none" style="padding-left:20px;"{if $edited_farm.bonus=='none'} selected="selected"{/if}>keiner dieser Boni</option>
					<option value="all" style="background-image: url({$root_path}images/storage.png);background-repeat: no-repeat;padding-left: 20px;"{if $edited_farm.bonus=='all'} selected="selected"{/if}>+{$bonus_res_all}% erhöhte Rohstoffproduktion</option>
					<option value="wood" style="background-image: url({$root_path}images/holz.png);background-repeat: no-repeat;padding-left: 20px;"{if $edited_farm.bonus=='wood'} selected="selected"{/if}>+{$bonus_res_one}% erhöhte Holzproduktion</option>
					<option value="loam" style="background-image: url({$root_path}images/lehm.png);background-repeat: no-repeat;padding-left: 20px;"{if $edited_farm.bonus=='loam'} selected="selected"{/if}>+{$bonus_res_one}% erhöhte Lehmproduktion</option>
					<option value="iron" style="background-image: url({$root_path}images/eisen.png);background-repeat: no-repeat;padding-left: 20px;"{if $edited_farm.bonus=='iron'} selected="selected"{/if}>+{$bonus_res_one}% erhöhte Eisenproduktion</option>
					{if $bonus_new}<option value="storage" style="background-image: url({$root_path}images/storage.png);background-repeat: no-repeat;padding-left: 20px;"{if $edited_farm.bonus=='storage'} selected="selected"{/if}>+50% Speicher-Volumen</option>{/if}
				</select>
			</td>
		</tr>
	</table>
	<input type="submit" style="font-weight: bold;width:500px;height:35px;" value="Speichern" />
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
	
	{if count($farms) > 0}
	<table cellspacing="0" cellpadding="3" id="farmmanager_farms">
		<tr>
			<th><a href="farmmanager.php?id={$saveid}&amp;order=v_coords">XXX|YYY</a></th>
			<th>Dorf</th>
			<th>Bonus</th>
			{if $source_village}<th><a href="farmmanager.php?id={$saveid}&amp;order=distance">Entfernung</a></th>{/if}
			<th>
				Aktuelle Ressourcen
				(<a href="farmmanager.php?id={$saveid}&amp;order=c_wood" title="Nach Holz sortieren"><img src="{$root_path}images/holz.png" alt="Holz" /></a>
				<a href="farmmanager.php?id={$saveid}&amp;order=c_loam" title="Nach Lehm sortieren"><img src="{$root_path}images/lehm.png" alt="Lehm" /></a>
				<a href="farmmanager.php?id={$saveid}&amp;order=c_iron" title="Nach Eisen sortieren"><img src="{$root_path}images/eisen.png" alt="Eisen" /></a>)
			</th>
			<th><a href="farmmanager.php?id={$saveid}&amp;order=c_sum"><abbr title="Ressourcen">Ress.</abbr></a></th>
			<th> / </th>
			<th><a href="farmmanager.php?id={$saveid}&amp;order=storage">Speicher</a> (<a href="farmmanager.php?id={$saveid}&amp;order=fill_level" title="relativer Füllstand des Speichers">XX%</a>)</th>
			<th><a href="farmmanager.php?id={$saveid}&amp;order=spear">Speer</a></th>
			<th><a href="farmmanager.php?id={$saveid}&amp;order=light"><abbr title="Leichte Kavallerie">LKav</abbr></a></th>
			<th>Wall</th>
			<th>Performance</th>
			<th><a href="farmmanager.php?id={$saveid}&amp;order=lastreport">Letzter Bericht</a></th>
			<th>Notiz</th>
			<th class="align_right">Aktionen (<a href="javascript:fm_actionDescription()">Info</a>)</th>
		</tr>
	{foreach from=$farms item=farm}{if !$farm.filter}
		<tr {if $farm.farmed}class="green"{/if} style="background-color: {cycle values="#F1EBDD,#E7E2D5"};">
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
			<td>
				<img src="{$root_path}images/holz.png" alt="Holz" />{$farm.c_wood}
				<img src="{$root_path}images/lehm.png" alt="Lehm" />{$farm.c_loam}
				<img src="{$root_path}images/eisen.png" alt="Eisen" />{$farm.c_iron}
			</td>
			<td class="align_left{if $farm.c_sum>=$farm.storage_max} red{/if}">{$farm.c_sum}</td>
			<td> / </td>
			<td class="align_left{if $farm.c_sum>=$farm.storage_max} red{/if}">{$farm.storage_max} ({$farm.fill_level}%)</td>
			<td><img src="{$root_path}images/unit_spear.png" alt="Speer" />{$farm.transport_spear}</td>
			<td><img src="{$root_path}images/unit_light.png" alt="Leichte Kavallerie" />{$farm.transport_light}</td>
			<td>{if $farm.b_wall >= 5}<span class="warnung" style="background-color: #f99; padding: 5px;">St. {$farm.b_wall}</span>{else}St. {$farm.b_wall}{/if}</td>
			<td>{if $farm.performance === null}-{else}{$farm.performance_percentage}%{/if}</td>
			<td>{$farm.time|date_format:"%d.%m. %H:%M Uhr"}</td>
			<td>{$farm.note|escape}</td>
			<td class="align_right">
				<!-- Als gefarmt markieren bzw. die Umkehrung -->
				<a class="image_link" href="farmmanager.php?id={$saveid}&amp;farmed={$farm.id}">
					<img src="{$root_path}images/unit_axe{if $farm.farmed}_arrowup{/if}.png" title="Als gefarmt markieren bzw. Markierung aufheben" alt="Axt" />
				</a>
				&nbsp;

				{if isset($source_village_id) && !$farm.farmed && $farm.v_id != 0}
				<!-- Späher schicken (semi-automatisch) und als gefarmt markieren -->
				<a class="image_link" href="farmmanager.php?id={$saveid}&amp;farmed={$farm.id}" onclick="fm_sendTroops('{$world_id}', {$source_village_id}, {$farm.v_id}, 'spy={$sendtroops_spy_count}');">
					<img src="{$root_path}images/unit_spy.png" title="Späher schicken & als gefarmt markieren" alt="Späher" />
				</a>
				&nbsp;

				<!-- Speerträger schicken (semi-automatisch) und als gefarmt markieren -->
				<a class="image_link" href="farmmanager.php?id={$saveid}&amp;farmed={$farm.id}" onclick="fm_sendTroops('{$world_id}', {$source_village_id}, {$farm.v_id}, 'spy={$sendtroops_spy_count}&spear={$farm.transport_spear}');">
					<img src="{$root_path}images/unit_spear.png" title="Speerträger schicken & als gefarmt markieren" alt="Speer" />
				</a>
				&nbsp;
				<!-- Leichte Kavallerie schicken (semi-automatisch) und als gefarmt markieren -->
				<a class="image_link" href="farmmanager.php?id={$saveid}&amp;farmed={$farm.id}" onclick="fm_sendTroops('{$world_id}', {$source_village_id}, {$farm.v_id}, 'spy={$sendtroops_spy_count}&light={$farm.transport_light}');">
					<img src="{$root_path}images/unit_light.png" title="Leichte Kavallerie schicken & als gefarmt markieren" alt="Leichte Kavallerie" />
				</a>
				&nbsp;
				{/if}

				<!-- Bearbeiten Farm -->
				<a class="image_link" href="farmmanager.php?id={$saveid}&amp;edit={$farm.id}">
					<img src="{$root_path}images/icon_text.gif" border="0" title="Bearbeiten" alt="Text-Icon" width="20" height="20" />
				</a>
				&nbsp;
				<!-- Löschen der Farm -->
				<a class="image_link" href="farmmanager.php?id={$saveid}&amp;delete={$farm.id}" onclick="return confirm('Möchtest du diese Farm wirklich löschen?');">
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
			<td class="tiny">
				<img src="{$root_path}images/holz.png" width="11" height="11" alt="Holz" />{$total_wood}
				<img src="{$root_path}images/lehm.png" width="11" height="11" alt="Lehm" />{$total_loam}
				<img src="{$root_path}images/eisen.png" width="11" height="11" alt="Eisen" />{$total_iron}
			</td>
			<td class="tiny">{$total_sum}</td>
			<td> / </td>
			<td class="tiny">{$total_storage}</td>
			<td class="tiny">{$total_spear}</td>
			<td class="tiny">{$total_light}</td>
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
</div><!-- //<div id="farmmanager"> -->

{/block}
