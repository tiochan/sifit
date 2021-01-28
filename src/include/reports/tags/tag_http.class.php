<?php

/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * @package sifit
 * @subpackage reports
 *
 * Tags definition class
 *
 */

include_once INC_DIR . "/reports/tags/tag_element.class.php";
include_once INC_DIR . "/forms/field_types/listbox.inc.php";


class tag_http extends tag_element
{

	protected $show_connection = false;

	public function get_value()
	{

		$this->replace_parameters();

		$URL = $this->value;

		$ret = file_get_contents($URL);

		return $ret;
	}

	protected function change_field_properties(&$field)
	{
		$field->reference = new fstring();
		$field->reference->size = 100;
		$field->alias = "URL";
	}
}
