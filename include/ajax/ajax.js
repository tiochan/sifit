/**
 * AJAX functions
 *
 * Adapted from http://www.w3schools.com/PHP/php_ajax_livesearch.asp
 *
 */

/**
 * Set the given value to a field identified by by the field_id.
 *
 * @param string field_id
 * @param string value
 */
function ajax_set_field_value(field_id, value) {
    document.getElementById(field_id).value=value;
}

/**
 * Set the given value as content to the div identified by the div_id.
 *
 * @param div_id
 * @param value
 */
function ajax_set_div_value(div_id, value) {
    document.getElementById(div_id).innerHTML=value;
}

/**
 * Parse the given value by lines ("\n") as list and set the result
 * as content to the div identified by the div_id.
 *
 * @param string div_id
 * @param string value
 * @param string selection_field_id
 */
function ajax_set_div_list_value(origin, div_id, value, selection_field_id) {

    var content= "";
    var line_values;
    var literal;
    var value;

    var lines = value.split("\n");

    for(i = 0; i < lines.length; i++) {

        line_values= lines[i].split("|");

        value= line_values[0];

        if(line_values.length > 1) {
            literal= line_values[1];
        } else {
            literal= line_values[0];
        }

        content+='<div style="block;" class="livesearch" onclick="ajax_set_field_value(\'' + selection_field_id + '\', \'' + value + '\'); ajax_hide_div(\'' + div_id + '\');">';
        content+=literal;
        content+='</div>\n';
    }

    document.getElementById(div_id).innerHTML=content;
}

/**
 * Auxiliar function to show a hidden div
 *
 * @param string div_id
 */
function ajax_show_div(div_id) {
//    document.getElementById(div_id).style.border="1px solid #A5ACB2";
    document.getElementById(div_id).style.display="block";
}

/**
 * Auxiliar function to hide div
 *
 * @param string div_id
 */
function ajax_hide_div(div_id) {
    document.getElementById(div_id).innerHTML="";
    document.getElementById(div_id).style.border="0px";
    document.getElementById(div_id).style.display="none";
}

/**
 * Get the HTTP Object in base of the navigator used.
 *
 * @returns HTTP Object
 */
    //
function getHTTPObject(){
    if (window.ActiveXObject) return new ActiveXObject("Microsoft.XMLHTTP");
    else if (window.XMLHttpRequest) return new XMLHttpRequest();
    else {
        alert("Your browser does not support AJAX.");
        return null;
    }
}

/**
 * Ajax function to send request via GET to destination using parameters and
 * set the return to the origin.
 *
 * @param string origin, the Id of the object set values
 * @param string destination, the URL to call
 * @param string parameters, to add to the URL call
 */
function doWorkGET(origin, destination, parameters){

    if (parameters.length==0) {
        ajax_hide_div(destination);
        return;
    }
    httpObject = getHTTPObject();
    if (httpObject != null) {
        httpObject.open("GET", origin + "?" + parameters, true);
        httpObject.send(null);
        httpObject.onreadystatechange = function() {
            if (httpObject.readyState == 4 && httpObject.status == 200) {
                ajax_set_div_value(destination, httpObject.responseText);
                ajax_show_div(destination);
            }
        }
    }
}

/**
 * Ajax function to send request via POST to destination using parameters and
 * set the return to the origin.
 *
 * @param string origin, the Id of the object set values
 * @param string destination, the URL to call
 * @param string parameters, to add to the URL call
 */
function doWorkPOST(origin, destination, parameters) {
    var pos, value_lenght;

    pos = (parameters + '').indexOf('=');

    value_length= parameters.length - pos - 1;

    if (value_length <= 3) {
        ajax_hide_div(destination);
        return;
    }
    httpObject = getHTTPObject();
    if (httpObject != null) {
        httpObject.open("POST", origin, true);
        httpObject.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        httpObject.setRequestHeader("Content-length", parameters.length);
        httpObject.setRequestHeader("Connection", "close");
        httpObject.send(parameters);

        // httpObject.onreadystatechange = setOutput;
        httpObject.onreadystatechange = function() {
            if (httpObject.readyState == 4 && httpObject.status == 200) {
                ajax_set_div_value(destination, httpObject.responseText);
                ajax_show_div(destination);
            }
        }
    }
}

/**
 * Ajax function to send request via POST to destination using parameters and
 * set the return AS A LIST to the origin.
 *
 * Usually used for live search, for example.
 *
 * @param string origin, the Id of the object set values
 * @param string destination, the URL to call
 * @param string parameters, to add to the URL call
 * @param string selection_field_id, OPTIONAL: the field where to set the selected value of the list
 */
function doListWorkPOST(origin, destination, parameters, selection_field_id) {
    var pos, value_lenght;

    pos = (parameters + '').indexOf('=');

    value_length= parameters.length - pos - 1;

    if (value_length <= 3) {
        ajax_hide_div(destination);
        return;
    }
    httpObject = getHTTPObject();
    if (httpObject != null) {
        httpObject.open("POST", origin, true);
        httpObject.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        httpObject.setRequestHeader("Content-length", parameters.length);
        httpObject.setRequestHeader("Connection", "close");
        httpObject.send(parameters);

        // httpObject.onreadystatechange = setOutput;
        httpObject.onreadystatechange = function() {
            if (httpObject.readyState == 4 && httpObject.status == 200) {
                ajax_set_div_list_value(origin, destination, httpObject.responseText, selection_field_id);
                ajax_show_div(destination);
            }
        }
    }
}

/**
 * Using Ajax, call to the webService passing via GET the parameter:
 *  {webService}?{field_id.name=field_id.value}
 *
 *  Take care with sync parameter. If the call is made synchronously, the navigator will remain blocked until
 *  the call is done.
 *
 * @param webService, The URL to the web service to call
 * @param field_id, The id of the field to check
 * @param sync boolean, If true, the ajax call will be made synchronously, else asynchronously (recommended)
 * @returns any, Returns the return of the web service call
 */
function ajax_check_field_value(webService, field_id, sync) {

    var field=document.getElementById(field_id);
    var field_name=field.name;
    var field_value= field.value;

    var ret;
    ret=ajax_get_response(webService, field_name + '=' + field_value, sync);

    return ret;
}

/**
 * Using Ajax, call to the webService passing via GET the parameter {params}
 *
 *  Take care with sync parameter. If the call is made synchronously, the navigator will remain blocked until
 *  the call is done.
 *
 * @param webService, The URL to the web service to call
 * @param field_id, The id of the field to check
 * @param sync boolean, If true, the ajax call will be made synchronously, else asynchronously (recommended)
 * @returns any, Returns the return of the web service call
 */
function ajax_get_response(webService, params, sync) {

    var result=0;

    if(sync) $.ajaxSetup({async:false});

    $.ajax({
        type: 'POST',
        url: webService + '?' + params,
        data: {},
        dataType: 'json',
        success: function(data) {
            result=data;
        },
        error: function() {
            result=0;
        }
    });

    if(sync) $.ajaxSetup({async:true});

    return result;
}

/**
 * timeout in miliseconds
 */
function ajax_set_timeout(callback_function, timeout, callbackagain) {

	// Call function
	window[callback_function]();
	
	if(callbackagain) setTimeout(ajax_set_timeout, timeout, callback_function, timeout, callbackagain);
}

var httpObject = null;
