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
	document.forms.namedItem("attplan_form").elements.namedItem(name).value = value;
}

function ap_setCoord(which, new_x, new_y) {
	document.forms.namedItem("attplan_form").elements.namedItem(which + "_x").value = new_x;
	document.forms.namedItem("attplan_form").elements.namedItem(which + "_y").value = new_y;
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

$(document).ready(function() {
	smartifyCoordInputs($("input[name=from_x]"), $("input[name=from_y]"));
	smartifyCoordInputs($("input[name=to_x]"), $("input[name=to_y]"));
	autoSwitchFocus($("input[name=from_y]"), $("input[name=to_x]"), 3);
	
	$('.normal_link_description').click(function() {
		ap_normalLinkDescription();
		return false;
	});
	$('.admin_link_description').click(function() {
		ap_adminLinkDescription();
		return false;
	});
	$('.setCoord').click(function() {
		ap_setCoord($(this).attr('data-which'), $(this).attr('data-x'), $(this).attr('data-y'));
		return false;
	});
	$('.setValue').click(function() {
		ap_setValue($(this).attr('data-name'), $(this).attr('data-value'));
		return false;
	});
	$('.delete_action').click(function() {
		return ap_deleteConfirm();
	});
	$('#attplaner_aktion_notiz').keyup(function() {
		$(this).val($(this).val().substr(0,50));
	});
	
	if ($('#no_admin_notice').length) {
		setTimeout("alert('Du hast diesen Angriffsplan nicht erstellt. Du bist nicht berechtigt Änderungen durchzuführen.');", 1000);
	}
});
