<?php
/**
 * @author Sebastian Gomez (tiochan@gmail.com)
 * For: Politechnical University of Catalonia (UPC), Spain.
 *
 * @package lib
 * @subpackage forms
 *
 */

	include_once SYSHOME . "/include/forms/form_elements.inc.php";


	/**
	 * Creates a selection object (list box)
	 *
	 */
	class select extends form_element {

		public $text;
		public $values;
		public $def_value;
		public $value;

		/**
		 * Constructor:
		 *
		 * @param string $name
		 * @param array $values
		 * @param string $default_value
		 * @return select
		 */
		public function select($name, $text, $values, $default_value="") {
			parent::form_element($name);
			$this->text= $text;
			$this->values= $values;
			$this->def_value= $default_value;

			$this->value= get_http_param($this->doc_name);
		}

		public function show() {
			parent::show();

			if(!$this->visible) return;

			echo "$this->text&nbsp;<select class='action' name='$this->doc_name'>\n";
			foreach ($this->values as $key => $value) {
				if($key === $this->def_value) {
					echo "<option value='$key' selected>$value</option>\n";
				} else {
					echo "<option value='$key'>$value</option>\n";
				}
			}
			echo "</select>";
		}
	}
?>