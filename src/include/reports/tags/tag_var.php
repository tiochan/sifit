<?php

/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 *
 * @package sifit
 * @subpackage reports
 *
 * Tags definition class
 *
 */

include_once INC_DIR . "/reports/tags/tag_element.class.php";
global $GLOBAL_REPORT_VARS;

class tag_var extends tag_element
{
	// Connection must be available for operation
	protected $var_name = "";

	public function get_value()
	{

		$var_name = $this->get_parameter("NAME");

		$this->replace_parameters();
		$val = stripslashes($this->value);

		if (!isset($GLOBAL_REPORT_VARS[$var_name])) {
			echo " * Error var " . $var_name . " does not exists * ";
			return false;
		}

		return $GLOBAL_REPORT_VARS[$var_name];
	}
}

if (!isset($GLOBAL_REPORT_VARS)) $GLOBAL_REPORT_VARS = array();
