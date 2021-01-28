<?php

/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage forms
 *
 */


require_once "basic.inc.php";
require_once INC_DIR . "/lookups/lookup.inc.php";

class field_lookup extends field_type
{

	public $lookup_url;
	public $parameters;

	public function field_lookup($lookup_url, $default_value = "")
	{
		parent::field_type($default_value);

		$this->lookup_url = $lookup_url;
		$this->parameters = array();
	}

	public function show($field_name, $readonly)
	{

		global $MESSAGES;

		if ($readonly) {
			echo "<input type='hidden' name='$field_name'>";
			echo "<input name='$field_name' readonly='1'>";
		} else {

			include_once SYSHOME . "/include/lookups/lookup.inc.php";

			$parameters = "";
			if (count($this->parameters) > 0) {
				foreach ($this->parameters as $name => $value) $parameters .= "&" . $name . "=" . $value;
			}

			echo "<input type='hidden' name='$field_name'>";
			echo "<input type='text' name='description_$field_name'>";
			$onclick = "lookup_function(\"" . $this->lookup_url . "\",\"$field_name\",\"$parameters\");";
			$title = $MESSAGES["SEARCH"];
			if (FE_SHOW_ICONS and defined("FE_LOOKUP_ICON")) {
				echo "&nbsp;<img src='" . FE_LOOKUP_ICON . "' alt='$title' title='$title' align='absmiddle' onclick='$onclick'>";
			} else {
				echo "<input type='button' class='action' title='$title' value='...' onclick='$onclick'>\n";
			}
		}
	}

	public function add_parameter($param_name, $value)
	{
		$this->parameters[$param_name] = $value;
	}
}
