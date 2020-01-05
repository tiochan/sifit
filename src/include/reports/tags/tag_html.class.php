<?php
/**
 * @author Jorge Novoa (jorge.novoa@upcnet.es)
 * @package sifit
 * @subpackage reports
 *
 * Tags definition class
 *
 */

	include_once INC_DIR . "/reports/tags/tag_element.class.php";
	include_once INC_DIR . "/forms/field_types/listbox.inc.php";


	class tag_html extends tag_element {

		protected $show_connection= false;

		protected function change_field_properties(&$field) {
			$field->reference= new html();
		}
	}
