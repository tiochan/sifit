<?php
	/**
	 * @author Sebastian Gomez (tiochan@gmail.com)
	 * For: Politechnical University of Catalonia (UPC), Spain.
	 *
	 * @package lib
	 * @subpackage javascript
	 */
?>
// General functions
function openWin(URL) {
		myWin= openNewWindow(URL,"", "");
		return myWin;
}

function openLookup(URL) {
	myWin= openNewWindow(URL,"","width=400,height=300,status=no,toolbar=no,menubar=no");
	return myWin;
}

function openNewWindow(URL, title, options) {
	myWin= open(URL, title, options);
	return myWin;
}
