function fm_getID() {
	return document.URL.match(/id=([0-9a-zA-Z]{10})/)[1];
}

function fm_toggleForm() {
	if($("#form:visible").size() == 0)
		// if not visible yet it will be visible after toggling
		setCookie(fm_getID()+"_formvisible", "true");
	else
		// if visible it will no more be visible after toggling
		setCookie(fm_getID()+"_formvisible", "false");

	$("#form").toggle("fast");
}

function fm_sendTroops(world_id, from, to, units) {
	window.open("sendtroops.php?world="+world_id+
				"&from="+from+
				"&to="+to+
				"&"+units,
				"dsnp_sendtroops",
				"width=800,height=600,location=no,menubar=no,toolbar=yes,status=yes,scrollbars=yes,resizable=yes");
}

function fm_updateFormVisible() {
	var formvisible = getCookie(fm_getID()+"_formvisible", "true");
	if(formvisible != "true")
		$("#form").hide();
}

function fm_actionDescription() {
	alert(
		'== Axt ==\n'+
		'Ein Klick auf die Axt markiert die jeweilige Farm als \'gefarmt\' (und umgekehrt). '+
		'Sie wird dann normalerweise so lange an das Ende der Liste geschoben, bis ein neuer Bericht von dieser Farm eingelesen wird.\n'+
		'\n'+
		'== Späher ==\n'+
		'Ein Klick auf den Späher schickt mittels 1-Klick-Farmen 5 Späher zu der Farm. Die Farm wird dabei auch gleich als gefarmt markiert.\n'+
		'\n'+
		'== Speer ==\n'+
		'Ein Klick auf den Speer aktiviert das 1-Klick-Farmen mit Speerträgern. Die Farm wird dabei auch gleich als gefarmt markiert.\n'+
		'\n'+
		'== Leichte Kavallerie ==\n'+
		'Ein Klick auf die leichte Kavallerie aktiviert das 1-Klick-Farmen mit leichter Kavallerie. Die Farm wird dabei auch gleich als gefarmt markiert.\n'+
		'\n'+
		'== Text ==\n'+
		'Bearbeiten der Farm.\n'+
		'\n'+
		'== Kreuz ==\n'+
		'Achtung: Ein Klick auf das Kreuz löscht die jeweilige Farm aus der Liste!'
	);
}
