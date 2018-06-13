<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage forms
 *
 */
	require_once SYSHOME . "/include/forms/forms.inc.php";

	/**
	 * Generic method definition for form elements that uses fields.
	 *
	 */
	class common_field_elements extends form_element {

		public $fields;				// Array of class field
		public $identifier;
		public $allow_save_and_continue= false;

		public function common_field_elements($doc_name, $fields) {

			parent::form_element($doc_name);
			$this->fields= $fields;
		}


		public function show() {
			parent::show();

			if(!$this->visible) return;
		}

		/**
		 * PROTECTED
		 *
		 * - For every field get its value in the _POST array.
		 *
		 * @return array $values: Defined previously as array, passed by reference.
		 */
		protected function get_values() {
			global $_POST;

			$values= array();

			for($i=0; $i<count($this->fields); $i++) {

				$tmp_field= $this->fields[$i];

				if(($val= $tmp_field->get_value_from_post()) !== false) {
					$values[$tmp_field->name] = $val;
				}
			}
			return $values;
		}

		/**
		 * PROTECTED
		 *
		 * Fill the fields of the form using the array.
		 * The array must be in the form key=>value, where key is the name of the field.
		 *
		 * @param array $values
		 */
		protected function set_form_values($values) {
			foreach ($values as $key => $value) {
				html_set_field_value($this->form_name, $key, $value);
			}
		}

		/**
		 * Get the values from $_POST and set them to current form fields.
		 *
		 * - Fill form fields with its values defined from last call (previous form)
		 *
		 */
		protected function recover_values_from_call() {

			$values= $this->get_values();
			$this->set_form_values($values);
		}		
	}
