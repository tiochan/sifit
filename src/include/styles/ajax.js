// Get the HTTP Object
function getHTTPObject() {
	if (window.ActiveXObject) return new ActiveXObject("Microsoft.XMLHTTP");
	else if (window.XMLHttpRequest) return new XMLHttpRequest();
	else {
		alert("Your browser does not support AJAX.");
		return null;
	}
}

// Change the value of the outputText field
function setOutput() {
	if (httpObject.readyState == 4) {
		//		document.getElementById('outputText').value = httpObject.responseText;
		//		document.getElementById("output").value=httpObject.responseText;
		document.getElementById("livesearch").innerHTML = httpObject.responseText;
		document.getElementById("livesearch").style.border = "1px solid #A5ACB2";
	}
}

// Implement business logic
function doWorkGET(origin, destination, parameters) {
	if (parameters.length == 0) {
		document.getElementById(destination).innerHTML = "";
		document.getElementById(destination).style.border = "0px";
		return;
	}
	httpObject = getHTTPObject();
	if (httpObject != null) {
		httpObject.open("GET", origin + "?" + parameters, true);
		httpObject.send(null);
		httpObject.onreadystatechange = setOutput;
	}
}

// http://www.captain.at/howto-ajax-form-post-request.php
function doWorkPOST(origin, destination, parameters) {
	if (parameters.length == 0) {
		document.getElementById(destination).innerHTML = "";
		document.getElementById(destination).style.border = "0px";
		return;
	}
	httpObject = getHTTPObject();
	if (httpObject != null) {
		httpObject.open("POST", origin, true);
		httpObject.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		httpObject.setRequestHeader("Content-length", parameters.length);
		httpObject.setRequestHeader("Connection", "close");
		httpObject.send(parameters);

		httpObject.onreadystatechange = setOutput;
	}
}



var httpObject = null;