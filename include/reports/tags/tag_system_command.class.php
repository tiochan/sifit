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


class tag_system_command extends tag_element {

	protected $show_connection= false;


	public function get_value() {

		$this->replace_parameters();

		$var= $this->value;

		return `$var`;
		/*
		if(file_exists($var) and is_executable($var)) {
			return `$var`;
		} else {
			return "** Error: File " . $this->value . " does not exists or is not executable **";
		}*/
	}

	protected function change_field_properties(&$field) {
		$field->alias="System command";
	}
}
