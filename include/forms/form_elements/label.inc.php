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
	 * Creates a button
	 *
	 */
	class label extends form_element {
		public $text;

		/**
		 * label constructor
		 *
		 * @param string $name, internal button name
		 * @param string $text, the text to display
		 * @return button
		 */
		public function label($name, $text) {
			parent::form_element($name);
			$this->text= $text;
		}

		public function show() {
			parent::show();
			if(!$this->visible) return;

			echo $this->text;
		}
	}

	class label_info extends form_element {
		public $text;

		public function label_info($name, $text) {
			parent::form_element($name);
			$this->text= $text;
		}

		public function show() {
			parent::show();
			if(!$this->visible) return;

			html_showInfo($this->text);
		}
	}

	class separator extends label {

		public function separator() {
			$s_name= rand(10000);
			parent::label($s_name,"&nbsp;");
		}
	}
?>