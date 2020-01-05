<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
  *
 * @package sifit
 * @subpackage reports
 *
 * Tags CSS styles definition class
 *
 */

	include_once INC_DIR . "/reports/tags/tag_element.class.php";
	include_once INC_DIR . "/forms/field_types/basic.inc.php";

	class tag_html_script extends tag_element {

		protected $show_connection= false;

/*		protected function change_field_properties(&$field) {
			$field->reference= new ftext();
		}*/

		public function get_value() {

			$value= parent::get_value();
			return '<script src="'. $value .'"></script>';
		}
	}
