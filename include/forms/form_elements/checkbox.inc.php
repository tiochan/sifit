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
	 * Creates a checkbox
	 *
	 */
	class checkbox extends form_element {

		var $text;

		/**
		 * Constructor:
		 *
		 * @param string $name
		 * @param string $text
		 * @return checkbox
		 */
		function checkbox($name, $text) {
			parent::form_element($name);
			$this->text= $text;
		}

		function show() {
			parent::show();

			if(!$this->visible) return;

			echo "$this->text&nbsp;<input type='checkbox' class='action' name='$this->doc_name'>";
		}
	}

?>