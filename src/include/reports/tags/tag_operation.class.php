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


	class tag_operation extends tag_element {
		// Connection must be available for operation
		//protected $show_connection= false;

		public function get_value() {

			$this->replace_parameters();

			$val= stripslashes($this->value);

			// eval will return null unless the command itself returns something
			if(strpos($val, "return") === false) {
				$command= "return (" . $val . ");";
			} else {
				$command= $val;
			}

			return eval($command);
		}
	}
