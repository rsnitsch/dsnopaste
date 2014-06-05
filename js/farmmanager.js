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
				"width=900,height=800,location=no,menubar=no,toolbar=yes,status=yes,scrollbars=yes,resizable=yes");
}

function fm_updateFormVisible() {
	var formvisible = getCookie(fm_getID()+"_formvisible", "true");
	if(formvisible != "true")
		$("#form").hide();
}
