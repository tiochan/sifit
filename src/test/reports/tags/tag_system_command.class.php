<?php
/**
 * @author Jorge Novoa (jorge.novoa@upcnet.es)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package sigvi
 * @subpackage reports
 *
 * Tags definition class
 *
 */

	include_once INC_DIR . "/reports/tags/tag_element.class.php";


	class tag_system_command extends tag_element {

		protected $show_connection= false;


		public function get_value() {

			parent::get_value();

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
?>
