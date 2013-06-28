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

function smartifyCoordInputs(x_elem, y_elem) {
	x_elem.keyup(function() {
		var inp = x_elem.val();
		if (inp.match(/\|$/)) {
			x_elem.val(inp.substr(0, inp.length-1));
			y_elem.val("").focus();
		} else if (inp.match(/\|/)) {
			var match = inp.match(/(.*)\|(.*)/);
			if (match) {
				x_elem.val(match[1]);
				y_elem.val("").focus().val(match[2]);
			}
		} else if (inp.length > 3) {
			var rest = inp.substr(3);
			if (rest.substr(0, 1) == "|") rest = rest.substr(1);
			y_elem.val("").focus().val(rest);
			x_elem.val(inp.substr(0, 3));
		}
	});
}

function autoSwitchFocus(prev_elem, next_elem, maxLen) {
	prev_elem.keyup(function() {
		var inp = prev_elem.val();
		if (inp.length > maxLen) {
			var rest = inp.substr(maxLen);
			next_elem.val("").focus().val(rest);
			prev_elem.val(inp.substr(0, maxLen));
		}
	});
}
