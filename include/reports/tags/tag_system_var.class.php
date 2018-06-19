<?php
/**
 * @author Jorge Novoa (jorge.novoa@upcnet.es)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package sifit
 * @subpackage reports
 *
 * Tags definition class
 *
 */

	include_once INC_DIR . "/reports/tags/tag_element.class.php";


	class tag_system_var extends tag_element {

		protected $show_connection= false;


		public function get_value() {

			$this->replace_parameters();

			$var= $this->value;

			if(array_key_exists($var, $GLOBALS)) {
				return $GLOBALS[$var];
			} else {
				return "** Error: Can't find the var " . $this->value . " **";
			}
		}
	}
