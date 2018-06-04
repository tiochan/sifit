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


	class tag_php_code extends tag_element {
		// Connection must be available for operation
		//protected $show_connection= false;

		public function get_value() {

			parent::get_value();

			$val= stripslashes($this->value);

			ob_start();
			eval($val);
			$result = ob_get_contents();
			ob_end_clean();
			return $result;
		}

		protected function change_field_properties(&$field) {
			$field->alias="PHP Code";
		}
	}
?>
