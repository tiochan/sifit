<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage ajax
 *
 * Ajax global functions.
 *
 */

/**
 *
 * This function return a string with the value of a line to
 * return to a ajax request for a field of type list (like a LDAP search)
 *
 * @param string $value, the value that will be set to the remote HTML field
 * @param string $display, the string that will be displayed for this value
 * @param string $add, optionally you can add some info for this line (will be tabuled)
 * @return string
 */
function ajax_get_list_output($value, $display, $add= "") {

	$add= $add != ""? "(" . $add . ")" : "";

	$output= "$value";
	$output.="|";

	$output.="<div style='display:table-row; width: 300px;'>";
	$output.="<div style='display:table-cell; width: 200px;'>$display</div>";
	$output.="<div style='display:table-cell; width: 100px;'>" . $add . "</div>";
	$output.="</div>";

	$output.="\n";

	return $output;
}
