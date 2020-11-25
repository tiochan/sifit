// General functions

/*
	Open URL functions
 */

// Change the URL of the current page
function openURL(URL) {
	self.location.href = URL;
}

// Open a new window using URL
function openWin(URL) {
	myWin = openNewWindow(URL, "", "");
	return myWin;
}

// Open a new window using URL and generic window size for lookups
function openLookup(URL) {
	myWin = openNewWindow(URL, "", "width=800,height=600,scrollbars=yes");
	return myWin;
}

// Open a new window using URL and custom size
function openMyWindow(URL, width, height) {
	myWin = openNewWindow(URL, "", "width=" + width + ",height=" + height + ",status=0,toolbar=0,menubar=0,resizable=0,scrollbars=1");
}

// Open a new window using URL and generic forms size
function openForm(URL) {
	myWin = openNewWindow(URL, "", "width=500,height=470,status=no,toolbar=no,menubar=no,scrollbars=yes");
	return myWin;
}

//Open a new window using URL and generic forms size
function openMyForm(URL, width, height) {
	myWin = openNewWindow(URL, "", "width=" + width + ",height=" + height + ",location=no,titlebar=no,status=no,toolbar=no,menubar=no,scrollbars=yes");
	return myWin;
}

function PopupCenter(pageURL, title, w, h) {
	var left = (screen.width / 2) - (w / 2);
	var top = (screen.height / 2) - (h / 2);
	var targetWin = window.open(pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
}

// Open a new window using URL and custom options (more than size)
function openNewWindow(URL, title, options) {
	myWin = open(URL, title, options);
	return myWin;
}

// Search for a field on each form and set its value
function set_value(field, value) {
	var field_reference;

	if (field == "") {
		alert("Error: Empty field name.");
		return;
	}

	if (window.FCKeditorAPI) {
		field_reference = FCKeditorAPI.GetInstance(field);
		field_reference.InsertHtml(value);
	} else {

		var found;

		for (i = 0, found = 0; ((i < document.forms.length) && (found == 0)); i++) {
			str = "";

			for (j = 0; ((j < document.forms[i].elements.length) && (found == 0)); j++) {
				if (document.forms[i].elements[j].name == field) {
					found = 1;
					form_id = i;
					field_id = j;
				}
			}
		}

		if (found == 0) {
			alert("FIELD NOT FOUND");
		} else {
			document.forms[form_id].elements[field_id].value = value;
		}
	}
}

// Set the value of a given field of a given form
function set_field_value(form_name, field_name, val) {

	var my_form;

	if (!document.forms[form_name]) return false;
	my_form = document.forms[form_name];

	if (!my_form.elements[field_name]) return false;
	my_form.elements[field_name].value = val;

	return true;
}

// Get the value of a field
function get_value(field) {
	var ret;

	if (!document.forms.contenedor.elements[field]) return "";
	return document.forms.contenedor.elements[field].value;
}

// Is a field checked?
function get_checked(field) {
	if (!document.forms.contenedor.elements[field]) return false;
	return document.forms.contenedor.elements[field].checked;
}

function check_all_checkboxes(field_prename) {

	prename = field_prename.toString();
	end = prename.length;

	for (i = 0; i < document.forms.length; i++) {

		for (j = 0; j < document.forms[i].elements.length; j++) {

			pre = document.forms[i].elements[j].name.substr(0, end);
			if (pre == prename) {
				document.forms[i].elements[j].checked = true;
			}
		}
	}
}

function uncheck_all_checkboxes(field_prename) {

	prename = field_prename.toString();
	end = prename.length;

	for (i = 0; i < document.forms.length; i++) {

		for (j = 0; j < document.forms[i].elements.length; j++) {

			pre = document.forms[i].elements[j].name.substr(0, end);
			if (pre == prename) {
				document.forms[i].elements[j].checked = false;
			}
		}
	}
}

function invert_checkboxes(field_prename) {

	prename = field_prename.toString();
	end = prename.length;

	for (i = 0; i < document.forms.length; i++) {

		for (j = 0; j < document.forms[i].elements.length; j++) {

			pre = document.forms[i].elements[j].name.substr(0, end);
			if (pre == prename) {
				if (document.forms[i].elements[j].checked) document.forms[i].elements[j].checked = false;
				else document.forms[i].elements[j].checked = true;
			}
		}
	}
}

function ckInsertHTML(fck_name, text) {
	CKEDITOR.instances[fck_name].insertText(text);
}

function ReplaceContentInContainer(id, content) {
	var container = document.getElementById(id);
	container.innerHTML = content;
}

function getRadioValue(radioObj) {

	if (!radioObj)
		return "";
	var radioLength = radioObj.length;
	if (radioLength == undefined)
		if (radioObj.checked)
			return radioObj.value;
		else
			return "";
	for (var i = 0; i < radioLength; i++) {
		if (radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return "";
}

function setCheckedValue(radioObj, newValue) {
	if (!radioObj)
		return;
	var radioLength = radioObj.length;
	if (radioLength == undefined) {
		radioObj.checked = (radioObj.value == newValue.toString());
		return;
	}
	for (var i = 0; i < radioLength; i++) {
		radioObj[i].checked = false;
		if (radioObj[i].value == newValue.toString()) {
			radioObj[i].checked = true;
		}
	}
}

function hide_div(div_id) {
	var element;

	element = document.getElementById(div_id)
	if (element) {
		element.style.display = 'none';
	}
}

function toggle_div_visibility(div_id, show_mode) {

	var element;

	if (show_mode == null) show_mode = 'inline';

	element = document.getElementById(div_id)
	if (element) {
		if (element.style.display == 'none') {
			element.style.display = show_mode;
		} else {
			element.style.display = 'none';
		}
	}
}

function setFocus(input_id) {
	var element_obj = document.getElementById("account_username");

	if (element_obj) element_obj.focus();
}