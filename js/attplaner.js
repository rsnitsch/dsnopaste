function ap_getKey() {
	return document.URL.match(/key=([0-9a-zA-Z]{6})/)[1];
}

function ap_deleteConfirm()
{
	if(confirm("Diesen Angriff bzw. diese Unterstützung wirklich löschen?"))
		return true;
	else
		return false;
}

function ap_setValue(name, value)
{
	eval("document.forms.attplan_form."+name+".value = value");
}

function ap_normalLinkDescription() {
	alert("== Einfacher Link zum Angriffsplan ==\n\nMit diesem Link kann man den "+
		  "Angriffsplan nur ANSEHEN. Bearbeitungsrechte bekommt man dabei NICHT.\n\n"+
		  "Du kannst den einfachen Link also benutzen um den Angriffsplan deinem Stamm "+
		  "oder Freunden zu zeigen, wenn diese den Plan nicht bearbeiten sollen.");
}

function ap_adminLinkDescription() {
	alert("== Admin-Link zum Angriffsplan ==\n\n"+
		  "Mit diesem Link kann man den Angriffsplan ansehen UND bearbeiten. "+
		  "Die Bearbeitungsrechte sind PERMANENT, das heißt man braucht den Admin-Link "+
		  "nur 1x zu benutzen und man hat (normalerweise) dauerhafte Bearbeitungsrechte für "+
		  "diesen Angriffsplan.\n\n"+
		  "Du solltest die Admin-Links zu deinen Angriffsplänen grundsätzlich zu deinen "+
		  "Lesezeichen hinzufügen, denn deine Bearbeitungsrechte gehen verloren, wenn du "+
		  "deine Cookies löschst. Mit einem Admin-Link kann man sich die Bearbeitungsrechte "+
		  "aber jederzeit wiederherstellen.");
}
