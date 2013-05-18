/* ##################### */
/* Allgemeine Funktionen */

// Setzt einen Cookie
//   expires: Das Ablaufdatum als Millisekunden-Timestamp (default: 1 Monat)
function setCookie(name, value, expires) {
    var date = new Date();
    date.setTime(expires != null ? expires : date.getTime() + 24*3600*30*1000);
    document.cookie = name+"="+value+";expires="+date.toGMTString();
}

// Gibt den Inhalt eines Cookies zurück
//    defaultValue: der Rückgabewert, falls der Cookie nicht gesetzt ist
function getCookie(cookieName, defaultValue) {
    var allCookies=""+document.cookie;
    
    var ind=allCookies.indexOf(cookieName);
    if (ind==-1 || cookieName=="" || allCookies.charAt(ind+cookieName.length) != '=')
        return defaultValue;
    if(ind > 0 && allCookies.charAt(ind-1) != ' ')
        return defaultValue;
        
    var ind1=allCookies.indexOf(';',ind);
    if (ind1==-1)
        ind1=allCookies.length;
        
    return unescape(allCookies.substring(ind+cookieName.length+1,ind1));
}

function getXMLHttpRequestObject() {
	var http = null;
	
    if (window.XMLHttpRequest) {
        http = new XMLHttpRequest();
    } else if (window.ActiveXObject) {
        http = new ActiveXObject("Microsoft.XMLHTTP");
    }
	
	return http;
}

/* ################################# */
/* Funktionen für den Angriffsplaner */

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

/* ############################## */
/* Funktionen für den Farmmanager */

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
    var target = "";
    
    if(getCookie(fm_getID()+"_noads", "false") == "true") {
        target = "&target=top";
    }
    
    window.open("sendtroops.php?world="+world_id+
                "&form=1"+
                "&from="+from+
                "&to="+to+
                target+
                "&"+units,
                "dsnp_sendtroops",
                "width=800,height=600,location=no,menubar=no,toolbar=yes,status=yes,scrollbars=yes,resizable=yes");
}

function fm_updateFormVisible() {
    var formvisible = getCookie(fm_getID()+"_formvisible", "true");
    if(formvisible != "true")
        $("#form").hide();
}

function fm_updateNoAds() {
    var noads = getCookie(fm_getID()+"_noads", "false");
    if(noads == "true") {
        $("#fm_noads_state").text("Ich HABE Werbefreiheit.").removeClass("red").addClass("green");
    }
    else {
        $("#fm_noads_state").text("Ich habe KEINE Werbefreiheit.").removeClass("green").addClass("red");
    }
    
    // cookie refreshen, damit er nicht nach 30 Tagen gelöscht wird,
    // sondern erst nach 30 Tagen NICHT-Nutzung des Farmmanagers
    if(noads != "false")
        setCookie(fm_getID()+"_noads", noads);
}

function fm_toggleNoAds() {
    var state = getCookie(fm_getID()+"_noads", "false");
    
    if(state == "false") {
        // ehemals false
        setCookie(fm_getID()+"_noads", "true");
        fm_updateNoAds();
    }
    else {
        // ehemals true
        setCookie(fm_getID()+"_noads", "false");
        fm_updateNoAds();
    }
}

function fm_actionDescription() {
    alert('== Axt ==\n'+
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
          'Achtung: Ein Klick auf das Kreuz löscht die jeweilige Farm aus der Liste!');
}