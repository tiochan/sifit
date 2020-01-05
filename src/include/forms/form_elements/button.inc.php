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
	class button extends form_element {
		public $text;
		public $image;

		/**
		 * button constructor
		 *
		 * @param string $name, internal button name
		 * @param string $text, the text to display
		 * @return button
		 */
		function button($name, $text, $image="") {
			parent::form_element($name);
			$this->text= $text;
			$this->image= $image;
		}

		function show() {
			parent::show();

			if(!$this->visible) return;

			if($this->image=="") {
				echo "<input class='action' type=button" .
					 " name='$this->doc_name' " .
					 " value='$this->text'" .
					 " onclick='document.forms.". $this->form_name. ".element.value=\"". $this->doc_name ."\";".
					 		   "document.forms.". $this->form_name. ".event.value=\"clicked\";".
					 		   "document.forms.". $this->form_name. ".submit()'>\n";
			} else {
				echo "<img src='" . $this->image . "'" .
					 " alt='" . $this->text . "' " .
					 " title='" . $this->text . "' " .
					 " align='absmiddle' " .
					 " onclick='document.forms.". $this->form_name. ".element.value=\"". $this->doc_name ."\";".
					 		   "document.forms.". $this->form_name. ".event.value=\"clicked\";".
					 		   "document.forms.". $this->form_name. ".submit()'>\n";
			}
		}

		function event($event_type) {
			return $this->clicked();
		}

		// Abstract
		function clicked() {
			return 0;
		}
	}
?>