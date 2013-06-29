{extends 'layout.tpl'}
{block 'content'}

	<p>Dieses Tool dient dazu einen übersichtlichen Post zu generieren, in dem alle wichtigen Informationen über einen
	Angriff für den ganzen Stamm stehen... zur Formatierung wird BBCode verwendet.<br />
	<b>Wichtig: </b>Javascript muss aktiviert sein!</p>
	<br />

	<form action="" name="formdeff" method="get">

		<table border="0" class="dsstyle" id="deffform" style="width: 800px;">
			<tr>
				<th>Info über den Angreifer</th>

				<th>Info über sich angeben (welches Dorf angegriffen wird):</th>
				<th>Info über das angegriffene Dorf</th>
			</tr>
			<tr>
				<td>
					<table border="0">
						<tr>
							<td>Angreifer:</td>

							<td><input type="text" name="angreifer_nick" /></td>
						</tr>
						<tr>
							<td>Dorf:</td>
							<td><input type="text" name="angreifer_dorf" /></td>
						</tr>
						<tr>
							<td>Stamm:</td>

							<td><input type="text" name="angreifer_stamm" /></td>
						</tr>
						<tr>
							<td>Punkte:</td>
							<td><input type="text" name="angreifer_punkte" /></td>
						</tr>
						<tr>
							<td>Anzahl Angriffe:</td>

							<td><input type="text" name="angreifer_angriffe" /></td>
						</tr>
					</table>
				</td>
				<td>
					<table border="0">
						<tr>
							<td>Dein Nick:</td>

							<td><input type="text" name="verteidiger_nick" /></td>
						</tr>
						<tr>
							<td>Wall Stufe:</td>
							<td><input type="text" name="verteidiger_wall" /></td>
						</tr>
						<tr>
							<td>Ankunft des Angreifers:</td>

							<td><input type="text" name="verteidiger_ankunft" /></td>
						</tr>
						<tr>
							<td>Zieldorf:</td>
							<td><input type="text" name="verteidiger_ziel" /></td>
						</tr>
					</table>
				</td>

				<td>
					<table border="0">
						<tr>
							<td>Speerträger:</td>
							<td><input type="text" name="verteidiger_speer" /></td>
						</tr>
						<tr>
							<td>Schwert:</td>

							<td><input type="text" name="verteidiger_schwert" /></td>
						</tr>
						<tr>
							<td>Bogenschützen:</td>
							<td><input type="text" name="verteidiger_bogen" /></td>
						</tr>
						<tr>
							<td>Schwere Kavallerie:</td>
							<td><input type="text" name="verteidiger_skav" /></td>
						</tr>
						<tr>
							<td>Paladin(e):</td>
							<td><input type="text" name="verteidiger_paladin" /></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<br />
		<input type="checkbox" name="simplebb" /> Nur kompatible BBCodes verwenden (keine [player], [ally], [village] - Tags)
		<br />

		<input type="checkbox" name="noimg" /> Keine Bilder für die Einheiten verwenden (denn in Ingame-Nachrichten sind Bilder nicht erlaubt)
		<br />
		<input type="checkbox" name="nolink" /> Kein Link zu dieser Seite hinzufügen
		<br />
		<input type="button" value="Generieren" onclick="generate();" style="height: 50px; width: 800px;" />
		<br />
		<p>
			Ergebnis:<br />

			<textarea name="ergebnis" cols="100" rows="25"></textarea>
		</p>
	</form>

	<script language="javascript" type="text/javascript">
		{literal}
		// copyright 2006, Robert Nitsch
		function generate()
		{
			var ergebnis=document.formdeff.ergebnis;
			ergebnis.value='';

			// info über angreifer
			ergebnis.value += '[u][b]Info über den Angreifer[/b][/u]\n';
			ergebnis.value += 'Angreifer: '+bbcode(document.formdeff.angreifer_nick.value,'player')+'\n';
			ergebnis.value += 'Dorf: '+bbcode(document.formdeff.angreifer_dorf.value,'village')+'\n';
			ergebnis.value += 'Stamm: '+bbcode(document.formdeff.angreifer_stamm.value,'ally')+'\n';
			ergebnis.value += 'Punkte: '+bbcode(document.formdeff.angreifer_punkte.value,'')+'\n';
			ergebnis.value += 'Angriffe: '+bbcode(document.formdeff.angreifer_angriffe.value,'')+'\n';

			// info über sich selbst
			ergebnis.value += '\n';
			ergebnis.value += '[u][b]Info über sich selbst (welches Dorf angegriffen wird...):[/b][/u]\n';
			ergebnis.value += 'Angegriffener: '+bbcode(document.formdeff.verteidiger_nick.value,'player')+'\n';
			ergebnis.value += 'Wall: '+bbcode(document.formdeff.verteidiger_wall.value,'')+'\n';
			ergebnis.value += 'Ankunft: '+bbcode(document.formdeff.verteidiger_ankunft.value,'')+'\n';
			ergebnis.value += 'Ziel: '+bbcode(document.formdeff.verteidiger_ziel.value,'village')+'\n';

			// info über angegriffenes Dorf
			ergebnis.value += '\n';
			ergebnis.value += '[u][b]Info über das angegriffene Dorf (Truppen...):[/b][/u]\n';
			ergebnis.value += bbcode('http://dsgfx.bmaker.net/unit_spear.png','img')+'Speerträger: '+bbcode(document.formdeff.verteidiger_speer.value,'')+'\n';
			ergebnis.value += bbcode('http://dsgfx.bmaker.net/unit_sword.png','img')+'Schwertkämpfer: '+bbcode(document.formdeff.verteidiger_schwert.value,'')+'\n';
			ergebnis.value += bbcode('http://dsgfx.bmaker.net/unit_archer.png','img')+'Bogenschützen: '+bbcode(document.formdeff.verteidiger_bogen.value,'')+'\n';
			ergebnis.value += bbcode('http://dsgfx.bmaker.net/unit_heavy.png','img')+'Schwere Kavallerie: '+bbcode(document.formdeff.verteidiger_skav.value,'')+'\n';
			ergebnis.value += bbcode('http://dsgfx.bmaker.net/unit_knight.png','img')+'Paladin(e): '+bbcode(document.formdeff.verteidiger_paladin.value,'')+'\n';


			// werbung ;-)
			if(!document.formdeff.nolink.checked)
				ergebnis.value += '\n\ngeneriert mit dem [url=http://np.bmaker.net]Deffformular[/url]';

			// das Ergebnis direkt auswählen
			document.formdeff.ergebnis.select();
		}


		function bbcode(str, tag)
		{
			if(document.formdeff.simplebb.checked)
			{
				if(tag=='player' || tag=='village' || tag=='ally')
					return str;
			}

			if(document.formdeff.noimg.checked)
			{
				if(tag=='img')
					return '';
			}

			if(tag != '')
				return '['+tag+']'+str+'[/'+tag+']';
			else
				return str;
		}
		{/literal}
	</script>

{/block}
