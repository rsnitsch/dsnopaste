<form action="add.php" method="post" name="eintrag" onsubmit="return checkForm()" target="_self">
	<input type="hidden" name="add" value="1" />
	
	<p style="font-size: 8pt;">Kopiere deinen Text (bei Berichten musst du ab "Gesendet (...)" kopieren!) in das Textfeld, gib den Typ an (meistens reicht Text) und klicke anschließend auf Einlesen.<br />
	Bei Berichten beachte: Es können keine "Spezialberichte" eingelesen werden (Berichte, bei denen du zum Beispiel Dörfer im Schlafmodus angegriffen hast)!</p>
	
	<p style="font-weight: bold; color: #21BB21;">Hinweis: Berichte von Welt 10-13 können noch nicht eingelesen werden!</p>
	
	<table border="0" id="eingabeformular_table">
		<tr>
			<td>
				<p>Typ:<br />
				<select name="type" style="width: 150px;">
					<option id="einlese_typ_bericht" value="attacknew">Bericht</option>
					<option id="einlese_typ_text" value="text">Text</option>
					<option id="einlese_typ_php" value="php">PHP Code</option>
				</select>
				</p>
				
				<p>Daten:<br />
					<textarea id="eingabeformular_data" name="report" cols="30" rows="5" onkeyup="displayInputLength()"></textarea>
					<br />
					<span id="inputLength">0</span> Zeichen
				</p>
				<a id="toggleAdv_link" href="javascript:toggleAdv()">&gt; Erweiterte Optionen einblenden</a>
			</td>
			<td width="50"></td>
			<td>
				<div id="advanced_options" style="display: none;">
					<p>Dein Name (optional):<br />
					<input type="text" name="poster" maxlength="100" />
					</p>
					<p>Kurzen Kommentar hinzufügen (optional):<br />
					<textarea cols="30" rows="4" name="comment"></textarea>
					</p>
					<p><input type="checkbox" name="key" value="1" checked="checked" /> Kennwort aktivieren (optional):<br />
					<span style="font-size: 8pt; font-weight: bold; color: #CC2121;">Hinweis: wird KEIN Kennwort verwendet, kann dieser Eintrag von jedermann gelesen werden!</span>
					</p>
				</div>
			</td>
		</tr>
	</table>

	<p><input type="submit" value="Einlesen" /> <input type="button" value="Zurück" onclick="history.go(-1)" /></p>
	
	<div style="display: none;"><input type="text" name="email" /> (Wenn du dieses Textfeld siehst, ignoriere es einfach!</div>
</form>


{literal}
<script language="javascript" type="text/javascript">
// clientseitige Eingabeprüfung...

function checkForm()
{
	if(!document.eintrag.report.value)
	{
		alert('Die Länge des eingelesenen Textes muss größer 0 sein! Du darfst das Feld nicht leer lassen!');
		return false;
	}
	
	if(document.eintrag.report.value.length < 2 || document.eintrag.report.value.length > 50000)
	{
		alert('Die Länge des eingelesenen Textes muss mind. 2 und darf maximal 50000 Zeichen betragen!');
		return false;
	}

	if(document.eintrag.comment.value.length > 300)
	{
		alert('Die Länge des Kommentars darf maximal 300 Zeichen betragen!');
		return false;
	}
	
	// alles ok
	return true;
}

// die aktuelle Anzahl von Zeichen (vom Haupttextfeld) anzeigen...
function displayInputLength()
{
	if(document.eintrag.report.value)
	{
		document.getElementById('inputLength').firstChild.nodeValue = document.eintrag.report.value.length;
	}
	else
	{
		document.getElementById('inputLength').firstChild.nodeValue = '0';
	}
}

// blendet die erweiterten Optionen ein bzw. aus
function toggleAdv()
{
	var adv=document.getElementById('advanced_options');
	var adv_link=document.getElementById('toggleAdv_link');
	if(adv.style.display=='none')
	{
		document.cookie = '1';
		adv.style.display = 'block';
		adv_link.firstChild.nodeValue='> Erweiterte Optionen ausblenden';
	}
	else
	{
		document.cookie = '0';
		adv.style.display = 'none';
		adv_link.firstChild.nodeValue='> Erweiterte Optionen einblenden';
	}
}

// bestimmen ob die erw. Einstellungen angezeigt werden sollen oder nicht
if(document.cookie=='1')
{
	toggleAdv();
}

</script>

{/literal}